<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing AI Providers...\n";

// Test Gemini
try {
    $gemini = app('App\Services\GeminiService');
    $geminiResult = $gemini->testConnection();
    echo "Gemini Service: " . ($geminiResult['success'] ? 'OK' : 'FAILED') . "\n";
    if (!$geminiResult['success']) {
        echo "  Error: " . $geminiResult['message'] . "\n";
    }
} catch (Exception $e) {
    echo "Gemini Error: " . $e->getMessage() . "\n";
}

// Test ChatGPT
try {
    $chatgpt = app('App\Services\ChatGPTService');
    $chatgptResult = $chatgpt->testConnection();
    echo "ChatGPT Service: " . ($chatgptResult['success'] ? 'OK' : 'FAILED') . "\n";
    if (!$chatgptResult['success']) {
        echo "  Error: " . $chatgptResult['message'] . "\n";
    }
} catch (Exception $e) {
    echo "ChatGPT Error: " . $e->getMessage() . "\n";
}

// Test AI Factory
try {
    $factory = App\Services\AIServiceFactory::create();
    $factoryResult = $factory->testConnection();
    echo "AI Factory: " . ($factoryResult['success'] ? 'OK' : 'FAILED') . "\n";
    echo "Current Provider: " . App\Services\AIServiceFactory::getCurrentProvider() . "\n";
    if (!$factoryResult['success']) {
        echo "  Error: " . $factoryResult['message'] . "\n";
    }
} catch (Exception $e) {
    echo "Factory Error: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
