<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    // Prediction horizon constants
    const HORIZON_NEXT_TWO_WEEKS = 'next_two_weeks';
    const HORIZON_NEXT_MONTH = 'next_month';
    const HORIZON_THREE_MONTHS = 'three_months';
    const HORIZON_SIX_MONTHS = 'six_months';
    const HORIZON_TWELVE_MONTHS = 'twelve_months';
    const HORIZON_TWO_YEARS = 'two_years';

    // Available horizon options
    public static function getHorizonOptions()
    {
        return [
            self::HORIZON_NEXT_TWO_WEEKS => 'Next Two Weeks',
            self::HORIZON_NEXT_MONTH => 'Next Month',
            self::HORIZON_THREE_MONTHS => '3 Months',
            self::HORIZON_SIX_MONTHS => '6 Months',
            self::HORIZON_TWELVE_MONTHS => '12 Months',
            self::HORIZON_TWO_YEARS => '2 Years',
        ];
    }

    protected $fillable = [
        'topic',
        'input_data',
        'prediction_horizon',
        'source_urls',
        'prediction_result',
        'confidence_score',
        'model_used',
        'processing_time',
        'user_id',
        'status'
    ];

    protected $casts = [
        'input_data' => 'array',
        'source_urls' => 'array',
        'prediction_result' => 'array',
        'confidence_score' => 'float',
        'processing_time' => 'float',
    ];
    
    // Ensure confidence_score is always a float
    public function setConfidenceScoreAttribute($value)
    {
        $this->attributes['confidence_score'] = is_numeric($value) ? (float) $value : 0.75;
    }
    
    // Ensure processing_time is always a float
    public function setProcessingTimeAttribute($value)
    {
        $this->attributes['processing_time'] = is_numeric($value) ? (float) $value : 0.0;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
