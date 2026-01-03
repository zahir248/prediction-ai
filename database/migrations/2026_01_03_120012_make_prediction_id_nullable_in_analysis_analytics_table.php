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
        Schema::table('analysis_analytics', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['prediction_id']);
            
            // Make the column nullable
            $table->unsignedBigInteger('prediction_id')->nullable()->change();
            
            // Re-add the foreign key constraint, but without cascade delete
            // This allows null values and preserves analytics when prediction is deleted
            $table->foreign('prediction_id')
                ->references('id')
                ->on('predictions')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analysis_analytics', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['prediction_id']);
            
            // Make the column NOT NULL again
            $table->unsignedBigInteger('prediction_id')->nullable(false)->change();
            
            // Re-add the original foreign key with cascade delete
            $table->foreign('prediction_id')
                ->references('id')
                ->on('predictions')
                ->onDelete('cascade');
        });
    }
};
