<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invite_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invite_id')->constrained('invites')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('redeemed_at');
            $table->timestamps();
            
            $table->index('invite_id');
            $table->index('user_id');
            $table->index('redeemed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invite_redemptions');
    }
};
