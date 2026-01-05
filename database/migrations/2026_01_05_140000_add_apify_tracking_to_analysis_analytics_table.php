<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analysis_analytics', function (Blueprint $table) {
            // Apify usage tracking fields
            $table->integer('apify_calls_count')->default(0)->after('successful_scrapes');
            $table->string('apify_platforms_used')->nullable()->after('apify_calls_count'); // JSON array of platforms
            $table->decimal('apify_total_cost', 10, 6)->default(0.00)->after('apify_platforms_used');
            $table->decimal('apify_total_response_time', 8, 4)->default(0.0000)->after('apify_total_cost');
            $table->integer('apify_successful_calls')->default(0)->after('apify_total_response_time');
            $table->integer('apify_failed_calls')->default(0)->after('apify_successful_calls');
        });
    }

    public function down(): void
    {
        Schema::table('analysis_analytics', function (Blueprint $table) {
            $table->dropColumn([
                'apify_calls_count',
                'apify_platforms_used',
                'apify_total_cost',
                'apify_total_response_time',
                'apify_successful_calls',
                'apify_failed_calls'
            ]);
        });
    }
};

