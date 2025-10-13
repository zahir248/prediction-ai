<?php

namespace App\Services;

interface AIServiceInterface
{
    /**
     * Analyze text and provide predictions
     */
    public function analyzeText($text, $analysisType = 'prediction-analysis', $sourceUrls = null, $predictionHorizon = null, $analytics = null, $target = null);

    /**
     * Test the API connection
     */
    public function testConnection();

    /**
     * Get available models
     */
    public function getAvailableModels();
}
