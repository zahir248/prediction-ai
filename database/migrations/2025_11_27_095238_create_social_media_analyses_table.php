<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_media_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->json('platform_data'); // Stores all platform data (Facebook, Instagram, TikTok)
            $table->json('ai_analysis')->nullable(); // Stores the AI analysis result
            $table->string('model_used')->nullable();
            $table->decimal('processing_time', 8, 4)->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media_analyses');
    }
};
