<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoReport;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoModerationController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::with(['user', 'approvedBy', 'editedBy'])
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

    public function edit(int $id)
    {
        $video = Video::with(['user', 'media', 'tags'])->findOrFail($id);
        
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, int $id)
    {
        $video = Video::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'summary' => 'nullable|string|max:500',
            'source' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'category' => 'required|string|in:guerra,terrorismo,chacina,massacre,suicidio,tribunal-do-crime,homicidio,assalto,sequestro,tiroteio,acidentes,desastres,operacao-policial,faccoes,conflitos,execucoes',
            'is_members_only' => 'boolean',
            'is_sensitive' => 'boolean',
            'is_nsfw' => 'boolean',
            'is_updating' => 'boolean',
        ]);
        
        // Tratar checkboxes não marcados
        $validated['is_members_only'] = $request->has('is_members_only');
        $validated['is_sensitive'] = $request->has('is_sensitive');
        $validated['is_nsfw'] = $request->has('is_nsfw');
        
        // Tratar flag de atualização
        $wasUpdating = $video->is_updating;
        $validated['is_updating'] = $request->has('is_updating');
        
        // Se está marcando como "em atualização" agora, registrar o timestamp
        if ($validated['is_updating'] && !$wasUpdating) {
            $validated['updating_since'] = now();
        }
        // Se está desmarcando, limpar o timestamp
        if (!$validated['is_updating'] && $wasUpdating) {
            $validated['updating_since'] = null;
        }
        
        // Registrar quem editou
        $validated['edited_by_user_id'] = Auth::id();
        $validated['edited_at'] = now();
        
        $video->update($validated);
        
        // Log da atividade
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'video_edited_by_admin',
            'description' => 'Editou a notícia: ' . $video->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        // Notificar o autor original
        if ($video->user_id !== Auth::id()) {
            Notification::create([
                'user_id' => $video->user_id,
                'type' => 'video_edited',
                'title' => 'Notícia Editada',
                'message' => 'Sua notícia "' . $video->title . '" foi editada pela moderação.',
                'related_video_id' => $video->id,
                'action_by_user_id' => Auth::id(),
            ]);
        }
        
        return redirect()->route('admin.videos.index')->with('success', 'Notícia atualizada com sucesso.');
    }

    public function approve(Request $request, int $id)
    {
        $video = Video::findOrFail($id);
        
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $video->approve(Auth::user());

        // Criar notificação para o autor do vídeo
        Notification::create([
            'user_id' => $video->user_id,
            'type' => 'video_approved',
            'title' => 'Vídeo Aprovado',
            'message' => $request->note ?? 'Seu vídeo "' . $video->title . '" foi aprovado e agora está visível para todos.',
            'related_video_id' => $video->id,
            'action_by_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Vídeo aprovado e notificação enviada.');
    }

    public function reject(Request $request, int $id)
    {
        $video = Video::findOrFail($id);
        
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'note' => 'nullable|string|max:500',
        ]);

        $video->reject($validated['reason']);

        // Criar notificação para o autor do vídeo
        Notification::create([
            'user_id' => $video->user_id,
            'type' => 'video_rejected',
            'title' => 'Vídeo Recusado',
            'message' => $validated['note'] ?? $validated['reason'],
            'related_video_id' => $video->id,
            'action_by_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Vídeo recusado e notificação enviada.');
    }

    public function hide(Request $request, int $id)
    {
        $video = Video::findOrFail($id);
        
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $video->update(['status' => 'hidden']);

        // Criar notificação para o autor do vídeo
        Notification::create([
            'user_id' => $video->user_id,
            'type' => 'video_hidden',
            'title' => 'Vídeo Ocultado',
            'message' => $request->note ?? 'Seu vídeo "' . $video->title . '" foi ocultado pela moderação.',
            'related_video_id' => $video->id,
            'action_by_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Vídeo ocultado e notificação enviada.');
    }

    public function toggleFeatured(int $id)
    {
        $video = Video::findOrFail($id);
        
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

    public function destroy(int $id)
    {
        $video = Video::findOrFail($id);
        
        $video->delete();

        return back()->with('success', 'Video deleted successfully.');
    }
}
