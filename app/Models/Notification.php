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
            'comment_approved' => '✓',
            'comment_hidden' => '🚫',
            'account_suspended' => '⚠',
            'account_unsuspended' => '✓',
            default => '🔔',
        };
    }

    public function getColorClassAttribute(): string
    {
        return match($this->type) {
            'video_approved', 'comment_approved', 'account_unsuspended' => 'text-green-500',
            'video_rejected', 'video_hidden', 'comment_hidden', 'account_suspended' => 'text-red-500',
            default => 'text-blue-500',
        };
    }
}
