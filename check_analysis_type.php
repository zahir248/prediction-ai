<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SocialMediaAnalysis;

$analysis = SocialMediaAnalysis::find(102);

if ($analysis && !empty($analysis->ai_analysis)) {
    $aiAnalysis = $analysis->ai_analysis;
    
    echo "=== ANALYSIS TYPE CHECK ===\n";
    $analysisType = $aiAnalysis['analysis_type'] ?? 'NOT SET';
    echo "Analysis Type: {$analysisType}\n";
    echo "Will show political sections: " . ($analysisType === 'political' ? 'YES' : 'NO') . "\n\n";
    
    echo "=== SECTIONS CHECK ===\n";
    echo "Has political_engagement_indicators: " . (isset($aiAnalysis['political_engagement_indicators']) ? 'YES' : 'NO') . "\n";
    echo "Has political_alignment_indicators: " . (isset($aiAnalysis['political_alignment_indicators']) ? 'YES' : 'NO') . "\n";
    echo "Has political_growth_signals: " . (isset($aiAnalysis['political_growth_signals']) ? 'YES' : 'NO') . "\n";
    echo "Has political_communication_style: " . (isset($aiAnalysis['political_communication_style']) ? 'YES' : 'NO') . "\n";
    echo "Has political_career_profile: " . (isset($aiAnalysis['political_career_profile']) ? 'YES' : 'NO') . "\n";
    echo "Has activity_overview: " . (isset($aiAnalysis['activity_overview']) ? 'YES' : 'NO') . "\n\n";
    
    if ($analysisType !== 'political') {
        echo "PROBLEM: analysis_type is not set to 'political', so the view will default to 'professional' and won't show political sections!\n";
        echo "Fixing by updating the analysis_type...\n";
        
        $aiAnalysis['analysis_type'] = 'political';
        $analysis->update(['ai_analysis' => $aiAnalysis]);
        
        echo "Fixed! Analysis type is now: " . ($analysis->ai_analysis['analysis_type'] ?? 'NOT SET') . "\n";
    } else {
        echo "Analysis type is correctly set to 'political'.\n";
    }
} else {
    echo "Analysis not found or no AI analysis data\n";
}
