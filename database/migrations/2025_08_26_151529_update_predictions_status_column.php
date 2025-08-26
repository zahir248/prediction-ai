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
            // Drop the existing enum column
            $table->dropColumn('status');
        });
        
        Schema::table('predictions', function (Blueprint $table) {
            // Recreate the enum column with all required status values
            $table->enum('status', [
                'pending', 
                'processing', 
                'completed', 
                'completed_with_warnings',
                'failed',
                'cancelled'
            ])->default('pending')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            // Drop the updated enum column
            $table->dropColumn('status');
        });
        
        Schema::table('predictions', function (Blueprint $table) {
            // Restore the original enum column
            $table->enum('status', [
                'pending', 
                'processing', 
                'completed', 
                'failed'
            ])->default('pending')->after('user_id');
        });
    }
};
