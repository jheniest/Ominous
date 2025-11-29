<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\RateLimiter;

class SecureMediaController extends Controller
{
    /**
     * Stream de mídia protegido com validação de token
     */
    public function stream(Request $request, string $token, int $mediaId)
    {
        // Rate limiting por IP
        $key = 'media_stream:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) { // 60 requests por minuto
            abort(429, 'Muitas requisições. Aguarde um momento.');
        }
        RateLimiter::hit($key, 60);
        
        // Buscar mídia
        $media = VideoMedia::with('video')->findOrFail($mediaId);
        $video = $media->video;
        
        // Validar token
        if (!$video->validateMediaToken($token)) {
            abort(403, 'Token inválido ou expirado.');
        }
        
        // Validar autenticação para conteúdo sensível
        if ($video->is_sensitive && !auth()->check()) {
            abort(403, 'Faça login para acessar este conteúdo.');
        }
        
        // Validar referer (proteção básica contra hotlinking)
        $referer = $request->header('Referer');
        $allowedHosts = [
            parse_url(config('app.url'), PHP_URL_HOST),
            'localhost',
            '127.0.0.1',
        ];
        
        if ($referer) {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            if (!in_array($refererHost, $allowedHosts)) {
                abort(403, 'Acesso não autorizado.');
            }
        }
        
        // Determinar caminho do arquivo
        $filePath = $media->file_path;
        
        // Verificar se arquivo existe
        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'Mídia não encontrada.');
        }
        
        return $this->streamFile($filePath, $media->mime_type, $request);
    }

    /**
     * Streaming de arquivo com suporte a range requests (para seek em vídeos)
     */
    protected function streamFile(string $path, string $mimeType, Request $request): StreamedResponse
    {
        $disk = Storage::disk('local');
        $fileSize = $disk->size($path);
        $fileName = basename($path);
        
        // Headers de segurança
        $headers = [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => 'inline',
            
            // Prevenção de cache
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            
            // Prevenção de download
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'",
            
            // Anti-embedding
            'X-Frame-Options' => 'SAMEORIGIN',
        ];
        
        $start = 0;
        $end = $fileSize - 1;
        $statusCode = 200;
        
        // Suporte a Range requests (para seek em vídeos)
        if ($request->hasHeader('Range')) {
            $range = $request->header('Range');
            
            if (preg_match('/bytes=(\d*)-(\d*)/', $range, $matches)) {
                $start = $matches[1] !== '' ? intval($matches[1]) : 0;
                $end = $matches[2] !== '' ? intval($matches[2]) : $fileSize - 1;
                
                // Validar range
                if ($start > $end || $start >= $fileSize || $end >= $fileSize) {
                    return response('', 416, [
                        'Content-Range' => "bytes */$fileSize"
                    ]);
                }
                
                $statusCode = 206;
                $headers['Content-Range'] = "bytes $start-$end/$fileSize";
            }
        }
        
        $headers['Content-Length'] = $end - $start + 1;
        
        return response()->stream(
            function () use ($disk, $path, $start, $end) {
                $stream = $disk->readStream($path);
                
                // Pular para posição inicial
                if ($start > 0) {
                    fseek($stream, $start);
                }
                
                $remaining = $end - $start + 1;
                $bufferSize = 8192; // 8KB chunks
                
                while ($remaining > 0 && !feof($stream)) {
                    $readSize = min($bufferSize, $remaining);
                    $data = fread($stream, $readSize);
                    
                    if ($data === false) {
                        break;
                    }
                    
                    echo $data;
                    flush();
                    
                    $remaining -= strlen($data);
                }
                
                fclose($stream);
            },
            $statusCode,
            $headers
        );
    }

    /**
     * Gera URL temporária para mídia
     */
    public function generateUrl(Request $request, Video $video)
    {
        // Verificar autenticação para conteúdo sensível
        if ($video->is_sensitive && !auth()->check()) {
            return response()->json([
                'error' => 'Faça login para acessar este conteúdo.',
                'require_auth' => true,
            ], 403);
        }
        
        // Rate limiting
        $key = 'media_url:' . ($request->user()->id ?? $request->ip());
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return response()->json([
                'error' => 'Muitas requisições. Aguarde um momento.',
            ], 429);
        }
        RateLimiter::hit($key, 60);
        
        // Gerar token
        $token = $video->generateMediaToken(15); // 15 minutos
        
        // Retornar URLs para cada mídia
        $mediaUrls = $video->media->map(function ($media) use ($token) {
            return [
                'id' => $media->id,
                'type' => $media->type,
                'url' => route('media.stream', ['token' => $token, 'mediaId' => $media->id]),
                'thumbnail' => $media->thumbnail_path 
                    ? route('media.thumbnail', ['token' => $token, 'mediaId' => $media->id])
                    : null,
            ];
        });
        
        return response()->json([
            'media' => $mediaUrls,
            'expires_in' => 15 * 60, // segundos
        ]);
    }

    /**
     * Thumbnail de mídia (menos protegido)
     */
    public function thumbnail(Request $request, string $token, int $mediaId)
    {
        $media = VideoMedia::with('video')->findOrFail($mediaId);
        
        // Validar token
        if (!$media->video->validateMediaToken($token)) {
            abort(403);
        }
        
        $thumbnailPath = $media->thumbnail_path;
        
        if (!$thumbnailPath || !Storage::disk('local')->exists($thumbnailPath)) {
            // Retornar placeholder
            abort(404);
        }
        
        return response()->file(
            Storage::disk('local')->path($thumbnailPath),
            [
                'Cache-Control' => 'private, max-age=3600',
                'Content-Type' => 'image/jpeg',
            ]
        );
    }
}
