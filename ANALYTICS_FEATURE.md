# Analytics Tracking System

This document describes the comprehensive analytics tracking system implemented for the NUJUM AI prediction application.

## Overview

The analytics system tracks detailed usage metrics for every AI analysis performed, including token consumption, costs, performance metrics, and user behavior patterns.

## Features

### 1. **Token Usage Tracking**
- **Input Tokens**: Estimated based on text length (1 token ≈ 4 characters)
- **Output Tokens**: Calculated from AI response length
- **Total Tokens**: Sum of input and output tokens
- **Token Efficiency**: Analysis of token usage patterns

### 2. **Cost Analytics**
- **Per-Analysis Cost**: Based on Gemini API pricing
- **Input Cost**: $0.00025 per 1K tokens
- **Output Cost**: $0.0005 per 1K tokens
- **Total Cost Tracking**: Cumulative costs over time periods
- **Cost Projections**: Monthly/quarterly cost estimates

### 3. **Performance Metrics**
- **API Response Time**: Time taken for Gemini API to respond
- **Total Processing Time**: End-to-end analysis completion time
- **Success Rate**: Percentage of successful analyses
- **Retry Attempts**: Number of retries and reasons
- **Error Tracking**: Detailed error logging and categorization

### 4. **Content Analysis**
- **Input Text Length**: Character count of user input
- **Scraped URLs**: Number of source URLs processed
- **File Uploads**: Count and total size of uploaded files
- **Analysis Types**: Breakdown by prediction horizon and type

### 5. **User Behavior Analytics**
- **Usage Frequency**: Analysis patterns per user
- **Peak Usage Times**: When users are most active
- **Feature Utilization**: Which features are used most
- **User Segmentation**: Usage patterns by user type

## Database Schema

### `analysis_analytics` Table

```sql
CREATE TABLE analysis_analytics (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    prediction_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    
    -- Token Usage
    input_tokens INT DEFAULT 0,
    output_tokens INT DEFAULT 0,
    total_tokens INT DEFAULT 0,
    
    -- Cost Tracking
    estimated_cost DECIMAL(10,6) DEFAULT 0.00,
    cost_currency VARCHAR(3) DEFAULT 'USD',
    
    -- Performance Metrics
    api_response_time DECIMAL(8,4) DEFAULT 0.0000,
    total_processing_time DECIMAL(8,4) DEFAULT 0.0000,
    retry_attempts INT DEFAULT 0,
    retry_reason VARCHAR(255) NULL,
    
    -- API Details
    model_used VARCHAR(255) DEFAULT 'gemini-2.5-flash',
    api_endpoint VARCHAR(500) NOT NULL,
    http_status_code INT NULL,
    api_error_message TEXT NULL,
    
    -- Content Analysis
    input_text_length INT DEFAULT 0,
    scraped_urls_count INT DEFAULT 0,
    successful_scrapes INT DEFAULT 0,
    uploaded_files_count INT DEFAULT 0,
    total_file_size_bytes INT DEFAULT 0,
    
    -- User Context
    user_agent VARCHAR(500) NULL,
    ip_address VARCHAR(45) NULL,
    analysis_type VARCHAR(255) DEFAULT 'prediction-analysis',
    prediction_horizon VARCHAR(255) NULL,
    
    -- Timestamps
    analysis_started_at TIMESTAMP NOT NULL,
    analysis_completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    -- Indexes
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_prediction (prediction_id),
    INDEX idx_analysis_type (analysis_type),
    INDEX idx_created_at (created_at),
    
    -- Foreign Keys
    FOREIGN KEY (prediction_id) REFERENCES predictions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Implementation Components

### 1. **Models**
- `AnalysisAnalytics`: Main analytics model with relationships and helper methods
- `Prediction`: Enhanced with analytics relationship
- `User`: Enhanced with analytics relationship

### 2. **Services**
- `AnalyticsService`: Core analytics logic and data aggregation
- `GeminiService`: Enhanced with analytics tracking integration

### 3. **Controllers**
- `AnalyticsController`: Admin analytics dashboard and data export
- `PredictionController`: Enhanced with analytics tracking during analysis

### 4. **Views**
- `admin.analytics`: Comprehensive admin analytics dashboard
- `predictions.analytics`: User-specific analytics view

## Usage Examples

### Starting Analytics Tracking

```php
// In PredictionController
$analytics = $this->analyticsService->startAnalysis($prediction, [
    'text' => $combinedInputData,
    'source_urls' => $sourceUrls,
    'uploaded_files' => $uploadedFiles,
    'analysis_type' => 'prediction-analysis',
    'prediction_horizon' => $request->prediction_horizon
]);
```

### Updating with API Response

```php
// In GeminiService
$this->updateAnalyticsWithApiResponse($analytics, $response, $apiResponseTime);
```

### Completing Analysis

```php
// In PredictionController
$this->analyticsService->completeAnalysis($analytics, [
    'total_processing_time' => $finalProcessingTime
]);
```

### Retrieving Analytics Data

```php
// System-wide analytics
$systemAnalytics = $this->analyticsService->getSystemAnalytics($startDate, $endDate);

