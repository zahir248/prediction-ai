<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SocialMediaAnalysis;

$analysisId = 102;
$analysis = SocialMediaAnalysis::find($analysisId);

if ($analysis) {
    echo "Analysis Found!\n";
    echo "ID: {$analysis->id}\n";
    echo "Username: {$analysis->username}\n";
    echo "Status: {$analysis->status}\n";
    echo "User ID: {$analysis->user_id}\n";
    echo "Model Used: {$analysis->model_used}\n";
    echo "Processing Time: {$analysis->processing_time} seconds\n";
    echo "\n";
    
    if (!empty($analysis->ai_analysis)) {
        echo "AI Analysis exists!\n";
        $aiAnalysis = $analysis->ai_analysis;
        
        if (is_array($aiAnalysis)) {
            echo "Title: " . ($aiAnalysis['title'] ?? 'N/A') . "\n";
            echo "Has Executive Summary: " . (isset($aiAnalysis['executive_summary']) ? 'Yes' : 'No') . "\n";
            echo "Has Risk Assessment: " . (isset($aiAnalysis['risk_assessment']) ? 'Yes' : 'No') . "\n";
            echo "Has Predictions: " . (isset($aiAnalysis['predictions']) ? 'Yes' : 'No') . "\n";
            echo "\n";
            echo "Full Analysis Structure:\n";
            print_r(array_keys($aiAnalysis));
        } else {
            echo "AI Analysis is not an array: " . gettype($aiAnalysis) . "\n";
        }
    } else {
        echo "No AI Analysis found!\n";
    }
    
    echo "\n";
    echo "Platform Data:\n";
    if (!empty($analysis->platform_data)) {
        $platforms = array_keys($analysis->platform_data);
        echo "Platforms: " . implode(', ', $platforms) . "\n";
    } else {
        echo "No platform data\n";
    }
    
    echo "\n";
    echo "View URL: /social-media/{$analysis->id}\n";
    echo "Direct URL: " . url("/social-media/{$analysis->id}") . "\n";
    
} else {
    echo "Analysis ID {$analysisId} not found!\n";
    echo "\nRecent analyses:\n";
    $recent = SocialMediaAnalysis::orderBy('id', 'desc')->limit(5)->get();
    foreach ($recent as $a) {
        echo "ID: {$a->id}, Username: {$a->username}, Status: {$a->status}\n";
    }
}
