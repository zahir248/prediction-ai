<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentimentComparison extends Model
{
    protected $fillable = [
        'user_id',
        'social_media_analysis_a_id',
        'social_media_analysis_b_id',
        'ai_result',
        'report_language',
        'processing_time',
        'model_used',
    ];

    protected $casts = [
        'ai_result' => 'array',
        'processing_time' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function socialMediaAnalysisA(): BelongsTo
    {
        return $this->belongsTo(SocialMediaAnalysis::class, 'social_media_analysis_a_id');
    }

    public function socialMediaAnalysisB(): BelongsTo
    {
        return $this->belongsTo(SocialMediaAnalysis::class, 'social_media_analysis_b_id');
    }
}