// User-specific analytics
$userAnalytics = $this->analyticsService->getUserAnalytics($user, $startDate, $endDate);
```

## Analytics Dashboard Features

### Admin Dashboard (`/admin/analytics`)
- **Key Metrics**: Total analyses, tokens, costs, success rate
- **Performance Metrics**: Processing times, active users, averages
- **User Breakdown**: Top users by usage, analysis type distribution
- **Trends**: Daily usage patterns, cost trends over time
- **Export**: CSV export functionality for data analysis

### User Dashboard (`/predictions/analytics`)
- **Personal Metrics**: Individual usage statistics
- **Performance Tracking**: Personal processing times and success rates
- **Usage Patterns**: Analysis type and horizon breakdown
- **Cost Tracking**: Personal cost analysis and trends

## Configuration

### Environment Variables

```env
# Analytics Configuration
ANALYTICS_ENABLED=true
ANALYTICS_RETENTION_DAYS=365
ANALYTICS_COST_CALCULATION=true
```

### Service Configuration

```php
// config/services.php
'analytics' => [
    'enabled' => env('ANALYTICS_ENABLED', true),
    'retention_days' => env('ANALYTICS_RETENTION_DAYS', 365),
    'cost_calculation' => env('ANALYTICS_COST_CALCULATION', true),
    'token_estimation_ratio' => env('ANALYTICS_TOKEN_RATIO', 4), // chars per token
],
```

## Benefits

### 1. **Cost Management**
- Track actual API costs vs. estimates
- Identify cost optimization opportunities
- Budget planning and forecasting

### 2. **Performance Optimization**
- Monitor API response times
- Identify bottlenecks in processing
- Optimize user experience

### 3. **User Insights**
- Understand usage patterns
- Identify popular features
- User behavior analysis

### 4. **Business Intelligence**
- Usage trends and patterns
- Resource allocation decisions
- Growth planning and forecasting

### 5. **Compliance & Billing**
- Detailed usage records
- Cost allocation per user
- Audit trail for compliance

## Future Enhancements

### 1. **Advanced Analytics**
- Machine learning for usage prediction
- Anomaly detection in usage patterns
- Predictive cost modeling

### 2. **Real-time Monitoring**
- Live dashboard updates
- Real-time alerts for issues
- Performance monitoring

### 3. **Advanced Reporting**
- Custom report builder
- Scheduled report generation
- Data visualization enhancements

### 4. **Integration Features**
- Export to external analytics tools
- API endpoints for third-party integration
- Webhook notifications for events

## Security Considerations

- **Data Privacy**: User data is anonymized in aggregate reports
- **Access Control**: Analytics access restricted to appropriate user roles
- **Data Retention**: Configurable data retention policies
- **Audit Logging**: All analytics access is logged for security

## Performance Considerations

- **Database Indexing**: Optimized indexes for fast query performance
- **Caching**: Strategic caching of frequently accessed analytics data
- **Query Optimization**: Efficient queries for large datasets
- **Background Processing**: Heavy analytics calculations run asynchronously

This analytics system provides comprehensive insights into AI analysis usage, enabling data-driven decisions for optimization, cost management, and user experience improvements.
