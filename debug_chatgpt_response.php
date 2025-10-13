<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debugging ChatGPT Response...\n";

try {
    $chatgpt = app('App\Services\ChatGPTService');
    
    // Test with a very simple prompt to see the raw response
    echo "Testing with simple prompt...\n";
    $result = $chatgpt->analyzeText('Hello, this is a test. Please respond with a simple JSON object.', 'test');
    
    echo "Result type: " . gettype($result) . "\n";
    
    if (isset($result['raw_response'])) {
        echo "Raw response length: " . strlen($result['raw_response']) . "\n";
        echo "Raw response content: " . $result['raw_response'] . "\n";
    }
    
    if (isset($result['api_metadata'])) {
        echo "API metadata: " . json_encode($result['api_metadata']) . "\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
