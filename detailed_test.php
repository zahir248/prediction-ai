<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Detailed ChatGPT Test...\n";

try {
    $chatgpt = app('App\Services\ChatGPTService');
    
    echo "Service created successfully\n";
    
    // Test with a very simple prompt
    echo "Testing with simple prompt...\n";
    $result = $chatgpt->analyzeText('Hello, this is a test.', 'test');
    
    echo "Result type: " . gettype($result) . "\n";
    
    if (is_array($result)) {
        echo "Result keys: " . implode(', ', array_keys($result)) . "\n";
        
        if (isset($result['status']) && $result['status'] === 'error') {
            echo "Error status detected\n";
            echo "Error message: " . ($result['note'] ?? 'No error message') . "\n";
        } else {
            echo "Success! Result has " . count($result) . " fields\n";
        }
    } else {
        echo "Result: " . substr($result, 0, 100) . "...\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
