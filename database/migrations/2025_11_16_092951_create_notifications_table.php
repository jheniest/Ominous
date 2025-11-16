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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['video_approved', 'video_rejected', 'video_hidden', 'comment_approved', 'comment_hidden', 'account_suspended', 'account_unsuspended', 'system'])->default('system');
            $table->string('title');
            $table->text('message');
            $table->foreignId('related_video_id')->nullable()->constrained('videos')->onDelete('cascade');
            $table->foreignId('related_comment_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->foreignId('action_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
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
        Schema::dropIfExists('notifications');
    }
};
