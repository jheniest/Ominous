<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentModerationController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['video', 'user'])
            ->latest();

        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $comments = $query->paginate(30);

        $stats = [
            'approved' => Comment::where('status', 'approved')->count(),
            'pending' => Comment::where('status', 'pending')->count(),
            'hidden' => Comment::where('status', 'hidden')->count(),
        ];

        return view('admin.comments.index', compact('comments', 'stats', 'status'));
    }

    public function hide(Comment $comment)
    {
        $comment->update(['status' => 'hidden']);

        return back()->with('success', 'Comment hidden successfully.');
    }

    public function approve(Comment $comment)
    {
        $comment->update(['status' => 'approved']);

        return back()->with('success', 'Comment approved successfully.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }
}
