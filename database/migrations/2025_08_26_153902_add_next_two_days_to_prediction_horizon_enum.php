<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add next_two_days to the prediction_horizon enum column
        DB::statement("ALTER TABLE predictions MODIFY COLUMN prediction_horizon ENUM('next_two_days', 'next_two_weeks', 'next_month', 'three_months', 'six_months', 'twelve_months', 'two_years') DEFAULT 'next_month'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove next_two_days from the prediction_horizon enum column
        DB::statement("ALTER TABLE predictions MODIFY COLUMN prediction_horizon ENUM('next_two_weeks', 'next_month', 'three_months', 'six_months', 'twelve_months', 'two_years') DEFAULT 'next_month'");
    }
};
