# ChatGPT API Integration

This document explains how to set up and use OpenAI's ChatGPT API alongside Google Gemini in your Laravel prediction application.

## Overview

The system now supports two AI providers:
- **Google Gemini** (default) - Using `gemini-2.5-flash` model
- **OpenAI ChatGPT** - Using `gpt-4o` model

## Setup Instructions

### 1. Get an OpenAI API Key

1. Go to [OpenAI Platform](https://platform.openai.com/api-keys)
2. Sign in with your OpenAI account
3. Click "Create new secret key"
4. Copy the generated API key (starts with `sk-`)

### 2. Configure Environment Variables

Add your OpenAI API key to your `.env` file:

```env
# OpenAI ChatGPT Configuration
OPENAI_API_KEY=your_actual_api_key_here
OPENAI_MODEL=gpt-4o
OPENAI_MAX_TOKENS=4000
OPENAI_TEMPERATURE=0.7
OPENAI_TIMEOUT=300
```

### 3. Database Setup

The system automatically creates a `system_settings` table to store AI provider preferences. Run migrations if needed:

```bash
php artisan migrate
```

## How to Use

### Switching AI Providers

1. **Via Superadmin Settings:**
   - Go to `/superadmin/settings`
   - Select your preferred AI provider from the dropdown
   - Click "Save Provider"
   - Use "Test Connection" to verify the provider works

2. **Via Code:**
   ```php
   use App\Services\AIServiceFactory;
   
   // Set provider
   AIServiceFactory::setProvider('chatgpt'); // or 'gemini'
   
   // Get current provider
   $currentProvider = AIServiceFactory::getCurrentProvider();
   
   // Create AI service instance
   $aiService = AIServiceFactory::create();
   ```

### Testing the Integration

Visit `/test-ai-providers` to test both AI providers and see their connection status.

## API Usage Limits

### OpenAI ChatGPT
- **Free Tier**: Limited requests per month
- **Paid Tier**: Higher limits based on your plan
- **Model**: Uses `gpt-4o` (latest GPT-4 model)

### Google Gemini
- **Free Tier**: 15 requests per minute
- **Paid Tier**: Higher limits available
- **Model**: Uses `gemini-2.5-flash`

## Features

Both AI providers offer:
- ✅ Comprehensive prediction analysis
- ✅ Source URL integration
- ✅ File upload processing
- ✅ Analytics tracking
- ✅ Fallback responses
- ✅ Connection testing

## Architecture

The system uses a factory pattern to switch between AI providers:

```
AIServiceFactory
├── GeminiService (implements AIServiceInterface)
└── ChatGPTService (implements AIServiceInterface)
```

### Key Components

1. **AIServiceInterface** - Common interface for all AI services
2. **AIServiceFactory** - Factory to create appropriate AI service
3. **SystemSetting** - Model to store AI provider preference
4. **SuperAdminController** - Handles AI provider settings
5. **PredictionController** - Uses AI service factory for predictions

## Configuration Options

### Environment Variables

```env
# Gemini (existing)
GEMINI_API_KEY=your_gemini_api_key
GEMINI_SSL_VERIFY=true

# ChatGPT (new)
OPENAI_API_KEY=your_openai_api_key
OPENAI_MODEL=gpt-4o
OPENAI_MAX_TOKENS=4000
OPENAI_TEMPERATURE=0.7
OPENAI_TIMEOUT=300
```

### Database Settings

The system stores AI provider preference in the `system_settings` table:
- `key`: 'ai_provider'
- `value`: 'gemini' or 'chatgpt'
- `description`: 'AI provider for predictions'

## Troubleshooting

### Common Issues

1. **API Key Not Working**
   - Verify the API key format (ChatGPT keys start with `sk-`)
   - Check if the API key has sufficient credits
   - Ensure the key has the correct permissions

2. **Connection Timeouts**
   - Increase `OPENAI_TIMEOUT` in your `.env` file
   - Check your internet connection
   - Verify OpenAI service status

3. **Rate Limiting**
   - Both providers have rate limits
   - The system includes built-in rate limiting for Gemini
   - Consider upgrading your OpenAI plan for higher limits

### Testing Commands

```bash
# Test both providers
php artisan tinker
>>> \App\Services\AIServiceFactory::testCurrentProvider();

# Check current provider
>>> \App\Services\AIServiceFactory::getCurrentProvider();

# Switch provider
>>> \App\Services\AIServiceFactory::setProvider('chatgpt');
```

## Security Notes

- Store API keys securely in environment variables
- Never commit API keys to version control
- Use different API keys for development and production
- Monitor API usage and costs regularly

## Support

For issues with:
- **Gemini API**: Check Google AI Studio documentation
- **ChatGPT API**: Check OpenAI Platform documentation
- **System Integration**: Check application logs in `storage/logs/`
