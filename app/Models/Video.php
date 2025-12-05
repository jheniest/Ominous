<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'subtitle',
        'description',
        'summary',
        'source',
        'location',
        'incident_date',
        'video_url',
        'thumbnail_url',
        'category',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by_user_id',
        'edited_by_user_id',
        'edited_at',
        'views_count',
        'likes_count',
        'comments_count',
        'is_featured',
        'is_sensitive',
        'is_nsfw',
        'is_members_only',
        'is_updating',
        'updating_since',
        'last_updated_by_user_id',
        'updates_closed_at',
        'media_token',
        'media_token_expires',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'edited_at' => 'datetime',
            'incident_date' => 'datetime',
            'media_token_expires' => 'datetime',
            'updating_since' => 'datetime',
            'updates_closed_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_sensitive' => 'boolean',
            'is_nsfw' => 'boolean',
            'is_members_only' => 'boolean',
            'is_updating' => 'boolean',
        ];
    }

    /**
     * Boot do modelo - gera slug automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($video) {
            if (empty($video->slug)) {
                $video->slug = static::generateUniqueSlug($video->title);
            }
        });

        static::updating(function ($video) {
            if ($video->isDirty('title') && !$video->isDirty('slug')) {
                $video->slug = static::generateUniqueSlug($video->title, $video->id);
            }
        });
    }

    /**
     * Gera um slug único baseado no título
     */
    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($title);
        
        if (empty($baseSlug)) {
            $baseSlug = 'noticia';
        }
        
        $slug = $baseSlug;
        $counter = 1;
        
        $query = static::withTrashed()->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            
            $query = static::withTrashed()->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }
        
        return $slug;
    }

    /**
     * Route model binding por slug
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Gera um token temporário para streaming de mídia
     */
    public function generateMediaToken(int $validMinutes = 30): string
    {
        $token = Str::random(64);
        
        $this->update([
            'media_token' => hash('sha256', $token),
            'media_token_expires' => now()->addMinutes($validMinutes),
        ]);
        
        return $token;
    }

    /**
     * Valida se um token de mídia é válido
     */
    public function validateMediaToken(string $token): bool
    {
        if (!$this->media_token || !$this->media_token_expires) {
            return false;
        }
        
        if ($this->media_token_expires->isPast()) {
            return false;
        }
        
        return hash_equals($this->media_token, hash('sha256', $token));
    }

    /**
     * Verifica se o conteúdo requer autenticação
     */
    public function requiresAuth(): bool
    {
        return $this->is_sensitive;
    }

    /**
     * Retorna o resumo ou uma versão truncada da descrição
     */
    public function getExcerpt(int $length = 150): string
    {
        if (!empty($this->summary)) {
            return Str::limit($this->summary, $length);
        }
        
        return Str::limit($this->description ?? '', $length);
    }

    /**
     * Retorna URL amigável para a notícia
     */
    public function getNewsUrl(): string
    {
        return route('news.show', $this->slug);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'edited_by_user_id');
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

    /**
     * Relacionamento com as atualizações da notícia
     */
    public function updates()
    {
        return $this->hasMany(VideoUpdate::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relacionamento com o usuário que fez a última atualização
     */
    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by_user_id');
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

    public function scopeSensitive($query)
    {
        return $query->where('is_sensitive', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_sensitive', false);
    }

    public function scopeMembersOnly($query)
    {
        return $query->where('is_members_only', true);
    }

    public function scopeUpdating($query)
    {
        return $query->where('is_updating', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
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
