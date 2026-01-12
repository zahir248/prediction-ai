<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SocialMediaAnalysis;

$analysis = SocialMediaAnalysis::find(102);

if ($analysis && !empty($analysis->ai_analysis)) {
    $aiAnalysis = $analysis->ai_analysis;
    
    echo "=== POLITICAL ENGAGEMENT INDICATORS ===\n";
    if (isset($aiAnalysis['political_engagement_indicators'])) {
        $data = $aiAnalysis['political_engagement_indicators'];
        echo "Has data: Yes\n";
        echo "Keys: " . implode(', ', array_keys($data)) . "\n";
        echo "Has consistency_score: " . (isset($data['consistency_score']) ? 'Yes (' . $data['consistency_score'] . ')' : 'No') . "\n";
        echo "Has activism_level_score: " . (isset($data['activism_level_score']) ? 'Yes (' . $data['activism_level_score'] . ')' : 'No') . "\n";
        echo "Has commitment_score: " . (isset($data['commitment_score']) ? 'Yes (' . $data['commitment_score'] . ')' : 'No') . "\n";
        echo "Has advocacy_score: " . (isset($data['advocacy_score']) ? 'Yes (' . $data['advocacy_score'] . ')' : 'No') . "\n";
        echo "Has influence_score: " . (isset($data['influence_score']) ? 'Yes (' . $data['influence_score'] . ')' : 'No') . "\n";
        echo "\nFull structure:\n";
        print_r($data);
    } else {
        echo "Missing!\n";
    }
    
    echo "\n=== POLITICAL ALIGNMENT INDICATORS ===\n";
    if (isset($aiAnalysis['political_alignment_indicators'])) {
        $data = $aiAnalysis['political_alignment_indicators'];
        echo "Has data: Yes\n";
        echo "Keys: " . implode(', ', array_keys($data)) . "\n";
        print_r($data);
    } else {
        echo "Missing!\n";
    }
    
    echo "\n=== POLITICAL GROWTH SIGNALS ===\n";
    if (isset($aiAnalysis['political_growth_signals'])) {
        $data = $aiAnalysis['political_growth_signals'];
        echo "Has data: Yes\n";
        echo "Keys: " . implode(', ', array_keys($data)) . "\n";
        print_r($data);
    } else {
        echo "Missing!\n";
    }
    
    echo "\n=== POLITICAL COMMUNICATION STYLE ===\n";
    if (isset($aiAnalysis['political_communication_style'])) {
        $data = $aiAnalysis['political_communication_style'];
        echo "Has data: Yes\n";
        echo "Keys: " . implode(', ', array_keys($data)) . "\n";
        print_r($data);
    } else {
        echo "Missing!\n";
    }
} else {
    echo "Analysis not found or no AI analysis data\n";
}
