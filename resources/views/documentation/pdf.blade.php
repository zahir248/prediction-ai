<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Prediction System Documentation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 28px;
        }
        
        .header .subtitle {
            color: #7f8c8d;
            margin: 10px 0 0 0;
            font-size: 16px;
        }
        
        .meta-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #3498db;
            margin-bottom: 30px;
        }
        
        .meta-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        
        h3 {
            color: #34495e;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        
        h4 {
            color: #7f8c8d;
            margin-top: 20px;
            margin-bottom: 8px;
        }
        
        .code-block {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            white-space: pre-wrap;
            overflow-x: auto;
        }
        
        .json-structure {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            white-space: pre-wrap;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .instruction-list {
            background-color: #f8f9fa;
            border-left: 4px solid #27ae60;
            padding: 15px;
            margin: 10px 0;
        }
        
        .instruction-list ol {
            margin: 0;
            padding-left: 20px;
        }
        
        .instruction-list li {
            margin-bottom: 5px;
        }
        
        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
        }
        
        .note {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        li {
            margin-bottom: 5px;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AI Prediction System Documentation</h1>
        <p class="subtitle">Complete AI Prompt Structure and Implementation Guide</p>
    </div>
    
    <div class="meta-info">
        <p><strong>Generated:</strong> {{ $generatedAt }}</p>
        <p><strong>Version:</strong> {{ $version }}</p>
        <p><strong>System:</strong> Laravel Prediction AI Application</p>
    </div>
    
    <div class="section">
        <h2>Overview</h2>
        <p>This documentation provides a comprehensive overview of the AI prompt structure used in the prediction system. The system uses Google's Gemini AI to generate detailed prediction analyses based on user input, optional target focus, prediction horizons, and external source integration.</p>
    </div>
    
    <div class="section">
        <h2>Complete AI Prompt Structure</h2>
        <p>This is the exact, full prompt that gets sent to the AI system:</p>
        <div class="code-block">You are an expert AI prediction analyst specializing in comprehensive future forecasting and strategic analysis. Please analyze the following text and provide a detailed, professional prediction analysis similar to high-quality consulting reports.

Text to analyze: {USER_INPUT_TEXT}

TARGET: {TARGET_IF_SPECIFIED}
Focus analysis on how predictions, risks, and implications affect {TARGET}.

HORIZON: {PREDICTION_HORIZON}
Tailor all predictions and assessments to this timeframe.

IMPORTANT: You have been provided with the following additional sources that contain relevant context, data, or background information:
- Source 1: {URL_1}
- Source 2: {URL_2}
- Source 3: {URL_3}
...

SCRAPING SUMMARY:
Total URLs provided: {TOTAL_URLS}
Successfully scraped: {SUCCESSFUL_SCRAPES}
Failed to scrape: {FAILED_SCRAPES}
Success rate: {SUCCESS_RATE}%

FAILED URLS (These could not be accessed due to anti-bot protection, server errors, or other issues):
- {FAILED_URL_1}: {ERROR_1} (HTTP {STATUS_CODE_1})
- {FAILED_URL_2}: {ERROR_2} (HTTP {STATUS_CODE_2})
...

Note: For failed URLs, rely on your existing knowledge about the topic or source.

ACTUAL CONTENT FROM SUCCESSFULLY SCRAPED SOURCES:
The following is the real content extracted from the accessible URLs. Use this actual data in your analysis:

=== SOURCE 1 ===
URL: {SOURCE_URL_1}
Title: {SOURCE_TITLE_1}
Content: {SOURCE_CONTENT_1}
Word Count: {WORD_COUNT_1}
==================

=== SOURCE 2 ===
URL: {SOURCE_URL_2}
Title: {SOURCE_TITLE_2}
Content: {SOURCE_CONTENT_2}
Word Count: {WORD_COUNT_2}
==================

SOURCE INTEGRATION:
1. Reference sources when supporting predictions
2. Use 'Source 1...', 'Source 2...' format
3. Include 'Source Analysis' section
4. Cite specific facts/numbers from sources

Provide analysis in this JSON structure:
{
  "title": "[Topic + Time Period + Focus]",
  "executive_summary": "[3-4 sentence summary of key predictions, risks, implications]",
  "prediction_horizon": "[Time period]",
  "current_situation": "[Current state and trends analysis]",
  "key_factors": [
    "[Factor 1: Specific, actionable factor]",
    "[Factor 2: Specific, actionable factor]",
    "[Factor 3: Specific, actionable factor]",
    "[Factor 4: Specific, actionable factor]",
    "[Factor 5: Specific, actionable factor]",
    "[Factor 6: Specific, actionable factor]"
  ],
  "predictions": [
    "[Prediction 1: Specific outcome with timeline]",
    "[Prediction 2: Specific outcome with timeline]",
    "[Prediction 3: Specific outcome with timeline]",
    "[Prediction 4: Specific outcome with timeline]",
    "[Prediction 5: Specific outcome with timeline]",
    "[Prediction 6: Specific outcome with timeline]",
    "[Prediction 7: Specific outcome with timeline]",
    "[Prediction 8: Specific outcome with timeline]",
    "[Prediction 9: Specific outcome with timeline]",
    "[Prediction 10: Specific outcome with timeline]"
  ],
  "risk_assessment": [
    {
      "risk": "[Risk description]",
      "level": "[Critical/High/Medium/Low]",
      "probability": "[Very Likely/Likely/Possible/Unlikely]",
      "mitigation": "[Mitigation strategy]"
    }
  ],
  "recommendations": [
    "[Specific, actionable recommendation]",
    "[Specific, actionable recommendation]",
    "[Specific, actionable recommendation]"
  ],
  "strategic_implications": [
    "[Strategic implication]",
    "[Strategic implication]",
    "[Strategic implication]"
  ],
  "confidence_level": "[High (90-95%)/Medium (75-89%)/Low (60-74%)]",
  "methodology": "[AI analysis approach, data sources, and validation methods]",
  "data_sources": [
    "[Data source with relevance]",
    "[Data source with relevance]"
  ],
  "assumptions": [
    "[Key assumption underlying predictions]",
    "[Key assumption underlying predictions]"
  ],
  "note": "[Important note about analysis limitations or key considerations]",
  "analysis_date": "[Current date in YYYY-MM-DD format]",
  "next_review": "[Recommended next review date]",
  "critical_timeline": "[Critical dates or milestones to watch]",
  "success_metrics": [
    "[How to measure success of predictions]",
    "[How to measure success of predictions]"
  ],
  "source_analysis": "[Detailed explanation of how each provided source influenced your analysis and predictions. Use specific examples and show direct connections between source information and conclusions.]"
}

INSTRUCTIONS:
1. Be specific and actionable
2. Include timelines for predictions
3. Focus on future outcomes
4. Provide realistic predictions
5. Structure risks by probability and impact
6. Make recommendations implementable
7. Include quantifiable metrics where possible
8. Consider opportunities and threats
9. Base analysis on logical reasoning
10. Ensure comprehensive and professional analysis
11. Cite sources using 'Source 1...', 'Source 2...'
12. Show connections between sources and predictions
13. Include source_analysis field
14. Use actual data and quotes from sources
15. Reference specific facts and numbers

Generate high-quality, professional prediction analysis suitable for executive decision-making.</div>
        <p>This is the complete prompt structure that gets dynamically constructed and sent to the AI system. The placeholder variables (like {USER_INPUT_TEXT}, {TARGET_IF_SPECIFIED}, etc.) are replaced with actual values during execution.</p>
    </div>
    
    <div class="section">
        <h2>Placeholder Variables</h2>
        <p>The following placeholder variables are dynamically replaced with actual values during execution:</p>
        <ul>
            <li><strong>{USER_INPUT_TEXT}</strong> - The text provided by the user for analysis</li>
            <li><strong>{TARGET_IF_SPECIFIED}</strong> - Optional target focus (e.g., "Software companies", "Healthcare industry")</li>
            <li><strong>{PREDICTION_HORIZON}</strong> - Time period for predictions (e.g., "Next 6 Months", "Next 12 Months")</li>
            <li><strong>{URL_1}, {URL_2}, {URL_3}...</strong> - External source URLs provided by the user</li>
            <li><strong>{TOTAL_URLS}</strong> - Total number of URLs provided</li>
            <li><strong>{SUCCESSFUL_SCRAPES}</strong> - Number of successfully scraped URLs</li>
            <li><strong>{FAILED_SCRAPES}</strong> - Number of failed URL scraping attempts</li>
            <li><strong>{SUCCESS_RATE}</strong> - Percentage of successful URL scrapes</li>
            <li><strong>{SOURCE_URL_1}, {SOURCE_TITLE_1}, {SOURCE_CONTENT_1}</strong> - Actual scraped content from successful URLs</li>
            <li><strong>{WORD_COUNT_1}</strong> - Word count of scraped content</li>
        </ul>
    </div>
    
    <div class="section">
        <h2>Implementation Notes</h2>
        <ul>
            <li><strong>Dynamic Construction:</strong> The prompt is built dynamically based on available input data (target, horizon, sources)</li>
            <li><strong>Source Integration:</strong> When URLs are provided, the system scrapes content and includes it in the prompt</li>
            <li><strong>Error Handling:</strong> Failed URL scraping is handled gracefully with fallback instructions</li>
            <li><strong>JSON Validation:</strong> The system includes JSON repair mechanisms for truncated responses</li>
            <li><strong>Professional Output:</strong> Designed for executive decision-making with comprehensive analysis</li>
            <li><strong>Source Attribution:</strong> Requires proper citation and connection between sources and predictions</li>
        </ul>
    </div>
    
    <div class="section">
        <h2>Usage Examples</h2>
        
        <h3>Basic Prediction</h3>
        <div class="code-block">Text to analyze: "Market trends in renewable energy"
HORIZON: Next 6 Months</div>
        
        <h3>Targeted Analysis</h3>
        <div class="code-block">Text to analyze: "AI technology developments"
TARGET: Software companies
HORIZON: Next 12 Months</div>
        
        <h3>Source-Enhanced Analysis</h3>
        <div class="code-block">Text to analyze: "Economic outlook"
HORIZON: Next 3 Months
Sources: [URL1, URL2, URL3]
+ Scraped content from accessible sources</div>
    </div>
    
    <div class="footer">
        <p>AI Prediction System Documentation | Generated on {{ $generatedAt }}</p>
        <p>This documentation is automatically generated and reflects the current prompt structure.</p>
    </div>
</body>
</html>
