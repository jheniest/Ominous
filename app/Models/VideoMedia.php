<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoMedia extends Model
{
    use HasFactory;

    protected $table = 'video_media';

    protected $fillable = [
        'video_id',
        'type',
        'file_path',
        'url',
        'order',
        'mime_type',
        'file_size',
        'duration',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'order' => 'integer',
        'file_size' => 'integer',
        'duration' => 'integer',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }
}
