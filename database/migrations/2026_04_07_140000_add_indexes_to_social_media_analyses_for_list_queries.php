<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Speeds user-scoped lists (e.g. completed analyses ordered by date) and reduces sort work.
     */
    public function up(): void
    {
        Schema::table('social_media_analyses', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'created_at'], 'sma_user_status_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('social_media_analyses', function (Blueprint $table) {
            $table->dropIndex('sma_user_status_created_idx');
        });
    }
};
