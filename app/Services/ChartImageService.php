<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChartImageService
{
    /**
     * Generate a radar/spider chart as base64 encoded image
     * This creates an SVG and converts it to base64 for PDF embedding
     */
    public static function generateRadarChart($dimensions, $options = [])
    {
        $centerX = $options['centerX'] ?? 250;
        $centerY = $options['centerY'] ?? 250;
        $radius = $options['radius'] ?? 150;
        $fillColor = $options['fillColor'] ?? 'rgba(59, 130, 246, 0.2)';
        $strokeColor = $options['strokeColor'] ?? '#3b82f6';
        $pointColor = $options['pointColor'] ?? '#1e40af';
        
        $numAxes = count($dimensions);
        $angleStep = (2 * M_PI) / $numAxes;
        
        $points = [];
        $angles = [];
        
        foreach ($dimensions as $key => $dim) {
            $i = array_search($key, array_keys($dimensions));
            $angle = ($i * $angleStep) - (M_PI / 2);
            $angles[] = $angle;
            $score = is_numeric($dim['score']) ? (int)$dim['score'] : 50;
            $score = max(0, min(100, $score));
            $distance = ($score / 100) * $radius;
            $x = $centerX + ($distance * cos($angle));
            $y = $centerY + ($distance * sin($angle));
            $points[] = ['x' => $x, 'y' => $y, 'label' => $dim['label'] ?? $key, 'score' => $score];
        }
        
        // Generate SVG
        $svg = '<svg viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: auto;">';
        
        // Grid circles
        for ($i = 1; $i <= 5; $i++) {
            $svg .= sprintf(
                '<circle cx="%s" cy="%s" r="%s" fill="none" stroke="#e5e7eb" stroke-width="1" stroke-dasharray="2,2"/>',
                $centerX, $centerY, ($i / 5) * $radius
            );
        }
        
        // Axis lines
        foreach ($angles as $angle) {
            $svg .= sprintf(
                '<line x1="%s" y1="%s" x2="%s" y2="%s" stroke="#e5e7eb" stroke-width="1"/>',
                $centerX, $centerY,
                $centerX + ($radius * cos($angle)),
                $centerY + ($radius * sin($angle))
            );
        }
        
        // Data polygon
        $pointsString = implode(' ', array_map(function($p) {
            return round($p['x'], 2) . ',' . round($p['y'], 2);
        }, $points));
        $svg .= sprintf(
            '<polygon points="%s" fill="%s" stroke="%s" stroke-width="2"/>',
            $pointsString, $fillColor, $strokeColor
        );
        
        // Data points and labels
        foreach ($points as $index => $point) {
            $labelAngle = $angles[$index];
            $labelDistance = $radius + 35;
            $labelX = $centerX + ($labelDistance * cos($labelAngle));
            $labelY = $centerY + ($labelDistance * sin($labelAngle));
            $textAnchor = abs($labelX - $centerX) < 10 ? 'middle' : ($labelX > $centerX ? 'start' : 'end');
            
            // Point
            $svg .= sprintf(
                '<circle cx="%s" cy="%s" r="6" fill="%s" stroke="white" stroke-width="2"/>',
                round($point['x'], 2), round($point['y'], 2), $pointColor
            );
            
            // Label
            $svg .= sprintf(
                '<text x="%s" y="%s" text-anchor="%s" fill="#374151" font-size="13" font-weight="600">%s</text>',
                round($labelX, 2), round($labelY, 2), $textAnchor, htmlspecialchars($point['label'])
            );
        }
        
        $svg .= '</svg>';
        
        // Convert SVG to base64 data URI for PDF embedding
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    
    /**
     * Generate a circular gauge chart
     */
    public static function generateGaugeChart($score, $maxScore = 100, $options = [])
    {
        $radius = $options['radius'] ?? 60;
        $size = $options['size'] ?? 140;
        $center = $size / 2;
        
        $percentage = round(($score / $maxScore) * 100);
        $circumference = 2 * M_PI * $radius;
        $offset = $circumference - ($percentage / 100) * $circumference;
        
        // Determine color
        $scoreColor = '#ef4444';
        if ($percentage >= 70) {
            $scoreColor = '#10b981';
        } elseif ($percentage >= 50) {
            $scoreColor = '#f59e0b';
        }
        
        $svg = sprintf(
            '<svg width="%s" height="%s" xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg);">',
            $size, $size
        );
        
        // Background circle
        $svg .= sprintf(
            '<circle cx="%s" cy="%s" r="%s" fill="none" stroke="#e5e7eb" stroke-width="12" stroke-linecap="round"/>',
            $center, $center, $radius
        );
        
        // Progress circle
        $svg .= sprintf(
            '<circle cx="%s" cy="%s" r="%s" fill="none" stroke="%s" stroke-width="12" stroke-linecap="round" stroke-dasharray="%s" stroke-dashoffset="%s"/>',
            $center, $center, $radius, $scoreColor, $circumference, $offset
        );
        
        $svg .= '</svg>';
        
        // Add text overlay (we'll position it in HTML)
        return [
            'svg' => 'data:image/svg+xml;base64,' . base64_encode($svg),
            'score' => $score,
            'maxScore' => $maxScore,
            'percentage' => $percentage,
            'color' => $scoreColor,
            'size' => $size,
            'center' => $center
        ];
    }
}
