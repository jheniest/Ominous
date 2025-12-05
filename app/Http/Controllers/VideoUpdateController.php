<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoUpdate;
use App\Models\VideoUpdateMedia;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VideoUpdateController extends Controller
{
    /**
     * Adicionar uma nova atualização à notícia
     */
    public function store(Request $request, Video $video)
    {
        // Verificar se é admin
        if (!Auth::user()->is_admin) {
            abort(403, 'Apenas administradores podem adicionar atualizações.');
        }

        // Verificar se a notícia está em modo de atualização
        if (!$video->is_updating) {
            return back()->with('error', 'Esta notícia não está em modo de atualização.');
        }

        $validated = $request->validate([
            'headline' => 'required|string|max:255',
            'subheadline' => 'nullable|string|max:1000',
            'media_files' => 'nullable|array|max:5',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov|max:102400',
        ]);

        // Calcular a ordem (próximo número)
        $maxOrder = $video->updates()->max('order') ?? 0;

        // Criar a atualização
        $update = $video->updates()->create([
            'user_id' => Auth::id(),
            'headline' => $validated['headline'],
            'subheadline' => $validated['subheadline'] ?? null,
            'order' => $maxOrder + 1,
        ]);

        // Processar mídias se enviadas
        if ($request->hasFile('media_files')) {
            $mediaOrder = 0;
            foreach ($request->file('media_files') as $file) {
                $mediaOrder++;
                
                // Determinar o tipo
                $mimeType = $file->getMimeType();
                $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
                
                // Salvar arquivo
                $path = $file->store('updates/' . $video->id, 'public');
                $url = Storage::url($path);
                
                // Gerar thumbnail se for vídeo (ou usar o próprio arquivo se for imagem)
                $thumbnailUrl = $type === 'image' ? $url : null;
                
                // Criar registro de mídia
                $update->media()->create([
                    'type' => $type,
                    'url' => $url,
                    'thumbnail_url' => $thumbnailUrl,
                    'order' => $mediaOrder,
                ]);
            }
        }

        // Atualizar timestamp de última atualização no vídeo
        $video->update([
            'last_updated_by_user_id' => Auth::id(),
            'updating_since' => now(),
        ]);

        // Log da atividade
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'video_update_added',
            'description' => 'Adicionou atualização à notícia: ' . $video->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Atualização adicionada com sucesso!');
    }

    /**
     * Fechar as atualizações da notícia
     */
    public function close(Request $request, Video $video)
    {
        // Verificar se é admin
        if (!Auth::user()->is_admin) {
            abort(403, 'Apenas administradores podem fechar atualizações.');
        }

        // Verificar se a notícia está em modo de atualização
        if (!$video->is_updating) {
            return back()->with('error', 'Esta notícia não está em modo de atualização.');
        }

        // Fechar as atualizações
        $video->update([
            'is_updating' => false,
            'updates_closed_at' => now(),
            'last_updated_by_user_id' => Auth::id(),
        ]);

        // Log da atividade
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'video_updates_closed',
            'description' => 'Fechou atualizações da notícia: ' . $video->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Atualizações encerradas com sucesso!');
    }

    /**
     * Reabrir atualizações da notícia
     */
    public function reopen(Request $request, Video $video)
    {
        // Verificar se é admin
        if (!Auth::user()->is_admin) {
            abort(403, 'Apenas administradores podem reabrir atualizações.');
        }

        // Reabrir as atualizações
        $video->update([
            'is_updating' => true,
            'updating_since' => now(),
            'updates_closed_at' => null,
        ]);

        // Log da atividade
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'video_updates_reopened',
            'description' => 'Reabriu atualizações da notícia: ' . $video->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Atualizações reabertas com sucesso!');
    }

    /**
     * Deletar uma atualização específica
     */
    public function destroy(Request $request, VideoUpdate $update)
    {
        // Verificar se é admin
        if (!Auth::user()->is_admin) {
            abort(403, 'Apenas administradores podem remover atualizações.');
        }

        $video = $update->video;

        // Deletar mídias associadas
        foreach ($update->media as $media) {
            // Remover arquivo físico
            $path = str_replace('/storage/', '', $media->url);
            Storage::disk('public')->delete($path);
        }

        // Deletar atualização
        $update->delete();

        // Log da atividade
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'video_update_deleted',
            'description' => 'Removeu atualização da notícia: ' . $video->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Atualização removida com sucesso!');
    }
}
