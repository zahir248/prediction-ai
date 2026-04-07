<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sentiment_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('social_media_analysis_a_id')->constrained('social_media_analyses')->cascadeOnDelete();
            $table->foreignId('social_media_analysis_b_id')->constrained('social_media_analyses')->cascadeOnDelete();
            $table->json('ai_result')->nullable();
            $table->string('report_language', 8)->default('en');
            $table->float('processing_time')->nullable();
            $table->string('model_used')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sentiment_comparisons');
    }
};
