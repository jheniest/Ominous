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
        // Add admin_message type to notifications enum
        // SQLite doesn't support ALTER COLUMN for enum, so we need to recreate
        
        // For SQLite: We'll just allow any string since SQLite doesn't enforce enum
        // The validation will be done at application level
        
        // For MySQL, you would do:
        // DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('video_approved', 'video_rejected', 'video_hidden', 'video_edited', 'comment_approved', 'comment_hidden', 'account_suspended', 'account_unsuspended', 'admin_message', 'system') DEFAULT 'system'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert if needed
    }
};
