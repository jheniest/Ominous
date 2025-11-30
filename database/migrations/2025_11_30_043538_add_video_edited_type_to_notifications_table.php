<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite não suporta ALTER COLUMN, então precisamos recriar
        // Primeiro, criar tabela temporária
        Schema::create('notifications_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', [
                'video_approved', 
                'video_rejected', 
                'video_hidden', 
                'video_edited',
                'comment_approved', 
                'comment_hidden', 
                'account_suspended', 
                'account_unsuspended', 
                'system'
            ])->default('system');
            $table->string('title');
            $table->text('message');
            $table->foreignId('related_video_id')->nullable()->constrained('videos')->onDelete('cascade');
            $table->foreignId('related_comment_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->foreignId('action_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Copiar dados existentes
        DB::statement('INSERT INTO notifications_new SELECT * FROM notifications');

        // Remover tabela antiga
        Schema::dropIfExists('notifications');

        // Renomear nova tabela
        Schema::rename('notifications_new', 'notifications');

        // Recriar índices
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('is_read');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para o schema anterior
        Schema::create('notifications_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', [
                'video_approved', 
                'video_rejected', 
                'video_hidden', 
                'comment_approved', 
                'comment_hidden', 
                'account_suspended', 
                'account_unsuspended', 
                'system'
            ])->default('system');
            $table->string('title');
            $table->text('message');
            $table->foreignId('related_video_id')->nullable()->constrained('videos')->onDelete('cascade');
            $table->foreignId('related_comment_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->foreignId('action_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Copiar dados (excluindo video_edited que não existe no schema antigo)
        DB::statement("INSERT INTO notifications_old SELECT * FROM notifications WHERE type != 'video_edited'");

        Schema::dropIfExists('notifications');
        Schema::rename('notifications_old', 'notifications');

        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('is_read');
            $table->index('type');
        });
    }
};
