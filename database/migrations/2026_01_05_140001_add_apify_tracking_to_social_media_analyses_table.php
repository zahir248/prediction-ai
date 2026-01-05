<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_media_analyses', function (Blueprint $table) {
            // Apify usage tracking fields
            $table->integer('apify_calls_count')->default(0)->after('processing_time');
            $table->json('apify_usage_details')->nullable()->after('apify_calls_count'); // Store detailed Apify call info
            $table->decimal('apify_total_cost', 10, 6)->default(0.00)->after('apify_usage_details');
            $table->decimal('apify_total_response_time', 8, 4)->default(0.0000)->after('apify_total_cost');
        });
    }

    public function down(): void
    {
        Schema::table('social_media_analyses', function (Blueprint $table) {
            $table->dropColumn([
                'apify_calls_count',
                'apify_usage_details',
                'apify_total_cost',
                'apify_total_response_time'
            ]);
        });
    }
};

