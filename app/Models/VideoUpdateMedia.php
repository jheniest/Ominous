<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoUpdateMedia extends Model
{
    use HasFactory;

    protected $table = 'video_update_media';

    protected $fillable = [
        'video_update_id',
        'type',
        'url',
        'thumbnail_url',
        'order',
    ];

    /**
     * Relacionamento com a atualização
     */
    public function videoUpdate()
    {
        return $this->belongsTo(VideoUpdate::class, 'video_update_id');
    }
}
