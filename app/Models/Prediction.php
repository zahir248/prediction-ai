<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic',
        'input_data',
        'prediction_result',
        'confidence_score',
        'model_used',
        'processing_time',
        'user_id',
        'status'
    ];

    protected $casts = [
        'input_data' => 'array',
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
