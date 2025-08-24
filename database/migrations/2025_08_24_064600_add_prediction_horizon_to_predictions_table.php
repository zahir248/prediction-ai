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
            $table->enum('prediction_horizon', [
                'next_two_weeks',
                'next_month', 
                'three_months',
                'six_months',
                'twelve_months',
                'two_years'
            ])->default('next_month')->after('input_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropColumn('prediction_horizon');
        });
    }
};
