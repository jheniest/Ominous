<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('title');
            $table->text('summary')->nullable()->after('description'); // Resumo curto para preview
            $table->string('source')->nullable()->after('summary'); // Fonte da notícia
            $table->string('location')->nullable()->after('source'); // Local do acontecimento
            $table->timestamp('incident_date')->nullable()->after('location'); // Data do incidente
            $table->boolean('is_sensitive')->default(true)->after('is_featured'); // Conteúdo sensível (paywall)
            $table->string('media_token')->nullable()->after('video_path'); // Token para streaming
            $table->timestamp('media_token_expires')->nullable()->after('media_token');
            
            // Índices para performance
            $table->index('slug');
            $table->index('is_sensitive');
            $table->index('incident_date');
        });

        // Gerar slugs para vídeos existentes
        $videos = \DB::table('videos')->get();
        foreach ($videos as $video) {
            $baseSlug = Str::slug($video->title);
            $slug = $baseSlug ?: 'video-' . $video->id;
            $counter = 1;
            
            // Garantir slug único
            while (\DB::table('videos')->where('slug', $slug)->where('id', '!=', $video->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            \DB::table('videos')->where('id', $video->id)->update(['slug' => $slug]);
        }

        // Tornar slug obrigatório após preencher existentes
        Schema::table('videos', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['is_sensitive']);
            $table->dropIndex(['incident_date']);
            
            $table->dropColumn([
                'slug',
                'summary',
                'source',
                'location',
                'incident_date',
                'is_sensitive',
                'media_token',
                'media_token_expires',
            ]);
        });
    }
};
