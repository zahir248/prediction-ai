<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Prediction;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Test that the target field can be set and retrieved.
     */
    public function test_prediction_target_field(): void
    {
        $prediction = new Prediction();
        $target = 'Test Company XYZ';
        
        $prediction->target = $target;
        
        $this->assertEquals($target, $prediction->target);
    }
}
