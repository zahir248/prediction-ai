# Gemini API Integration Setup

This guide explains how to set up and use Google's Gemini API in your Laravel prediction application.

## What is Gemini?

Google Gemini is a family of multimodal AI models that can understand and generate text, images, and other types of content. It's designed to be helpful, accurate, and safe for a wide range of applications.

## Setup Instructions

### 1. Get a Gemini API Key

1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy the generated API key

### 2. Configure Environment Variables

Add your Gemini API key to your `.env` file:

```env
GEMINI_API_KEY=your_actual_api_key_here
GEMINI_SSL_VERIFY=false
```

**Note**: Set `GEMINI_SSL_VERIFY=false` if you encounter SSL certificate issues on Windows or development environments.

### 3. API Usage Limits

- **Free Tier**: 15 requests per minute
- **Paid Tier**: Higher limits available
- **Model**: Uses `gemini-2.0-flash` (latest Gemini model)

## How It Works

### API Integration

The application uses `GeminiService` for all AI predictions. The service:

1. **Sends structured prompts** to Gemini API
2. **Requests JSON responses** for consistent data format
3. **Falls back to intelligent analysis** if API is unavailable
4. **Processes responses** into structured prediction data

### Prompt Engineering

The service sends carefully crafted prompts that request:
- Executive summary
- Current situation analysis
- Key factors identification
- Future predictions with timelines
- Policy implications
- Risk assessment
- Strategic recommendations

### Pure Gemini Integration

The system is designed to work exclusively with Google's Gemini API:
- No fallback systems or local analysis
- Direct integration with Gemini Pro model
- Structured JSON responses for consistent data
- High-quality AI-powered predictions

## Testing the Integration

### 1. Test API Connection

Visit `/test-api` endpoint to verify:
- Gemini API connectivity
- Response processing
- API integration status

### 2. Create a Prediction

1. Go to `/predictions/create`
2. Enter a topic and input data
3. Submit for AI analysis
4. View results with Gemini-powered insights

## Benefits of Gemini

### Advantages
- **High-quality responses** from Google's latest AI model
- **Structured output** for consistent data format
- **Comprehensive analysis** covering multiple aspects
- **Pure AI integration** with no fallback complexity
- **Free tier available** for testing and development

### Features
- **Future-focused predictions** with specific timelines
- **Risk assessment** with mitigation strategies
- **Policy implications** analysis
- **Strategic recommendations** for action
- **Executive summaries** for quick insights

## Troubleshooting

### Common Issues

1. **API Key Not Configured**
   - Ensure `GEMINI_API_KEY` is set in `.env`
   - Check for typos in the environment variable

2. **SSL Certificate Issues**
   - Set `GEMINI_SSL_VERIFY=false` in `.env` for development
   - Download CA certificates: `curl -o cacert.pem https://curl.se/ca/cacert.pem`
   - Common on Windows systems

3. **Rate Limiting**
   - Free tier: 15 requests per minute
   - Consider upgrading for higher usage

4. **API Unavailable**
   - System will return error messages
   - Check Google AI Studio status
   - Verify API key configuration

5. **Response Format Issues**
   - Service handles both JSON and text responses
   - Structured parsing ensures consistent output

### Debug Information

The system logs detailed information about:
- API requests and responses
- Processing times and model usage
- Gemini API integration status
- Error conditions and resolutions

## System Architecture

The Gemini-powered system includes:

- **Service replacement** with minimal code changes
- **Enhanced prompt engineering** for better results
- **Pure AI integration** with no fallback complexity
- **Better response processing** for structured data
- **Maintained compatibility** with existing features

## Next Steps

1. **Test the integration** with sample predictions
2. **Monitor API usage** and performance
3. **Customize prompts** for specific use cases
4. **Explore advanced features** like temperature and token controls
5. **Consider paid tier** for production applications

## Support

For issues or questions:
- Check the application logs for detailed error information
- Verify API key configuration
- Test with the `/test-api` endpoint
- Review Google AI Studio documentation

---

**Note**: This integration provides a robust, production-ready AI prediction system with Google's latest AI technology with pure Gemini API integration.
