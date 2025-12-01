<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'related_video_id',
        'related_comment_id',
        'action_by_user_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedVideo(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'related_video_id');
    }

    public function relatedComment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'related_comment_id');
    }

    public function actionByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by_user_id');
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'video_approved' => '✓',
            'video_rejected' => '✗',
            'video_hidden' => '🚫',
            'video_edited' => '✏️',
            'comment_approved' => '✓',
            'comment_hidden' => '🚫',
            'account_suspended' => '⚠',
            'account_unsuspended' => '✓',
            'admin_message' => '📢',
            default => '🔔',
        };
    }

    public function getColorClassAttribute(): string
    {
        return match($this->type) {
            'video_approved', 'comment_approved', 'account_unsuspended' => 'text-green-500',
            'video_rejected', 'video_hidden', 'comment_hidden', 'account_suspended' => 'text-red-500',
            'admin_message' => 'text-yellow-500',
            'video_edited' => 'text-blue-500',
            default => 'text-blue-500',
        };
    }

    /**
     * Send admin notification to a specific user or all users.
     */
    public static function sendAdminMessage(string $title, string $message, ?int $userId = null, ?int $adminId = null): int
    {
        $count = 0;
        
        if ($userId) {
            // Send to specific user
            static::create([
                'user_id' => $userId,
                'type' => 'admin_message',
                'title' => $title,
                'message' => $message,
                'action_by_user_id' => $adminId,
            ]);
            $count = 1;
        } else {
            // Send to all users
            $users = \App\Models\User::where('is_suspended', false)->get();
            foreach ($users as $user) {
                static::create([
                    'user_id' => $user->id,
                    'type' => 'admin_message',
                    'title' => $title,
                    'message' => $message,
                    'action_by_user_id' => $adminId,
                ]);
                $count++;
            }
        }
        
        return $count;
    }
}
