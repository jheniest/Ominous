<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'subtitle',
        'description',
        'video_url',
        'thumbnail_url',
        'category',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by_user_id',
        'views_count',
        'likes_count',
        'comments_count',
        'is_featured',
        'is_nsfw',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_nsfw' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reports()
    {
        return $this->hasMany(VideoReport::class);
    }

    public function media()
    {
        return $this->hasMany(VideoMedia::class)->orderBy('order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'video_tag');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function approve(User $admin)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by_user_id' => $admin->id,
        ]);
    }

    public function reject(string $reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }
}
