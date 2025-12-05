<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class VideoUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'user_id',
        'headline',
        'subheadline',
        'order',
    ];

    /**
     * Tags HTML permitidas no conteúdo
     */
    protected static array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'mark', 'span',
        'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'a', 'blockquote'
    ];

    /**
     * Sanitiza o HTML permitindo apenas tags seguras
     */
    public static function sanitizeHtml(?string $content): ?string
    {
        if (!$content) {
            return null;
        }

        // Remove scripts e eventos JS
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        $content = preg_replace('/\bon\w+\s*=\s*["\'][^"\']*["\']/i', '', $content);
        $content = preg_replace('/\bon\w+\s*=\s*[^\s>]*/i', '', $content);
        
        // Permite apenas tags seguras
        $allowedTagsString = '<' . implode('><', self::$allowedTags) . '>';
        $content = strip_tags($content, $allowedTagsString);
        
        // Adiciona rel="nofollow noopener" em links externos
        $content = preg_replace_callback(
            '/<a\s+([^>]*href=["\']https?:\/\/[^"\']*["\'][^>]*)>/i',
            function ($matches) {
                $attrs = $matches[1];
                if (strpos($attrs, 'rel=') === false) {
                    return '<a ' . $attrs . ' rel="nofollow noopener" target="_blank">';
                }
                return $matches[0];
            },
            $content
        );

        return $content;
    }

    /**
     * Accessor para subheadline sanitizado
     */
    protected function safeSubheadline(): Attribute
    {
        return Attribute::make(
            get: fn () => self::sanitizeHtml($this->subheadline)
        );
    }

    /**
     * Relacionamento com o vídeo/notícia
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Relacionamento com o usuário que criou a atualização
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com as mídias da atualização
     */
    public function media()
    {
        return $this->hasMany(VideoUpdateMedia::class)->orderBy('order');
    }
}
