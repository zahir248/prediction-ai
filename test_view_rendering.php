<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SocialMediaAnalysis;

$analysis = SocialMediaAnalysis::find(102);

if ($analysis && !empty($analysis->ai_analysis)) {
    $aiAnalysis = $analysis->ai_analysis;
    
    // Simulate the view logic
    $analysisType = $aiAnalysis['analysis_type'] ?? 'professional';
    
    echo "=== VIEW RENDERING TEST ===\n";
    echo "Analysis Type from data: {$analysisType}\n\n";
    
    if ($analysisType === 'political') {
        echo "✓ Will show POLITICAL sections\n\n";
        
        echo "Sections that should be displayed:\n";
        echo "1. Political Profile: " . (isset($aiAnalysis['political_profile']) ? 'YES' : 'NO') . "\n";
        echo "2. Political Engagement Indicators: " . (isset($aiAnalysis['political_engagement_indicators']) ? 'YES' : 'NO') . "\n";
        echo "3. Political Alignment Indicators: " . (isset($aiAnalysis['political_alignment_indicators']) ? 'YES' : 'NO') . "\n";
        echo "4. Political Growth Signals: " . (isset($aiAnalysis['political_growth_signals']) ? 'YES' : 'NO') . "\n";
        echo "5. Political Communication Style: " . (isset($aiAnalysis['political_communication_style']) ? 'YES' : 'NO') . "\n";
        echo "6. Political Career Profile: " . (isset($aiAnalysis['political_career_profile']) ? 'YES' : 'NO') . "\n";
        echo "7. Activity Overview: " . (isset($aiAnalysis['activity_overview']) ? 'YES' : 'NO') . "\n";
        
        // Check if spider graphs can be rendered
        echo "\n=== SPIDER GRAPH DATA CHECK ===\n";
        
        // Political Engagement Indicators
        if (isset($aiAnalysis['political_engagement_indicators'])) {
            $data = $aiAnalysis['political_engagement_indicators'];
            echo "Political Engagement Indicators:\n";
            echo "  - consistency_score: " . (isset($data['consistency_score']) ? $data['consistency_score'] : 'MISSING') . "\n";
            echo "  - activism_level_score: " . (isset($data['activism_level_score']) ? $data['activism_level_score'] : 'MISSING') . "\n";
            echo "  - commitment_score: " . (isset($data['commitment_score']) ? $data['commitment_score'] : 'MISSING') . "\n";
            echo "  - advocacy_score: " . (isset($data['advocacy_score']) ? $data['advocacy_score'] : 'MISSING') . "\n";
            echo "  - influence_score: " . (isset($data['influence_score']) ? $data['influence_score'] : 'MISSING') . "\n";
        }
        
        // Political Alignment Indicators
        if (isset($aiAnalysis['political_alignment_indicators'])) {
            $data = $aiAnalysis['political_alignment_indicators'];
            echo "\nPolitical Alignment Indicators:\n";
            echo "  - ideological_alignment_level: " . (isset($data['ideological_alignment_level']) ? $data['ideological_alignment_level'] : 'MISSING') . "\n";
            echo "  - party_alignment_level: " . (isset($data['party_alignment_level']) ? $data['party_alignment_level'] : 'MISSING') . "\n";
            echo "  - value_consistency_level: " . (isset($data['value_consistency_level']) ? $data['value_consistency_level'] : 'MISSING') . "\n";
        }
        
        // Political Growth Signals
        if (isset($aiAnalysis['political_growth_signals'])) {
            $data = $aiAnalysis['political_growth_signals'];
            echo "\nPolitical Growth Signals:\n";
            echo "  - political_development_level: " . (isset($data['political_development_level']) ? $data['political_development_level'] : 'MISSING') . "\n";
            echo "  - influence_growth_level: " . (isset($data['influence_growth_level']) ? $data['influence_growth_level'] : 'MISSING') . "\n";
            echo "  - network_expansion_level: " . (isset($data['network_expansion_level']) ? $data['network_expansion_level'] : 'MISSING') . "\n";
        }
        
        // Political Communication Style
        if (isset($aiAnalysis['political_communication_style'])) {
            $data = $aiAnalysis['political_communication_style'];
            echo "\nPolitical Communication Style:\n";
            echo "  - persuasiveness_score: " . (isset($data['persuasiveness_score']) ? $data['persuasiveness_score'] : 'MISSING') . "\n";
            echo "  - authenticity_score: " . (isset($data['authenticity_score']) ? $data['authenticity_score'] : 'MISSING') . "\n";
            echo "  - polarization_level: " . (isset($data['polarization_level']) ? $data['polarization_level'] : 'MISSING') . "\n";
            echo "  - diplomacy_score: " . (isset($data['diplomacy_score']) ? $data['diplomacy_score'] : 'MISSING') . "\n";
            echo "  - emotional_appeal_score: " . (isset($data['emotional_appeal_score']) ? $data['emotional_appeal_score'] : 'MISSING') . "\n";
        }
    } else {
        echo "✗ Will show PROFESSIONAL sections (wrong!)\n";
        echo "This means analysis_type is not set to 'political'\n";
    }
} else {
    echo "Analysis not found\n";
}
