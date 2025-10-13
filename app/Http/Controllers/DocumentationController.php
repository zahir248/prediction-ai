<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class DocumentationController extends Controller
{
    public function download()
    {
        try {
            // Get the full AI prompt structure
            $promptStructure = $this->getFullPromptStructure();
            
            // Create PDF
            $pdf = Pdf::loadView('documentation.pdf', [
                'promptStructure' => $promptStructure,
                'generatedAt' => now()->format('Y-m-d H:i:s'),
                'version' => '1.0'
            ]);
            
            // Set PDF options
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);
            
            return $pdf->download('AI_Prediction_Documentation.pdf');
            
        } catch (\Exception $e) {
            Log::error('Documentation PDF generation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate documentation PDF.');
        }
    }
    
    private function getFullPromptStructure()
    {
        return [
            'ai_providers' => [
                'gemini' => [
                    'name' => 'Google Gemini',
                    'model' => 'gemini-2.5-flash',
                    'base_prompt' => "You are an expert AI prediction analyst specializing in comprehensive future forecasting and strategic analysis. Please analyze the following text and provide a detailed, professional prediction analysis similar to high-quality consulting reports.",
                    'characteristics' => [
                        'More structured and formal approach',
                        'Comprehensive JSON structure with detailed fields',
                        'Extensive error handling and truncation detection',
                        'Advanced source integration capabilities',
                        'Professional consulting-style output'
                    ]
                ],
                'chatgpt' => [
                    'name' => 'OpenAI ChatGPT',
                    'model' => 'gpt-5',
                    'base_prompt' => "You are a world-class AI prediction analyst with expertise in comprehensive future forecasting and strategic analysis. You are known for providing exceptionally detailed, thorough, and complex analysis that rivals top-tier consulting firms like McKinsey, BCG, and Bain. Your analysis should be comprehensive, nuanced, and deeply insightful.",
                    'characteristics' => [
                        'More conversational and detailed approach',
                        'Emphasis on consulting firm-level analysis',
                        'Enhanced depth requirements and methodology',
                        'Focus on executive decision-making',
                        'Advanced analytical depth and complexity'
                    ]
                ]
            ],
            
            'common_dynamic_sections' => [
                'target_focus' => [
                    'condition' => 'When a target is specified',
                    'content' => "TARGET: {TARGET}\nFocus analysis on how predictions, risks, and implications affect {TARGET}."
                ],
                'prediction_horizon' => [
                    'condition' => 'When prediction horizon is set',
                    'content' => "HORIZON: {PREDICTION_HORIZON}\nTailor all predictions and assessments to this timeframe.",
                    'options' => [
                        'next_two_days' => 'Next Two Days',
                        'next_two_weeks' => 'Next Two Weeks', 
                        'next_month' => 'Next Month',
                        'three_months' => 'Next 3 Months',
                        'six_months' => 'Next 6 Months',
                        'twelve_months' => 'Next 12 Months',
                        'two_years' => 'Next 2 Years'
                    ]
                ],
                'source_integration' => [
                    'condition' => 'When URLs are provided',
                    'content' => "IMPORTANT: You have been provided with the following additional sources that contain relevant context, data, or background information:",
                    'scraping_summary' => [
                        'total_urls' => 'Total URLs provided: {TOTAL_URLS}',
                        'successful_scrapes' => 'Successfully scraped: {SUCCESSFUL_SCRAPES}',
                        'failed_scrapes' => 'Failed to scrape: {FAILED_SCRAPES}',
                        'success_rate' => 'Success rate: {SUCCESS_RATE}%'
                    ],
                    'failed_urls' => "FAILED URLS (These could not be accessed due to anti-bot protection, server errors, or other issues):",
                    'actual_content' => "ACTUAL CONTENT FROM SUCCESSFULLY SCRAPED SOURCES:",
                    'source_format' => "=== SOURCE {N} ===\nURL: {SOURCE_URL}\nTitle: {SOURCE_TITLE}\nContent: {SOURCE_CONTENT}\nWord Count: {WORD_COUNT}\n==================",
                    'integration_rules' => [
                        "Reference sources when supporting predictions",
                        "Use 'Source 1...', 'Source 2...' format", 
                        "Include 'Source Analysis' section",
                        "Cite specific facts/numbers from sources"
                    ]
                ]
            ],
            
            'json_structure' => [
                'title' => '[Topic + Time Period + Focus]',
                'executive_summary' => '[3-4 sentence summary of key predictions, risks, implications]',
                'prediction_horizon' => '[Time period]',
                'current_situation' => '[Current state and trends analysis]',
                'key_factors' => [
                    'count' => 6,
                    'format' => '[Factor N: Specific, actionable factor]'
                ],
                'predictions' => [
                    'count' => 10,
                    'format' => '[Prediction N: Specific outcome with timeline]'
                ],
                'risk_assessment' => [
                    'structure' => [
                        'risk' => '[Risk description]',
                        'level' => '[Critical/High/Medium/Low]',
                        'probability' => '[Very Likely/Likely/Possible/Unlikely]',
                        'mitigation' => '[Mitigation strategy]'
                    ]
                ],
                'recommendations' => [
                    'count' => 3,
                    'format' => '[Specific, actionable recommendation]'
                ],
                'strategic_implications' => [
                    'count' => 3,
                    'format' => '[Strategic implication]'
                ],
                'confidence_level' => '[High (90-95%)/Medium (75-89%)/Low (60-74%)]',
                'methodology' => '[AI analysis approach, data sources, and validation methods]',
                'data_sources' => [
                    'count' => 2,
                    'format' => '[Data source with relevance]'
                ],
                'assumptions' => [
                    'count' => 2,
                    'format' => '[Key assumption underlying predictions]'
                ],
                'note' => '[Important note about analysis limitations or key considerations]',
                'analysis_date' => '[Current date in YYYY-MM-DD format]',
                'next_review' => '[Recommended next review date]',
                'critical_timeline' => '[Critical dates or milestones to watch]',
                'success_metrics' => [
                    'count' => 2,
                    'format' => '[How to measure success of predictions]'
                ],
                'source_analysis' => '[Detailed explanation of how each provided source influenced your analysis and predictions. Use specific examples and show direct connections between source information and conclusions.]'
            ],
            
            'instructions' => [
                'general' => [
                    'Be specific and actionable',
                    'Include timelines for predictions',
                    'Focus on future outcomes',
                    'Provide realistic predictions',
                    'Structure risks by probability and impact',
                    'Make recommendations implementable',
                    'Include quantifiable metrics where possible',
                    'Consider opportunities and threats',
                    'Base analysis on logical reasoning',
                    'Ensure comprehensive and professional analysis'
                ],
                'source_specific' => [
                    'Cite sources using \'Source 1...\', \'Source 2...\'',
                    'Show connections between sources and predictions',
                    'Include source_analysis field',
                    'Use actual data and quotes from sources',
                    'Reference specific facts and numbers'
                ]
            ],
            
            'final_instruction' => 'Generate high-quality, professional prediction analysis suitable for executive decision-making.'
        ];
    }
}
