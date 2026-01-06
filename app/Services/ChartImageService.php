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
    
    /**
     * Generate Chart.js chart as image using QuickChart.io API
     * Converts Chart.js config to image URL
     */
    public static function generateChartJsImage($chartConfig, $width = 800, $height = 400)
    {
        try {
            $chartType = $chartConfig['type'] ?? 'bar';
            $chartData = $chartConfig['data'] ?? [];
            $chartOptions = $chartConfig['options'] ?? [];
            
            // Validate chart data
            if (empty($chartData) || empty($chartData['labels']) || empty($chartData['datasets'])) {
                \Log::warning('Chart data validation failed', [
                    'has_data' => !empty($chartData),
                    'has_labels' => !empty($chartData['labels']),
                    'has_datasets' => !empty($chartData['datasets']),
                ]);
                return null;
            }
            
            // Clean and validate datasets
            $validDatasets = [];
            foreach ($chartData['datasets'] as $datasetIndex => $dataset) {
                if (isset($dataset['data']) && is_array($dataset['data'])) {
                    // Ensure data is numeric or null, and filter out nulls for better chart rendering
                    $cleanedData = [];
                    foreach ($dataset['data'] as $val) {
                        if (is_numeric($val)) {
                            $cleanedData[] = (float)$val;
                        } elseif ($val !== null && $val !== '') {
                            // Try to convert string numbers
                            $numVal = filter_var($val, FILTER_VALIDATE_FLOAT);
                            $cleanedData[] = $numVal !== false ? (float)$numVal : 0;
                        } else {
                            $cleanedData[] = 0; // Use 0 instead of null for better compatibility
                        }
                    }
                    
                    // Ensure data length matches labels length
                    if (count($cleanedData) !== count($chartData['labels'])) {
                        \Log::warning('Dataset data length mismatch with labels', [
                            'dataset_index' => $datasetIndex,
                            'data_length' => count($cleanedData),
                            'labels_length' => count($chartData['labels']),
                        ]);
                        // Pad or truncate to match
                        if (count($cleanedData) < count($chartData['labels'])) {
                            $cleanedData = array_pad($cleanedData, count($chartData['labels']), 0);
                        } else {
                            $cleanedData = array_slice($cleanedData, 0, count($chartData['labels']));
                        }
                    }
                    
                    $validDatasets[] = [
                        'label' => $dataset['label'] ?? 'Dataset ' . ($datasetIndex + 1),
                        'data' => $cleanedData,
                    ];
                } else {
                    \Log::warning('Dataset missing data array', [
                        'dataset_index' => $datasetIndex,
                        'dataset' => $dataset,
                    ]);
                }
            }
            
            if (empty($validDatasets)) {
                \Log::warning('No valid datasets found after cleaning', [
                    'original_datasets_count' => count($chartData['datasets']),
                ]);
                return null;
            }
            
            // Prepare Chart.js config for QuickChart
            $quickChartConfig = [
                'type' => $chartType,
                'data' => [
                    'labels' => $chartData['labels'],
                    'datasets' => $validDatasets,
                ],
                'options' => array_merge([
                    'responsive' => false,
                    'plugins' => [
                        'legend' => [
                            'display' => true,
                            'position' => 'top',
                        ],
                    ],
                ], $chartOptions),
            ];
            
            // Encode config as JSON
            $configJson = json_encode($quickChartConfig);
            
            // Check if config is too long (QuickChart has URL length limits)
            // Limit data points to prevent timeout and URL length issues
            $maxDataPoints = 30; // Reduced from 50 to 30 for faster generation
            if (count($quickChartConfig['data']['labels']) > $maxDataPoints || strlen($configJson) > 8000) {
                $quickChartConfig['data']['labels'] = array_slice($quickChartConfig['data']['labels'], 0, $maxDataPoints);
                foreach ($quickChartConfig['data']['datasets'] as &$dataset) {
                    if (isset($dataset['data']) && is_array($dataset['data'])) {
                        $dataset['data'] = array_slice($dataset['data'], 0, $maxDataPoints);
                    }
                }
                $configJson = json_encode($quickChartConfig);
            }
            
            // Check if config is still too long after truncation
            if (strlen($configJson) > 8000) {
                // Further reduce data points
                $maxDataPoints = 20;
                $quickChartConfig['data']['labels'] = array_slice($quickChartConfig['data']['labels'], 0, $maxDataPoints);
                foreach ($quickChartConfig['data']['datasets'] as &$dataset) {
                    if (isset($dataset['data']) && is_array($dataset['data'])) {
                        $dataset['data'] = array_slice($dataset['data'], 0, $maxDataPoints);
                    }
                }
                $configJson = json_encode($quickChartConfig);
            }
            
            // Generate QuickChart.io URL or use POST for large configs
            $baseUrl = 'https://quickchart.io/chart';
            
            // Use POST if config is too long for URL (QuickChart supports POST)
            $usePost = strlen($configJson) > 2000;
            
            $imageData = false;
            
            if ($usePost) {
                // Use POST method for large configs
                $postData = http_build_query([
                    'c' => $configJson,
                    'w' => $width,
                    'h' => $height,
                    'f' => 'png',
                ]);
                
                $context = stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                                   "Content-Length: " . strlen($postData) . "\r\n" .
                                   "User-Agent: DataAnalysisApp/1.0\r\n",
                        'content' => $postData,
                        'timeout' => 5,
                    ]
                ]);
                
                $imageData = @file_get_contents($baseUrl, false, $context);
            } else {
                // Use GET method for smaller configs
                $params = http_build_query([
                    'c' => $configJson,
                    'w' => $width,
                    'h' => $height,
                    'f' => 'png',
                ]);
                
                $chartUrl = $baseUrl . '?' . $params;
                
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 5,
                        'user_agent' => 'DataAnalysisApp/1.0',
                    ]
                ]);
                
                $imageData = @file_get_contents($chartUrl, false, $context);
            }
            
            if ($imageData === false || empty($imageData)) {
                $error = error_get_last();
                \Log::warning('QuickChart API request failed', [
                    'chart_type' => $chartType,
                    'url_length' => strlen($chartUrl),
                    'error' => $error['message'] ?? 'Unknown error',
                    'config_length' => strlen($configJson),
                ]);
                return null;
            }
            
            // Check if response is an error message (HTML/text) instead of image
            if (strlen($imageData) < 100 || strpos($imageData, '<html') !== false || strpos($imageData, 'error') !== false) {
                \Log::warning('QuickChart returned non-image response', [
                    'chart_type' => $chartType,
                    'response_preview' => substr($imageData, 0, 200),
                    'response_length' => strlen($imageData),
                ]);
                return null;
            }
            
            // Verify it's actually an image
            $imageInfo = @getimagesizefromstring($imageData);
            if ($imageInfo === false) {
                \Log::warning('QuickChart response is not a valid image', [
                    'chart_type' => $chartType,
                    'response_preview' => substr($imageData, 0, 200),
                ]);
                return null;
            }
            
            // Convert to base64 data URI
            $base64 = base64_encode($imageData);
            return 'data:image/png;base64,' . $base64;
        } catch (\Exception $e) {
            \Log::warning('Failed to generate chart image: ' . $e->getMessage(), [
                'chart_type' => $chartConfig['type'] ?? 'unknown',
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }
    
    /**
     * Generate static map image from location data
     */
    public static function generateMapImage($locations, $width = 800, $height = 500)
    {
        try {
            // Extract coordinates from locations
            $markers = [];
            $centerLat = 0;
            $centerLng = 0;
            $count = 0;
            
            foreach ($locations as $location) {
                if (isset($location['lat']) && isset($location['lng'])) {
                    $lat = floatval($location['lat']);
                    $lng = floatval($location['lng']);
                    $markers[] = $lat . ',' . $lng;
                    $centerLat += $lat;
                    $centerLng += $lng;
                    $count++;
                }
            }
            
            if (empty($markers)) {
                return null;
            }
            
            // Calculate center point
            $centerLat = $count > 0 ? $centerLat / $count : 0;
            $centerLng = $count > 0 ? $centerLng / $count : 0;
            
            // Limit markers to avoid URL length issues
            $markersLimited = array_slice($markers, 0, 20);
            
            // Set context for HTTP request
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'DataAnalysisApp/1.0',
                    'follow_location' => true,
                    'max_redirects' => 3,
                ]
            ]);
            
            $imageData = false;
            
            // Try multiple static map services
            // Method 1: Try Mapbox if token is available
            $mapboxToken = env('MAPBOX_ACCESS_TOKEN', '');
            if (!empty($mapboxToken)) {
                // Build markers string for Mapbox (lng,lat format)
                $mapboxMarkers = [];
                foreach ($markersLimited as $marker) {
                    list($lat, $lng) = explode(',', $marker);
                    $mapboxMarkers[] = sprintf('pin-s+ff0000(%s,%s)', $lng, $lat);
                }
                $markersParam = implode(',', $mapboxMarkers);
                
                $mapboxUrl = sprintf(
                    'https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/%s/%.6f,%.6f,6/%dx%d?access_token=%s',
                    urlencode($markersParam),
                    $centerLng,
                    $centerLat,
                    $width,
                    $height,
                    $mapboxToken
                );
                
                $imageData = @file_get_contents($mapboxUrl, false, $context);
            }
            
            // Method 2: Try OpenStreetMap static map (different format)
            if ($imageData === false || empty($imageData)) {
                // Try with different URL format - using lat,lng for center and markers
                $markersParam = implode('|', $markersLimited);
                $osmUrl = sprintf(
                    'https://staticmap.openstreetmap.de/staticmap.php?center=%.6f,%.6f&zoom=6&size=%dx%d&markers=%s',
                    $centerLat,
                    $centerLng,
                    $width,
                    $height,
                    urlencode($markersParam)
                );
                
                $imageData = @file_get_contents($osmUrl, false, $context);
                
                // Log response for debugging
                if ($imageData === false) {
                    $error = error_get_last();
                    \Log::info('OSM static map request failed', [
                        'url' => $osmUrl,
                        'error' => $error['message'] ?? 'Unknown error',
                    ]);
                } elseif (empty($imageData)) {
                    \Log::info('OSM static map returned empty response', ['url' => $osmUrl]);
                } elseif (strlen($imageData) < 100) {
                    // Might be an error message instead of image
                    \Log::info('OSM static map returned small response (might be error)', [
                        'url' => $osmUrl,
                        'response_preview' => substr($imageData, 0, 200),
                    ]);
                    $imageData = false; // Treat as failure
                }
            }
            
            // Method 3: Try alternative OSM static map service
            if ($imageData === false || empty($imageData)) {
                // Try with lon,lat format (reversed)
                $markersReversed = array_map(function($marker) {
                    list($lat, $lng) = explode(',', $marker);
                    return $lng . ',' . $lat;
                }, $markersLimited);
                $markersParam = implode('|', $markersReversed);
                
                $osmUrl2 = sprintf(
                    'https://www.openstreetmap.org/export/embed.html?bbox=%.6f,%.6f,%.6f,%.6f&layer=mapnik&marker=%s',
                    $centerLng - 5,
                    $centerLat - 5,
                    $centerLng + 5,
                    $centerLat + 5,
                    urlencode($centerLat . ',' . $centerLng)
                );
                
                // This won't work for image, but let's try a simple tile-based approach
                // Actually, let's generate an SVG map instead as fallback
            }
            
            // Method 4: Generate SVG map as fallback if all services fail
            if ($imageData === false || empty($imageData)) {
                \Log::info('All static map services failed, generating SVG map as fallback');
                return self::generateSvgMap($locations, $width, $height);
            }
            
            // Verify it's actually an image
            $imageInfo = @getimagesizefromstring($imageData);
            if ($imageInfo === false) {
                \Log::warning('Map image data is not a valid image', [
                    'data_length' => strlen($imageData),
                ]);
                return null;
            }
            
            // Convert to base64 data URI
            $base64 = base64_encode($imageData);
            return 'data:image/png;base64,' . $base64;
        } catch (\Exception $e) {
            \Log::warning('Failed to generate map image: ' . $e->getMessage());
            // Try SVG fallback
            try {
                return self::generateSvgMap($locations, $width, $height);
            } catch (\Exception $e2) {
                \Log::warning('SVG map generation also failed: ' . $e2->getMessage());
                return null;
            }
        }
    }
    
    /**
     * Generate a simple SVG map as fallback when static map services fail
     */
    private static function generateSvgMap($locations, $width = 800, $height = 500)
    {
        try {
            if (empty($locations)) {
                return null;
            }
            
            // Calculate bounds
            $minLat = min(array_column($locations, 'lat'));
            $maxLat = max(array_column($locations, 'lat'));
            $minLng = min(array_column($locations, 'lng'));
            $maxLng = max(array_column($locations, 'lng'));
            
            // Add padding
            $latRange = $maxLat - $minLat;
            $lngRange = $maxLng - $minLng;
            $padding = max($latRange, $lngRange) * 0.2;
            
            $minLat -= $padding;
            $maxLat += $padding;
            $minLng -= $padding;
            $maxLng += $padding;
            
            // Calculate scale
            $latScale = ($height - 40) / ($maxLat - $minLat);
            $lngScale = ($width - 40) / ($maxLng - $minLng);
            $scale = min($latScale, $lngScale);
            
            // Convert lat/lng to SVG coordinates
            $svgPoints = [];
            foreach ($locations as $location) {
                $x = 20 + ($location['lng'] - $minLng) * $scale;
                $y = $height - 20 - ($location['lat'] - $minLat) * $scale;
                $svgPoints[] = ['x' => $x, 'y' => $y, 'lat' => $location['lat'], 'lng' => $location['lng']];
            }
            
            // Generate SVG
            $svg = '<?xml version="1.0" encoding="UTF-8"?>';
            $svg .= sprintf('<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">', $width, $height, $width, $height);
            
            // Background
            $svg .= sprintf('<rect width="%d" height="%d" fill="#f0f4f8"/>', $width, $height);
            
            // Draw grid lines
            $svg .= '<g stroke="#d1d5db" stroke-width="0.5" opacity="0.5">';
            for ($i = 0; $i <= 10; $i++) {
                $x = 20 + ($i / 10) * ($width - 40);
                $y = 20 + ($i / 10) * ($height - 40);
                $svg .= sprintf('<line x1="%d" y1="20" x2="%d" y2="%d"/>', $x, $x, $height - 20);
                $svg .= sprintf('<line x1="20" y1="%d" x2="%d" y2="%d"/>', $y, $width - 20, $y);
            }
            $svg .= '</g>';
            
            // Draw markers
            foreach ($svgPoints as $point) {
                // Marker circle
                $svg .= sprintf(
                    '<circle cx="%.2f" cy="%.2f" r="8" fill="#ef4444" stroke="#ffffff" stroke-width="2"/>',
                    $point['x'],
                    $point['y']
                );
                // Inner dot
                $svg .= sprintf(
                    '<circle cx="%.2f" cy="%.2f" r="3" fill="#ffffff"/>',
                    $point['x'],
                    $point['y']
                );
            }
            
            // Add border
            $svg .= sprintf('<rect x="20" y="20" width="%d" height="%d" fill="none" stroke="#374151" stroke-width="2"/>', $width - 40, $height - 40);
            
            $svg .= '</svg>';
            
            // Convert to base64 data URI
            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        } catch (\Exception $e) {
            \Log::warning('Failed to generate SVG map: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate chart image from chart config (handles both regular charts and maps)
     */
    public static function generateChartImage($chartConfig, $width = 800, $height = 400)
    {
        $chartTitle = $chartConfig['title'] ?? '';
        $chartType = $chartConfig['type'] ?? 'bar';
        $chartDescription = $chartConfig['description'] ?? '';
        
        // Check if it's a map chart
        $isMapChart = stripos($chartTitle, 'map') !== false 
            || stripos($chartType, 'map') !== false
            || stripos($chartDescription, 'map') !== false;
        
        if ($isMapChart) {
            // Generate map image
            $chartData = $chartConfig['data'] ?? [];
            $labels = $chartData['labels'] ?? [];
            $datasets = $chartData['datasets'] ?? [];
            $coordinates = $chartConfig['coordinates'] ?? null;
            
            // Build locations array from coordinates
            $locations = [];
            
            // Try to extract coordinates from chart config
            if (!empty($coordinates) && is_array($coordinates)) {
                foreach ($coordinates as $coord) {
                    if (is_array($coord) && count($coord) >= 2) {
                        $lat = floatval($coord[0]);
                        $lng = floatval($coord[1]);
                        // Validate coordinates
                        if (!is_nan($lat) && !is_nan($lng) && 
                            $lat >= -90 && $lat <= 90 && 
                            $lng >= -180 && $lng <= 180) {
                            $locations[] = [
                                'lat' => $lat,
                                'lng' => $lng,
                            ];
                        }
                    }
                }
            }
            
            // Log if coordinates are missing
            if (empty($locations)) {
                \Log::warning('Map chart detected but no valid coordinates found', [
                    'chart_title' => $chartTitle,
                    'has_coordinates' => !empty($coordinates),
                    'coordinates_count' => is_array($coordinates) ? count($coordinates) : 0,
                    'labels_count' => count($labels),
                ]);
            }
            
            // Only generate map if we have valid locations
            if (!empty($locations)) {
                $mapImage = self::generateMapImage($locations, $width, $height);
                if ($mapImage) {
                    return $mapImage;
                } else {
                    \Log::warning('Map image generation failed even with valid coordinates', [
                        'chart_title' => $chartTitle,
                        'locations_count' => count($locations),
                    ]);
                }
            }
            
            // For maps without coordinates, return null to show data table only
            return null;
        }
        
        // Generate regular chart image
        return self::generateChartJsImage($chartConfig, $width, $height);
    }
}
