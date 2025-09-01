<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prediction_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Token Usage
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            
            // Cost Tracking
            $table->decimal('estimated_cost', 10, 6)->default(0.00);
            $table->string('cost_currency', 3)->default('USD');
            
            // Performance Metrics
            $table->decimal('api_response_time', 8, 4)->default(0.0000);
            $table->decimal('total_processing_time', 8, 4)->default(0.0000);
            $table->integer('retry_attempts')->default(0);
            $table->string('retry_reason')->nullable();
            
            // API Details
            $table->string('model_used')->default('gemini-2.5-flash');
            $table->string('api_endpoint');
            $table->integer('http_status_code')->nullable();
            $table->text('api_error_message')->nullable();
            
            // Content Analysis
            $table->integer('input_text_length')->default(0);
            $table->integer('scraped_urls_count')->default(0);
            $table->integer('successful_scrapes')->default(0);
            $table->integer('uploaded_files_count')->default(0);
            $table->integer('total_file_size_bytes')->default(0);
            
            // User Context
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('analysis_type')->default('prediction-analysis');
            $table->string('prediction_horizon')->nullable();
            
            // Timestamps
            $table->timestamp('analysis_started_at');
            $table->timestamp('analysis_completed_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index(['prediction_id']);
            $table->index(['analysis_type']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_analytics');
    }
};
