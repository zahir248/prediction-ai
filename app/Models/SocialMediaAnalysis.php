<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaAnalysis extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    // Available status options
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    protected $fillable = [
        'username',
        'platform_data',
        'ai_analysis',
        'model_used',
        'processing_time',
        'user_id',
        'status'
    ];

    protected $casts = [
        'platform_data' => 'array',
        'ai_analysis' => 'array',
        'processing_time' => 'float',
    ];

    // Ensure processing_time is always a float
    public function setProcessingTimeAttribute($value)
    {
        $this->attributes['processing_time'] = is_numeric($value) ? (float) $value : 0.0;
    }

    // Ensure status is always valid
    public function setStatusAttribute($value)
    {
        $validStatuses = [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_FAILED
        ];
        
        if (!in_array($value, $validStatuses)) {
            \Log::error('Invalid status attempted: ' . $value);
            throw new \InvalidArgumentException('Invalid status value: ' . $value . '. Valid values are: ' . implode(', ', $validStatuses));
        }
        
        $this->attributes['status'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get platforms that were found in this analysis
     */
    public function getFoundPlatformsAttribute()
    {
        if (!$this->platform_data || !is_array($this->platform_data)) {
            return [];
        }

        $found = [];
        foreach ($this->platform_data as $platform => $data) {
            if (is_array($data) && isset($data['found']) && $data['found']) {
                $found[] = $platform;
            }
        }
        return $found;
    }

    /**
     * Get platform count
     */
    public function getPlatformCountAttribute()
    {
        return count($this->found_platforms);
    }

    /**
     * Get the first available profile URL from platform data
     */
    public function getFirstProfileUrlAttribute()
    {
        if (!$this->platform_data || !is_array($this->platform_data)) {
            return null;
        }

        // Check each platform for a profile URL
        foreach ($this->platform_data as $platform => $platformInfo) {
            if (is_array($platformInfo) && isset($platformInfo['found']) && $platformInfo['found'] && isset($platformInfo['data'])) {
                $data = $platformInfo['data'];
                
                // Check for profile_url or link
                if (isset($data['profile_url']) && is_string($data['profile_url'])) {
                    return $data['profile_url'];
                }
                
                if (isset($data['link']) && is_string($data['link'])) {
                    return $data['link'];
                }
                
                // Construct URL from username if available
                if (isset($data['username']) && is_string($data['username'])) {
                    $username = $data['username'];
                    switch ($platform) {
                        case 'facebook':
                            return "https://www.facebook.com/{$username}";
                        case 'instagram':
                            return "https://www.instagram.com/{$username}/";
                        case 'tiktok':
                            return "https://www.tiktok.com/@{$username}";
                    }
                }
            }
        }

        return null;
    }
}
