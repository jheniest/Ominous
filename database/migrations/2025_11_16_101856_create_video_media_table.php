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
        Schema::create('video_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained('videos')->onDelete('cascade');
            $table->enum('type', ['video', 'image'])->default('video');
            $table->string('file_path'); // storage path
            $table->string('url'); // public URL
            $table->integer('order')->default(0);
            $table->string('mime_type')->nullable();
            $table->integer('file_size')->nullable(); // in bytes
            $table->integer('duration')->nullable(); // for videos, in seconds
            $table->json('metadata')->nullable(); // width, height, etc
            $table->timestamps();
            
            $table->index(['video_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_media');
    }
};
