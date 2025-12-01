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
        // SQLite doesn't support altering enum columns, so we need to recreate the table
        
        // 1. Create a temporary table with the new enum
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
                'admin_message',
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
            
            $table->index('user_id');
            $table->index('is_read');
            $table->index('type');
        });
        
        // 2. Copy data from old table to new table
        DB::statement('INSERT INTO notifications_new SELECT * FROM notifications');
        
        // 3. Drop the old table
        Schema::dropIfExists('notifications');
        
        // 4. Rename new table to original name
        Schema::rename('notifications_new', 'notifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate original table structure
        Schema::create('notifications_old', function (Blueprint $table) {
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
        
        // Delete admin_message type and copy data
        DB::statement("INSERT INTO notifications_old SELECT * FROM notifications WHERE type != 'admin_message'");
        
        Schema::dropIfExists('notifications');
        Schema::rename('notifications_old', 'notifications');
    }
};
