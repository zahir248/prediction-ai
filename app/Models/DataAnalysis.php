<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAnalysis extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'file_name',
        'file_path',
        'excel_data',
        'ai_insights',
        'chart_configs',
        'model_used',
        'processing_time',
        'user_id',
        'status',
        'error_message',
    ];

    protected $casts = [
        'excel_data' => 'array',
        'ai_insights' => 'array',
        'chart_configs' => 'array',
        'processing_time' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
        ];
    }
}
