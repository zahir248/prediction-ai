<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'prediction_id',
        'user_id',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'estimated_cost',
        'cost_currency',
        'api_response_time',
        'total_processing_time',
        'retry_attempts',
        'retry_reason',
        'model_used',
        'api_endpoint',
        'http_status_code',
        'api_error_message',
        'input_text_length',
        'scraped_urls_count',
        'successful_scrapes',
        'uploaded_files_count',
        'total_file_size_bytes',
        'user_agent',
        'ip_address',
        'analysis_type',
        'prediction_horizon',
        'analysis_started_at',
        'analysis_completed_at'
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:6',
        'api_response_time' => 'decimal:4',
        'total_processing_time' => 'decimal:4',
        'analysis_started_at' => 'datetime',
        'analysis_completed_at' => 'datetime',
    ];

    // Relationships
    public function prediction()
    {
        return $this->belongsTo(Prediction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes for filtering
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByAnalysisType($query, $type)
    {
        return $query->where('analysis_type', $type);
    }

    public function scopeByOrganization($query, $organization)
    {
        if ($organization) {
            return $query->whereHas('user', function($q) use ($organization) {
                $q->where('organization', $organization);
            });
        }
        return $query;
    }

    // Helper methods
    public function calculateTotalTokens()
    {
        $this->total_tokens = $this->input_tokens + $this->output_tokens;
        return $this->total_tokens;
    }

    public function calculateEstimatedCost()
    {
        // Gemini pricing (approximate as of 2024)
        // Input: $0.00025 per 1K tokens
        // Output: $0.0005 per 1K tokens
        $inputCost = ($this->input_tokens / 1000) * 0.00025;
        $outputCost = ($this->output_tokens / 1000) * 0.0005;
        
        $this->estimated_cost = $inputCost + $outputCost;
        return $this->estimated_cost;
    }

    public function markCompleted()
    {
        $this->analysis_completed_at = now();
        $this->save();
    }

    // Static methods for analytics
    public static function getTotalTokenUsage($userId = null, $startDate = null, $endDate = null, $organization = null)
    {
        $query = self::query();
        
        if ($userId) {
            $query->byUser($userId);
        }
        
        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }
        
        if ($organization) {
            $query->byOrganization($organization);
        }
        
        return $query->sum('total_tokens');
    }

    public static function getTotalCost($userId = null, $startDate = null, $endDate = null, $organization = null)
    {
        $query = self::query();
        
        if ($userId) {
            $query->byUser($userId);
        }
        
        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }
        
        if ($organization) {
            $query->byOrganization($organization);
        }
        
        return $query->sum('estimated_cost');
    }

    public static function getAverageProcessingTime($userId = null, $startDate = null, $endDate = null, $organization = null)
    {
        $query = self::query();
        
        if ($userId) {
            $query->byUser($userId);
        }
        
        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }
        
        if ($organization) {
            $query->byOrganization($organization);
        }
        
        return $query->avg('total_processing_time');
    }

    public static function getSuccessRate($userId = null, $startDate = null, $endDate = null, $organization = null)
    {
        $query = self::query();
        
        if ($userId) {
            $query->byUser($userId);
        }
        
        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }
        
        if ($organization) {
            $query->byOrganization($organization);
        }
        
        $total = $query->count();
        $successful = $query->whereNull('api_error_message')->count();
        
        return $total > 0 ? ($successful / $total) * 100 : 0;
    }
}
