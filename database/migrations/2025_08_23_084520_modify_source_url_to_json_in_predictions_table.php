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
        Schema::table('predictions', function (Blueprint $table) {
            // First drop the existing source_url column
            $table->dropColumn('source_url');
            
            // Add new JSON column for multiple source URLs
            $table->json('source_urls')->nullable()->after('input_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            // Drop the JSON column
            $table->dropColumn('source_urls');
            
            // Add back the original string column
            $table->string('source_url')->nullable()->after('input_data');
        });
    }
};
