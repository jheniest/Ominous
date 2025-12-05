<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('video_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('headline');
            $table->text('subheadline')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Tabela para mídias das atualizações
        Schema::create('video_update_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_update_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'image' ou 'video'
            $table->string('url');
            $table->string('thumbnail_url')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Adicionar campos para controle de fechamento da notícia
        Schema::table('videos', function (Blueprint $table) {
            $table->foreignId('last_updated_by_user_id')->nullable()->after('updating_since')->constrained('users')->nullOnDelete();
            $table->timestamp('updates_closed_at')->nullable()->after('last_updated_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropForeign(['last_updated_by_user_id']);
            $table->dropColumn(['last_updated_by_user_id', 'updates_closed_at']);
        });

        Schema::dropIfExists('video_update_media');
        Schema::dropIfExists('video_updates');
    }
};
