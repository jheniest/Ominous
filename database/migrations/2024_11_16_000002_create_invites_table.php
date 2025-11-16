<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->onDelete('set null');
            $table->integer('max_uses')->default(1);
            $table->integer('current_uses')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'expired', 'consumed', 'suspended'])->default('active');
            $table->enum('source', ['manual', 'purchase', 'guest_purchase', 'admin'])->default('manual');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('status');
            $table->index('created_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
