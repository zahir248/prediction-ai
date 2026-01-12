<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SocialMediaAnalysis;

$analysis = SocialMediaAnalysis::find(102);

if ($analysis && !empty($analysis->ai_analysis)) {
    $aiAnalysis = $analysis->ai_analysis;
    
    echo "=== PROFESSIONAL ANALYSIS CHECK ===\n";
    $analysisType = $aiAnalysis['analysis_type'] ?? 'NOT SET';
    echo "Analysis Type: {$analysisType}\n";
    echo "Will show professional sections: " . ($analysisType === 'professional' ? 'YES' : 'NO') . "\n\n";
    
    echo "=== SECTIONS CHECK ===\n";
    echo "Has professional_footprint: " . (isset($aiAnalysis['professional_footprint']) ? 'YES' : 'NO') . "\n";
    echo "Has work_ethic_indicators: " . (isset($aiAnalysis['work_ethic_indicators']) ? 'YES' : 'NO') . "\n";
    echo "Has cultural_fit_indicators: " . (isset($aiAnalysis['cultural_fit_indicators']) ? 'YES' : 'NO') . "\n";
    echo "Has professional_growth_signals: " . (isset($aiAnalysis['professional_growth_signals']) ? 'YES' : 'NO') . "\n";
    echo "Has personality_communication: " . (isset($aiAnalysis['personality_communication']) ? 'YES' : 'NO') . "\n";
    echo "Has career_profile: " . (isset($aiAnalysis['career_profile']) ? 'YES' : 'NO') . "\n";
    echo "Has activity_overview: " . (isset($aiAnalysis['activity_overview']) ? 'YES' : 'NO') . "\n\n";
    
    // Check spider graph data
    echo "=== SPIDER GRAPH DATA CHECK ===\n";
    
    // Work Ethic Indicators
    if (isset($aiAnalysis['work_ethic_indicators'])) {
        $data = $aiAnalysis['work_ethic_indicators'];
        echo "Work Ethic Indicators:\n";
        echo "  - consistency_score: " . (isset($data['consistency_score']) ? $data['consistency_score'] : 'MISSING') . "\n";
        echo "  - follow_through_score: " . (isset($data['follow_through_score']) ? $data['follow_through_score'] : 'MISSING') . "\n";
        echo "  - collaboration_score: " . (isset($data['collaboration_score']) ? $data['collaboration_score'] : 'MISSING') . "\n";
        echo "  - initiative_score: " . (isset($data['initiative_score']) ? $data['initiative_score'] : 'MISSING') . "\n";
        echo "  - productivity_score: " . (isset($data['productivity_score']) ? $data['productivity_score'] : 'MISSING') . "\n";
    }
    
    // Cultural Fit Indicators
    if (isset($aiAnalysis['cultural_fit_indicators'])) {
        $data = $aiAnalysis['cultural_fit_indicators'];
        echo "\nCultural Fit Indicators:\n";
        echo "  - value_alignment_level: " . (isset($data['value_alignment_level']) ? $data['value_alignment_level'] : 'MISSING') . "\n";
        echo "  - teamwork_ethos_level: " . (isset($data['teamwork_ethos_level']) ? $data['teamwork_ethos_level'] : 'MISSING') . "\n";
        echo "  - innovation_mindset_level: " . (isset($data['innovation_mindset_level']) ? $data['innovation_mindset_level'] : 'MISSING') . "\n";
    }
    
    // Professional Growth Signals
    if (isset($aiAnalysis['professional_growth_signals'])) {
        $data = $aiAnalysis['professional_growth_signals'];
        echo "\nProfessional Growth Signals:\n";
        echo "  - learning_initiative_level: " . (isset($data['learning_initiative_level']) ? $data['learning_initiative_level'] : 'MISSING') . "\n";
        echo "  - skill_development_level: " . (isset($data['skill_development_level']) ? $data['skill_development_level'] : 'MISSING') . "\n";
        echo "  - mentorship_activity_level: " . (isset($data['mentorship_activity_level']) ? $data['mentorship_activity_level'] : 'MISSING') . "\n";
    }
    
    // Personality & Communication
    if (isset($aiAnalysis['personality_communication'])) {
        $data = $aiAnalysis['personality_communication'];
        echo "\nPersonality & Communication:\n";
        echo "  - openness_score: " . (isset($data['openness_score']) ? $data['openness_score'] : 'MISSING') . "\n";
        echo "  - conscientiousness_score: " . (isset($data['conscientiousness_score']) ? $data['conscientiousness_score'] : 'MISSING') . "\n";
        echo "  - extraversion_score: " . (isset($data['extraversion_score']) ? $data['extraversion_score'] : 'MISSING') . "\n";
        echo "  - agreeableness_score: " . (isset($data['agreeableness_score']) ? $data['agreeableness_score'] : 'MISSING') . "\n";
        echo "  - neuroticism_score: " . (isset($data['neuroticism_score']) ? $data['neuroticism_score'] : 'MISSING') . "\n";
    }
} else {
    echo "Analysis not found\n";
}
