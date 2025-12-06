<?php

namespace App\Services;

use App\Models\Video;
use App\Models\VideoMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoMediaService
{
    /**
     * Process and store multiple media files
     */
    public function processMediaFiles(Video $video, array $files): array
    {
        $mediaItems = [];
        $firstImage = null;

        foreach ($files as $index => $file) {
            $mediaItem = $this->storeMediaFile($video, $file, $index);
            $mediaItems[] = $mediaItem;

            // Capture first image for potential thumbnail
            if ($mediaItem->type === 'image' && !$firstImage) {
                $firstImage = $mediaItem;
            }
        }

        // Set thumbnail from first available media (image preferred)
        $this->setThumbnail($video, $mediaItems, $firstImage);

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
     * Set thumbnail for video (uses first image or first media as fallback)
     * Note: For videos, thumbnail should be uploaded manually via admin panel
     */
    private function setThumbnail(Video $video, array $mediaItems, ?VideoMedia $firstImage): void
    {
        // Skip if video already has a manually set thumbnail
        if ($video->thumbnail_url && !str_contains($video->thumbnail_url, 'storage/videos/')) {
            return;
        }

        // Prefer first image as thumbnail
        if ($firstImage) {
            $video->update([
                'thumbnail_url' => $firstImage->url,
            ]);
            return;
        }

        // Fallback to first media (even if video - user can update later)
        $firstMedia = $mediaItems[0] ?? null;
        if ($firstMedia) {
            $video->update([
                'thumbnail_url' => $firstMedia->url,
            ]);
        }
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

    /**
     * Update thumbnail manually
     */
    public function updateThumbnail(Video $video, UploadedFile $file): string
    {
        // Delete old thumbnail if exists in thumbnails folder
        if ($video->thumbnail_url && str_contains($video->thumbnail_url, 'storage/thumbnails/')) {
            $oldPath = str_replace(asset('storage/'), '', $video->thumbnail_url);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Store new thumbnail
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('thumbnails', $filename, 'public');
        $url = asset('storage/' . $path);

        $video->update(['thumbnail_url' => $url]);

        return $url;
    }
}
