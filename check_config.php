<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking OpenAI Configuration...\n";

$apiKey = config('services.chatgpt.api_key');
echo "OpenAI API Key configured: " . ($apiKey ? 'YES (length: ' . strlen($apiKey) . ')' : 'NO') . "\n";

if ($apiKey) {
    echo "Key starts with sk-: " . (str_starts_with($apiKey, 'sk-') ? 'YES' : 'NO') . "\n";
    echo "Key preview: " . substr($apiKey, 0, 10) . "...\n";
}

echo "Model: " . config('services.chatgpt.model', 'gpt-4o') . "\n";
echo "Max Tokens: " . config('services.chatgpt.max_tokens', 4000) . "\n";
echo "Temperature: " . config('services.chatgpt.temperature', 0.7) . "\n";
echo "Timeout: " . config('services.chatgpt.timeout', 300) . "\n";
