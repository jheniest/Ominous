<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\VideoReport;
use App\Models\SiteSetting;
use App\Services\VideoMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function create()
    {
        // Security Check 1: Verify authenticated user
        if (!Auth::check()) {
            abort(401, 'Não autenticado');
        }

        // Security Check 2: Check if user is suspended
        if (Auth::user()->is_suspended) {
            abort(403, 'Sua conta está suspensa');
        }

        // Security Check 3: Check if public uploads are enabled (cached)
        $publicUploadsEnabled = \Cache::remember('public_uploads_status', 300, function () {
            return SiteSetting::get('public_uploads_enabled', true);
        });

        if (!$publicUploadsEnabled && !Auth::user()->is_admin) {
            abort(403, 'Uploads públicos estão temporariamente desabilitados. Apenas administradores podem fazer upload.');
        }

        return view('news.create');
    }

    public function store(Request $request)
    {
        // Security Check 1: Verify authenticated user
        if (!Auth::check()) {
            abort(401, 'Não autenticado');
        }

        // Security Check 2: Check if user is suspended
        if (Auth::user()->is_suspended) {
            abort(403, 'Sua conta está suspensa');
        }

        // Security Check 3: Check if public uploads are enabled (cached)
        $publicUploadsEnabled = \Cache::remember('public_uploads_status', 300, function () {
            return SiteSetting::get('public_uploads_enabled', true);
        });

        if (!$publicUploadsEnabled && !Auth::user()->is_admin) {
            return back()->with('error', 'Uploads públicos estão temporariamente desabilitados. Apenas administradores podem fazer upload.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'required|string|max:5000',
            'media_files' => 'required|array|min:1|max:10',
            'media_files.*' => 'file|mimes:mp4,mov,avi,wmv,jpg,jpeg,png,gif,webp|max:102400', // 100MB per file
            'category' => 'required|in:guerra,terrorismo,chacina,massacre,suicidio,tribunal-do-crime,homicidio,assalto,sequestro,tiroteio,acidentes,desastres,operacao-policial,faccoes,conflitos,execucoes',
            'is_nsfw' => 'boolean',
            'is_members_only' => 'boolean',
            'tags' => 'nullable|string|max:255',
        ]);

        // Admin posts are auto-approved
        $status = Auth::user()->is_admin ? 'approved' : 'pending';

        // Only admins can set is_members_only
        $isMembersOnly = Auth::user()->is_admin ? ($validated['is_members_only'] ?? false) : false;

        $video = Auth::user()->videos()->create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'description' => $validated['description'],
            'video_url' => null, // Will be set by first media
            'thumbnail_url' => null, // Will be generated
            'category' => $validated['category'],
            'is_nsfw' => $validated['is_nsfw'] ?? false,
            'is_members_only' => $isMembersOnly,
            'status' => $status,
            'approved_by_user_id' => Auth::user()->is_admin ? Auth::id() : null,
            'approved_at' => Auth::user()->is_admin ? now() : null,
        ]);

        // Process media files
        $mediaService = new VideoMediaService();
        $mediaItems = $mediaService->processMediaFiles($video, $validated['media_files']);

        // Set video_url to first media item
        if (!empty($mediaItems) && isset($mediaItems[0]->url)) {
            $video->update(['video_url' => $mediaItems[0]->url]);
        } else {
            // Fallback: set to empty string if no media was processed
            $video->update(['video_url' => '']);
        }

        // Process tags
        if (!empty($validated['tags'])) {
            $this->processTags($video, $validated['tags']);
        }

        $message = Auth::user()->is_admin
            ? 'Conteúdo publicado com sucesso!' 
            : 'Conteúdo enviado para moderação. Você será notificado quando for revisado.';

        return redirect()->route('news.my-submissions')->with('success', $message);
    }

    public function storeComment(Request $request, Video $video)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Admin comments are auto-approved
        $status = Auth::user()->is_admin ? 'approved' : 'approved'; // All comments approved for now

        $comment = $video->comments()->create([
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
            'status' => $status,
        ]);

        return back()->with('success', 'Comentário publicado com sucesso.');
    }

    public function destroyComment(Comment $comment)
    {
        // Only comment owner or admin can delete
        if ($comment->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Ação não autorizada.');
        }

        // Delete all replies too
        $comment->replies()->delete();
        $comment->delete();

        return back()->with('success', 'Comentário excluído com sucesso.');
    }

    public function report(Request $request, Video $video)
    {
        $validated = $request->validate([
            'reason' => 'required|in:inappropriate,fake,spam,violence,copyright,other',
            'description' => 'nullable|string|max:500',
        ]);

        // Check if user already reported this video
        $existing = VideoReport::where('video_id', $video->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already reported this video.');
        }

        VideoReport::create([
            'video_id' => $video->id,
            'user_id' => Auth::id(),
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Report submitted. We will review it shortly.');
    }

    public function myVideos()
    {
        $videos = Auth::user()->videos()
            ->withCount('comments')
            ->latest()
            ->paginate(12);

        return view('news.my-submissions', compact('videos'));
    }

    public function edit(Video $video)
    {
        // Only owner or admin can edit
        if ($video->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $video->load('tags');

        return view('news.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        // Only owner or admin can update
        if ($video->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'required|string|max:5000',
            'thumbnail_url' => 'nullable|url|max:500',
            'category' => 'required|in:guerra,terrorismo,chacina,massacre,suicidio,tribunal-do-crime,homicidio,assalto,sequestro,tiroteio,acidentes,desastres,operacao-policial,faccoes,conflitos,execucoes',
            'is_nsfw' => 'boolean',
            'tags' => 'nullable|string|max:255',
        ]);

        $video->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'description' => $validated['description'],
            'thumbnail_url' => $validated['thumbnail_url'] ?? $video->thumbnail_url,
            'category' => $validated['category'],
            'is_nsfw' => $validated['is_nsfw'] ?? false,
        ]);

        // Process tags
        if (isset($validated['tags'])) {
            $this->processTags($video, $validated['tags']);
        }

        return redirect()->route('news.show', $video)->with('success', 'Vídeo atualizado com sucesso.');
    }

    public function destroy(Video $video)
    {
        // Only owner or admin can delete
        if ($video->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        // Delete all media files
        $mediaService = new VideoMediaService();
        $mediaService->deleteVideoMedia($video);

        // Delete old video file if exists (backward compatibility)
        if ($video->video_url && str_contains($video->video_url, 'storage/videos/')) {
            $path = str_replace(asset('storage/'), '', $video->video_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $video->delete();

        return redirect()->route('news.my-submissions')->with('success', 'Conteúdo deletado com sucesso.');
    }

    /**
     * Process tags from comma-separated string
     */
    private function processTags(Video $video, string $tagsString)
    {
        $tagNames = array_map('trim', explode(',', $tagsString));
        $tagNames = array_filter($tagNames); // Remove empty values

        $tagIds = [];
        foreach ($tagNames as $tagName) {
            if (empty($tagName)) continue;

            // Find or create tag
            $tag = Tag::firstOrCreate(
                ['name' => $tagName],
                ['color' => $this->getRandomTagColor()]
            );

            $tagIds[] = $tag->id;
        }

        // Sync tags with video
        $video->tags()->sync($tagIds);
    }

    /**
     * Get random color for new tags
     */
    private function getRandomTagColor()
    {
        $colors = [
            '#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#6366F1', 
            '#8B5CF6', '#EC4899', '#06B6D4', '#14B8A6', '#F97316'
        ];
        
        return $colors[array_rand($colors)];
    }
}
