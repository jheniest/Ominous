<?php

namespace App\Services;

use App\Models\Video;
use App\Models\VideoMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

class VideoMediaService
{
    /**
     * Process and store multiple media files
     */
    public function processMediaFiles(Video $video, array $files): array
    {
        $mediaItems = [];
        $hasVideo = false;
        $firstImage = null;

        foreach ($files as $index => $file) {
            $mediaItem = $this->storeMediaFile($video, $file, $index);
            $mediaItems[] = $mediaItem;

            if ($mediaItem->type === 'video') {
                $hasVideo = true;
            } elseif ($mediaItem->type === 'image' && !$firstImage) {
                $firstImage = $mediaItem;
            }
        }

        // Generate thumbnail
        $this->generateThumbnail($video, $mediaItems, $hasVideo, $firstImage);

        return $mediaItems;
    }

    /**
     * Store a single media file
     */
    private function storeMediaFile(Video $video, UploadedFile $file, int $order): VideoMedia
    {
        $type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
        $folder = $type === 'video' ? 'videos' : 'images';
        
        // Generate unique filename
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'public');
        
        $metadata = [
            'original_name' => $file->getClientOriginalName(),
        ];

        // Get image dimensions if it's an image
        if ($type === 'image') {
            try {
                $imagePath = storage_path('app/public/' . $path);
                if (file_exists($imagePath)) {
                    $imageSize = getimagesize($imagePath);
                    if ($imageSize) {
                        $metadata['width'] = $imageSize[0];
                        $metadata['height'] = $imageSize[1];
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return VideoMedia::create([
            'video_id' => $video->id,
            'type' => $type,
            'file_path' => $path,
            'url' => asset('storage/' . $path),
            'order' => $order,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Generate thumbnail for video
     */
    private function generateThumbnail(Video $video, array $mediaItems, bool $hasVideo, ?VideoMedia $firstImage): void
    {
        try {
            if ($hasVideo) {
                // Use first video to generate thumbnail
                $firstVideo = collect($mediaItems)->first(fn($m) => $m->type === 'video');
                if ($firstVideo) {
                    $this->extractVideoThumbnail($video, $firstVideo);
                }
            } elseif ($firstImage) {
                // Use first image as thumbnail
                $video->update([
                    'thumbnail_url' => $firstImage->url,
                ]);
            }
        } catch (\Exception $e) {
            // Fallback: use default or first media URL
            $firstMedia = $mediaItems[0] ?? null;
            if ($firstMedia) {
                $video->update([
                    'thumbnail_url' => $firstMedia->url,
                ]);
            }
        }
    }

    /**
     * Extract thumbnail from video using FFmpeg
     */
    private function extractVideoThumbnail(Video $video, VideoMedia $videoMedia): void
    {
        try {
            $videoPath = storage_path('app/public/' . $videoMedia->file_path);
            
            // Check if FFmpeg is available
            if (!$this->isFFmpegAvailable()) {
                // Fallback to video URL if FFmpeg is not available
                $video->update(['thumbnail_url' => $videoMedia->url]);
                return;
            }

            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe', // Adjust path for Windows
                'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
            ]);

            $videoFile = $ffmpeg->open($videoPath);
            
            // Extract frame at 2 seconds (or 10% of video duration)
            $thumbnailFilename = Str::random(40) . '.jpg';
            $thumbnailPath = 'thumbnails/' . $thumbnailFilename;
            $fullThumbnailPath = storage_path('app/public/' . $thumbnailPath);

            // Create thumbnails directory if it doesn't exist
            if (!file_exists(dirname($fullThumbnailPath))) {
                mkdir(dirname($fullThumbnailPath), 0755, true);
            }

            $frame = $videoFile->frame(TimeCode::fromSeconds(2));
            $frame->save($fullThumbnailPath);

            $video->update([
                'thumbnail_url' => asset('storage/' . $thumbnailPath),
            ]);
        } catch (\Exception $e) {
            // Fallback to video URL
            $video->update(['thumbnail_url' => $videoMedia->url]);
        }
    }

    /**
     * Check if FFmpeg is available
     */
    private function isFFmpegAvailable(): bool
    {
        try {
            // Try common FFmpeg locations on Windows
            $paths = [
                'C:/ffmpeg/bin/ffmpeg.exe',
                'C:/Program Files/ffmpeg/bin/ffmpeg.exe',
                'ffmpeg', // System PATH
            ];

            foreach ($paths as $path) {
                if (file_exists($path) || $this->commandExists('ffmpeg')) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if command exists in system PATH
     */
    private function commandExists(string $command): bool
    {
        $return = shell_exec(sprintf("where %s 2>NUL", escapeshellarg($command)));
        return !empty($return);
    }

    /**
     * Delete all media files for a video
     */
    public function deleteVideoMedia(Video $video): void
    {
        foreach ($video->media as $media) {
            // Delete file from storage
            if (Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }

            // Delete media record
            $media->delete();
        }

        // Delete thumbnail if it's stored separately
        if ($video->thumbnail_url && str_contains($video->thumbnail_url, 'storage/thumbnails/')) {
            $path = str_replace(asset('storage/'), '', $video->thumbnail_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
