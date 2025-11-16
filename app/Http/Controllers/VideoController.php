<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Comment;
use App\Models\VideoReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::approved()
            ->with('user')
            ->withCount(['comments', 'reports']);

        // Category filter
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'trending':
                $query->where('created_at', '>=', now()->subDays(7))
                      ->orderBy('views_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $videos = $query->paginate(12);
        $featured = Video::featured()->approved()->with('user')->take(3)->get();

        return view('videos.index', compact('videos', 'featured'));
    }

    public function show(Video $video)
    {
        // Only show approved videos to non-admins
        if (!Auth::user()->is_admin && $video->status !== 'approved') {
            abort(403, 'This video is not available.');
        }

        $video->load('user', 'approvedBy');
        $video->incrementViews();

        // Get top-level comments with replies
        $comments = $video->comments()
            ->approved()
            ->topLevel()
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(20);

        // Related videos (same category)
        $related = Video::approved()
            ->where('category', $video->category)
            ->where('id', '!=', $video->id)
            ->with('user')
            ->take(6)
            ->get();

        return view('videos.show', compact('video', 'comments', 'related'));
    }

    public function create()
    {
        return view('videos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'video_url' => 'nullable|url|max:500',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:102400', // 100MB
            'thumbnail_url' => 'nullable|url|max:500',
            'category' => 'required|in:breaking_news,footage,investigation,accident,crime,natural_disaster,other',
            'is_nsfw' => 'boolean',
        ]);

        // Handle video file upload
        $videoUrl = $validated['video_url'] ?? null;
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('videos', 'public');
            $videoUrl = asset('storage/' . $path);
        }

        if (!$videoUrl) {
            return back()->withErrors(['video_url' => 'Você deve fornecer uma URL de vídeo ou fazer upload de um arquivo.']);
        }

        // Admin posts are auto-approved
        $status = Auth::user()->is_admin ? 'approved' : 'pending';

        $video = Auth::user()->videos()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'video_url' => $videoUrl,
            'thumbnail_url' => $validated['thumbnail_url'] ?? null,
            'category' => $validated['category'],
            'is_nsfw' => $validated['is_nsfw'] ?? false,
            'status' => $status,
            'approved_by_user_id' => Auth::user()->is_admin ? Auth::id() : null,
            'approved_at' => Auth::user()->is_admin ? now() : null,
        ]);

        $message = Auth::user()->is_admin 
            ? 'Vídeo publicado com sucesso!' 
            : 'Vídeo enviado para moderação. Você será notificado quando for revisado.';

        return redirect()->route('videos.my-videos')->with('success', $message);
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

        return back()->with('success', 'Comment posted successfully.');
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

        return view('videos.my-videos', compact('videos'));
    }

    public function edit(Video $video)
    {
        // Only owner or admin can edit
        if ($video->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        // Only owner or admin can update
        if ($video->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'thumbnail_url' => 'nullable|url|max:500',
            'category' => 'required|in:breaking_news,footage,investigation,accident,crime,natural_disaster,other',
            'is_nsfw' => 'boolean',
        ]);

        $video->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'thumbnail_url' => $validated['thumbnail_url'] ?? $video->thumbnail_url,
            'category' => $validated['category'],
            'is_nsfw' => $validated['is_nsfw'] ?? false,
        ]);

        return redirect()->route('videos.show', $video)->with('success', 'Vídeo atualizado com sucesso.');
    }

    public function destroy(Video $video)
    {
        // Only owner or admin can delete
        if ($video->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        // Delete video file if it's stored locally
        if ($video->video_url && str_contains($video->video_url, 'storage/videos/')) {
            $path = str_replace(asset('storage/'), '', $video->video_url);
            Storage::disk('public')->delete($path);
        }

        $video->delete();

        return redirect()->route('videos.my-videos')->with('success', 'Vídeo deletado com sucesso.');
    }
}
