<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\PredictionController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfidenceScoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_confidence_score_extraction_from_numeric()
    {
        $controller = new PredictionController();
        
        $result = [
            'confidence_score' => 0.85,
            'title' => 'Test Analysis'
        ];
        
        $confidence = $this->invokeMethod($controller, 'extractConfidenceScore', [$result, 'prediction-analysis']);
        
        $this->assertEquals(0.85, $confidence);
    }

    public function test_confidence_score_extraction_from_text()
    {
        $controller = new PredictionController();
        
        $result = [
            'confidence_level' => 'High (85-90%)',
            'title' => 'Test Analysis'
        ];
        
        $confidence = $this->invokeMethod($controller, 'extractConfidenceScore', [$result, 'prediction-analysis']);
        
        $this->assertEquals(0.875, $confidence); // Average of 85% and 90%
    }

    public function test_confidence_score_extraction_from_text_description()
    {
        $controller = new PredictionController();
        
        $result = [
            'confidence_level' => 'Very High',
            'title' => 'Test Analysis'
        ];
        
        $confidence = $this->invokeMethod($controller, 'extractConfidenceScore', [$result, 'prediction-analysis']);
        
        $this->assertEquals(0.95, $confidence);
    }

    public function test_confidence_score_estimation_from_result_quality()
    {
        $controller = new PredictionController();
        
        $result = [
            'title' => 'Test Analysis',
            'executive_summary' => 'This is a comprehensive summary',
            'predictions' => [
                'Detailed prediction 1 with more than 20 characters',
                'Detailed prediction 2 with more than 20 characters',
                'Detailed prediction 3 with more than 20 characters'
            ],
            'key_factors' => ['Factor 1', 'Factor 2'],
            'risk_assessment' => ['Risk 1'],
            'recommendations' => ['Recommendation 1'],
            'policy_implications' => ['Implication 1']
        ];
        
        $confidence = $this->invokeMethod($controller, 'extractConfidenceScore', [$result, 'prediction-analysis']);
        
        // Base confidence (0.75) + essential fields (0.10) + detailed fields (0.08) + specific predictions (0.05)
        $expectedConfidence = 0.75 + 0.10 + 0.08 + 0.05;
        $this->assertEquals($expectedConfidence, $confidence);
    }

    public function test_confidence_score_fallback_for_invalid_result()
    {
        $controller = new PredictionController();
        
        $result = 'not an array';
        
        $confidence = $this->invokeMethod($controller, 'extractConfidenceScore', [$result, 'prediction-analysis']);
        
        $this->assertEquals(0.75, $confidence);
    }

    /**
     * Call protected/private method of a class.
     */
    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
