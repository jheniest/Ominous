<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoModerationController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::with(['user', 'approvedBy'])
            ->withCount(['comments', 'reports']);

        // Status filter
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $videos = $query->latest()->paginate(20);

        $stats = [
            'pending' => Video::where('status', 'pending')->count(),
            'approved' => Video::where('status', 'approved')->count(),
            'rejected' => Video::where('status', 'rejected')->count(),
            'hidden' => Video::where('status', 'hidden')->count(),
        ];

        return view('admin.videos.index', compact('videos', 'stats', 'status'));
    }

    public function approve(Video $video)
    {
        $video->approve(Auth::user());

        return back()->with('success', 'Video approved successfully.');
    }

    public function reject(Request $request, Video $video)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $video->reject($validated['reason']);

        return back()->with('success', 'Video rejected.');
    }

    public function hide(Video $video)
    {
        $video->update(['status' => 'hidden']);

        return back()->with('success', 'Video hidden from public view.');
    }

    public function toggleFeatured(Video $video)
    {
        $video->update(['is_featured' => !$video->is_featured]);

        $message = $video->is_featured 
            ? 'Video featured successfully.' 
            : 'Video removed from featured.';

        return back()->with('success', $message);
    }

    public function reports(Request $request)
    {
        $query = VideoReport::with(['video', 'user', 'reviewedBy']);

        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(20);

        $stats = [
            'pending' => VideoReport::where('status', 'pending')->count(),
            'reviewed' => VideoReport::where('status', 'reviewed')->count(),
            'dismissed' => VideoReport::where('status', 'dismissed')->count(),
        ];

        return view('admin.videos.reports', compact('reports', 'stats', 'status'));
    }

    public function reviewReport(Request $request, VideoReport $report)
    {
        $validated = $request->validate([
            'action' => 'required|in:reviewed,dismissed',
        ]);

        $report->update([
            'status' => $validated['action'],
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Report ' . $validated['action'] . ' successfully.');
    }

    public function destroy(Video $video)
    {
        $video->delete();

        return back()->with('success', 'Video deleted successfully.');
    }
}
