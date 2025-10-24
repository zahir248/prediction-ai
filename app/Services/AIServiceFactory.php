<?php

namespace App\Services;

use App\Models\SystemSetting;

class AIServiceFactory
{
    /**
     * Get the appropriate AI service based on system settings
     */
    public static function create(): AIServiceInterface
    {
        $provider = SystemSetting::get('ai_provider', 'gemini');
        
        switch ($provider) {
            case 'chatgpt':
                return app(ChatGPTService::class);
            case 'gemini':
            default:
                return app(GeminiService::class);
        }
    }

    /**
     * Get the current AI provider
     */
    public static function getCurrentProvider(): string
    {
        return SystemSetting::get('ai_provider', 'gemini');
    }

    /**
     * Set the AI provider
     */
    public static function setProvider(string $provider): void
    {
        if (!in_array($provider, ['gemini', 'chatgpt'])) {
            throw new \InvalidArgumentException('Invalid AI provider. Must be "gemini" or "chatgpt".');
        }
        
        SystemSetting::set('ai_provider', $provider, 'AI provider for predictions');
    }

    /**
     * Get available providers
     */
    public static function getAvailableProviders(): array
    {
        return [
            'gemini' => [
                'name' => 'Google Gemini',
                'description' => 'Google\'s advanced AI model for comprehensive analysis',
                'model' => 'gemini-2.5-flash'
            ],
            'chatgpt' => [
                'name' => 'OpenAI ChatGPT',
                'description' => 'OpenAI\'s GPT-4o model for advanced predictions',
                'model' => 'gpt-4o'
            ]
        ];
    }

    /**
     * Test the current AI provider connection
     */
    public static function testCurrentProvider(): array
    {
        try {
            $service = self::create();
            return $service->testConnection();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to test AI provider: ' . $e->getMessage(),
                'status_code' => 0
            ];
        }
    }
}
