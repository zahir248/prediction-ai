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
        Schema::table('data_analyses', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('id');
            $table->string('file_name')->after('user_id');
            $table->string('file_path')->nullable()->after('file_name');
            $table->json('excel_data')->nullable()->after('file_path');
            $table->json('ai_insights')->nullable()->after('excel_data');
            $table->json('chart_configs')->nullable()->after('ai_insights');
            $table->string('model_used')->default('gemini-2.5-flash')->after('chart_configs');
            $table->decimal('processing_time', 10, 3)->nullable()->after('model_used');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('processing_time');
            $table->text('error_message')->nullable()->after('status');
            $table->text('custom_insights')->nullable()->after('error_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_analyses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'file_name',
                'file_path',
                'excel_data',
                'ai_insights',
                'chart_configs',
                'model_used',
                'processing_time',
                'status',
                'error_message',
                'custom_insights'
            ]);
        });
    }
};
