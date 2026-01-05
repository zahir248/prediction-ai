<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\AIServiceFactory;

class DataAnalysisService
{
    protected $aiService;

    public function __construct()
    {
        $this->aiService = AIServiceFactory::create();
    }

    /**
     * Process uploaded Excel file and extract data from all sheets
     */
    public function processExcelFile(UploadedFile $file): array
    {
        try {
            $fileName = $file->getClientOriginalName();
            $storedPath = $file->store('data-analysis', 'public');
            
            // Read all sheets from Excel file
            $allSheetsData = Excel::toArray([], $file);
            $processedData = [];
            
            foreach ($allSheetsData as $sheetIndex => $sheet) {
                $sheetName = $this->getSheetName($file, $sheetIndex);
                
                // Process sheet data
                $processedSheet = [
                    'sheet_name' => $sheetName,
                    'sheet_index' => $sheetIndex,
                    'rows' => [],
                    'headers' => [],
                    'row_count' => count($sheet),
                    'column_count' => 0,
                ];
                
                if (!empty($sheet)) {
                    // First row as headers
                    $processedSheet['headers'] = array_values($sheet[0] ?? []);
                    $processedSheet['column_count'] = count($processedSheet['headers']);
                    
                    // Process data rows
                    for ($i = 1; $i < count($sheet); $i++) {
                        $row = $sheet[$i];
                        $rowData = [];
                        
                        foreach ($processedSheet['headers'] as $colIndex => $header) {
                            $rowData[$header] = $row[$colIndex] ?? null;
                        }
                        
                        // Only add non-empty rows
                        if (!empty(array_filter($rowData, fn($val) => $val !== null && $val !== ''))) {
                            $processedSheet['rows'][] = $rowData;
                        }
                    }
                }
                
                $processedData[] = $processedSheet;
            }
            
            return [
                'file_name' => $fileName,
                'file_path' => $storedPath,
                'sheets' => $processedData,
                'total_sheets' => count($processedData),
            ];
        } catch (\Exception $e) {
            Log::error('Error processing Excel file: ' . $e->getMessage());
            throw new \Exception('Failed to process Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Get sheet name from Excel file
     */
    protected function getSheetName(UploadedFile $file, int $sheetIndex): string
    {
        // For now, use default naming. Sheet names can be extracted if needed
        // by using PhpSpreadsheet directly, but this requires more complex code
        return "Sheet " . ($sheetIndex + 1);
    }

    /**
     * Analyze data using AI (Gemini)
     */
    public function analyzeData(array $excelData, string $customInsights = '', bool $useSummary = false, $analytics = null): array
    {
        try {
            $startTime = microtime(true);
            
            // Prepare data summary for AI
            $dataSummary = $this->prepareDataSummary($excelData);
            
            // Create prompt for AI analysis
            $prompt = $this->createAnalysisPrompt($dataSummary, $customInsights, $useSummary);
            
            // Call AI service (pass analytics for tracking)
            $aiResponse = $this->aiService->analyzeText(
                $prompt,
                'data-analysis',
                null,
                null,
                $analytics,
                null
            );
            
            $processingTime = microtime(true) - $startTime;
            
            // Parse AI response
            $insights = $this->parseAIResponse($aiResponse);
            
            // Generate chart configurations based on data and user insights
            $chartConfigs = $this->generateChartConfigs($excelData, $insights, $customInsights);
            
            return [
                'insights' => $insights,
                'chart_configs' => $chartConfigs,
                'processing_time' => $processingTime,
                'model_used' => AIServiceFactory::getCurrentProvider(),
            ];
        } catch (\Exception $e) {
            Log::error('Error analyzing data with AI: ' . $e->getMessage());
            throw new \Exception('Failed to analyze data: ' . $e->getMessage());
        }
    }

    /**
     * Prepare data summary for AI analysis
     * Sends ALL data from all sheets to the AI in compact format for maximum precision
     */
    protected function prepareDataSummary(array $excelData): string
    {
        $summary = "Excel Data\n\n";
        
        foreach ($excelData['sheets'] as $sheet) {
            $summary .= "Sheet: {$sheet['sheet_name']}\n";
            $summary .= "Rows: {$sheet['row_count']}, Cols: {$sheet['column_count']}\n";
            $summary .= "Headers: " . implode(',', $sheet['headers']) . "\n";
            
            // Include ALL data rows in compact CSV-like format
            if (!empty($sheet['rows'])) {
                $totalRows = count($sheet['rows']);
                $summary .= "Data:\n";
                
                // First, output header row
                $summary .= implode('|', $sheet['headers']) . "\n";
                
                // Then output all data rows in compact format
                foreach ($sheet['rows'] as $rowIndex => $row) {
                    $rowValues = array_values($row);
                    $summary .= implode('|', array_map(function($val) {
                        if (is_null($val)) {
                            return '';
                        } elseif (is_string($val)) {
                            // Truncate only extremely long strings to 150 chars
                            // Remove newlines and extra spaces to save characters
                            $val = str_replace(["\n", "\r"], ' ', $val);
                            $val = preg_replace('/\s+/', ' ', trim($val));
                            return strlen($val) > 150 ? substr($val, 0, 150) : $val;
                        } elseif (is_array($val)) {
                            return json_encode($val);
                        } else {
                            return $val;
                        }
                    }, $rowValues));
                    $summary .= "\n";
                }
                $summary .= "\n";
            }
        }
        
        return $summary;
    }

    /**
     * Create AI analysis prompt - optimized for large datasets
     */
    protected function createAnalysisPrompt(string $dataSummary, string $customInsights = '', bool $useSummary = false): string
    {
        $prompt = "Analyze Excel data and provide insights.\n\n{$dataSummary}";

        // If Summary mode is enabled, generate comprehensive insights automatically
        if ($useSummary || empty(trim($customInsights))) {
            $prompt .= "\n\n=== ANALYSIS REQUIREMENTS ===\n";
            $prompt .= "Perform a comprehensive analysis of the Excel data and generate ALL possible relevant insights.\n";
            $prompt .= "You MUST identify and generate insights for EVERY meaningful pattern, trend, distribution, and relationship found in the data.\n";
            $prompt .= "Do not limit yourself - analyze all columns, all sheets, and all relationships between data points.\n";
            $prompt .= "Generate insights for:\n";
            $prompt .= "- All categorical distributions (pie/bar charts)\n";
            $prompt .= "- All value comparisons across categories (bar charts)\n";
            $prompt .= "- All time-based trends if dates/times are present (line charts)\n";
            $prompt .= "- All geographic patterns if location data exists (maps)\n";
            $prompt .= "- All aggregations, totals, counts, and summaries\n";
            $prompt .= "- All relationships between different columns\n";
            $prompt .= "- All significant patterns in the data\n";
            $prompt .= "Each insight must be specific, actionable, and based on actual data from the Excel file.\n";
            $prompt .= "Provide complete chart configurations with labels and data arrays extracted from the Excel data for EVERY insight.\n";
            $prompt .= "The 'suggested_charts' array should contain a chart for EVERY meaningful insight that can be derived from the data.\n";
        } elseif (!empty(trim($customInsights))) {
            $insightsList = array_filter(array_map('trim', explode("\n", $customInsights)));
            if (!empty($insightsList)) {
                $prompt .= "\n\nINSIGHTS:\n";
                foreach ($insightsList as $index => $insight) {
                    $prompt .= ($index + 1) . ". " . $insight . "\n";
                }
                
                $prompt .= "\nREQUIREMENTS:\n";
                $prompt .= "Generate EXACTLY " . count($insightsList) . " charts.\n";
                $prompt .= "Each chart: title (match insight), type (pie/bar/line), labels (array), data (numeric array) OR datasets (array), description, data_source.\n";
                $prompt .= "Use real Excel data. Labels and data arrays must have same length.\n";
                $prompt .= "IMPORTANT: If the insight contains 'vs.' or 'versus' or 'compared to' or 'comparison', you MUST provide multiple datasets.\n";
                $prompt .= "For comparison charts, use 'datasets' array instead of single 'data' array:\n";
                $prompt .= "  \"datasets\": [\n";
                $prompt .= "    {\"label\": \"First metric\", \"data\": [value1, value2, ...]},\n";
                $prompt .= "    {\"label\": \"Second metric\", \"data\": [value1, value2, ...]}\n";
                $prompt .= "  ]\n";
                $prompt .= "Each dataset must have the same length as 'labels' array.\n";
                $prompt .= "For single-metric charts (no 'vs.'), you can use either 'data' array OR 'datasets' array with one dataset.\n";
                $prompt .= "IMPORTANT: If the insight contains 'map' or is about geographic locations, include a 'coordinates' array with [latitude, longitude] for each label.\n";
                $prompt .= "If Excel data has latitude/longitude columns, extract and use those coordinates in the 'coordinates' field.\n";
            }
        }

        $prompt .= "\n\nReturn JSON only:\n";
        $prompt .= "{\n";
        $prompt .= "    \"summary\": \"Brief summary\",\n";
        $prompt .= "    \"key_findings\": [\"Finding 1\", \"Finding 2\"],\n";
        $prompt .= "    \"trends\": [{\"title\": \"Trend\", \"description\": \"Description\", \"type\": \"increasing|decreasing|stable\"}],\n";
        $prompt .= "    \"recommendations\": [\"Rec 1\", \"Rec 2\"],\n";
        $prompt .= "    \"statistics\": {\"total_records\": number, \"key_metrics\": {\"metric\": \"value\"}},\n";
        $prompt .= "    \"suggested_charts\": [\n";
        $prompt .= "        {\n";
        $prompt .= "            \"type\": \"bar|pie|line\",\n";
        $prompt .= "            \"title\": \"Chart title\",\n";
        $prompt .= "            \"labels\": [\"Label1\", \"Label2\"],\n";
        $prompt .= "            \"data\": [value1, value2],\n";
        $prompt .= "            \"datasets\": [{\"label\": \"Metric 1\", \"data\": [value1, value2]}, {\"label\": \"Metric 2\", \"data\": [value3, value4]}],\n";
        $prompt .= "            \"description\": \"Description\",\n";
        $prompt .= "            \"data_source\": \"sheet/column\",\n";
        $prompt .= "            \"coordinates\": [[lat1, lng1], [lat2, lng2]]\n";
        $prompt .= "        }\n";
        $prompt .= "    ]\n";
        $prompt .= "}\n\n";
        $prompt .= "IMPORTANT FOR COMPARISON CHARTS:\n";
        $prompt .= "- If chart title contains 'vs.', 'versus', 'compared to', or 'comparison', you MUST use 'datasets' array with multiple datasets.\n";
        $prompt .= "- Each dataset should have a 'label' (e.g., 'Regional Product Sales') and 'data' array.\n";
        $prompt .= "- All datasets must have the same length as 'labels' array.\n";
        $prompt .= "- For single-metric charts, you can use either 'data' array OR 'datasets' array with one dataset.\n";
        $prompt .= "Note: Include 'coordinates' array only for map-related charts. Each coordinate should be [latitude, longitude].\n";
        
        return $prompt;
    }

    /**
     * Parse AI response
     */
    protected function parseAIResponse($aiResponse): array
    {
        try {
            // Extract JSON from response
            $responseText = is_array($aiResponse) ? ($aiResponse['content'] ?? json_encode($aiResponse)) : $aiResponse;
            
            // Try to extract JSON from markdown code blocks
            if (preg_match('/```json\s*(.*?)\s*```/s', $responseText, $matches)) {
                $responseText = $matches[1];
            } elseif (preg_match('/```\s*(.*?)\s*```/s', $responseText, $matches)) {
                $responseText = $matches[1];
            }
            
            // Try to find JSON object
            if (preg_match('/\{.*\}/s', $responseText, $matches)) {
                $responseText = $matches[0];
            }
            
            $parsed = json_decode($responseText, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                return $parsed;
            }
            
            // Fallback: create structured response from text
            return [
                'summary' => is_string($responseText) ? substr($responseText, 0, 500) : 'Analysis completed',
                'key_findings' => [],
                'trends' => [],
                'recommendations' => [],
                'statistics' => [],
                'suggested_charts' => [],
                'raw_response' => $responseText,
            ];
        } catch (\Exception $e) {
            Log::error('Error parsing AI response: ' . $e->getMessage());
            return [
                'summary' => 'Analysis completed but response parsing failed',
                'key_findings' => [],
                'trends' => [],
                'recommendations' => [],
                'statistics' => [],
                'suggested_charts' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate chart configurations based on AI suggestions
     * AI analyzes insights and Excel data, then suggests chart types and provides data
     */
    protected function generateChartConfigs(array $excelData, array $insights, string $customInsights = ''): array
    {
        $chartConfigs = [];
        
        // Rely primarily on AI-suggested charts
        if (isset($insights['suggested_charts']) && is_array($insights['suggested_charts'])) {
            foreach ($insights['suggested_charts'] as $suggestedChart) {
                $chartConfig = $this->buildChartFromSuggestion($suggestedChart, $excelData);
                if ($chartConfig) {
                    $chartConfigs[] = $chartConfig;
                }
            }
        }
        
        // If user provided custom insights but AI didn't generate enough charts, ensure we have one per insight
        if (!empty(trim($customInsights))) {
            $insightsList = array_filter(array_map('trim', explode("\n", $customInsights)));
            $insightCount = count($insightsList);
            $chartCount = count($chartConfigs);
            
            // If we have fewer charts than insights, try to extract data for missing insights
            if ($chartCount < $insightCount) {
                foreach ($insightsList as $index => $userInsight) {
                    // Check if we already have a chart for this insight
                    $hasChart = false;
                    foreach ($chartConfigs as $existingChart) {
                        $existingTitle = strtolower($existingChart['title'] ?? '');
                        $userInsightLower = strtolower($userInsight);
                        
                        if (stripos($existingTitle, $userInsightLower) !== false || 
                            stripos($userInsightLower, $existingTitle) !== false) {
                            $hasChart = true;
                            break;
                        }
                    }
                    
                    // If no chart for this insight, create a simple one from data
                    if (!$hasChart) {
                        $chartConfig = $this->createSimpleChartForInsight($userInsight, $excelData);
                        if ($chartConfig) {
                            $chartConfigs[] = $chartConfig;
                        }
                    }
                }
            }
        }
        
        return $chartConfigs;
    }
    
    /**
     * Create a simple chart for an insight when AI doesn't provide one
     * This is a fallback - AI should provide charts in most cases
     */
    protected function createSimpleChartForInsight(string $insight, array $excelData): ?array
    {
        $insightLower = strtolower($insight);
        
        // Try to find a relevant column based on insight keywords
        foreach ($excelData['sheets'] as $sheet) {
            if (empty($sheet['rows']) || empty($sheet['headers'])) continue;
            
            // Look for "by X" pattern
            if (preg_match('/by\s+(\w+)/i', $insight, $matches)) {
                $groupByColumn = $matches[1];
                foreach ($sheet['headers'] as $header) {
                    if (stripos(strtolower($header), strtolower($groupByColumn)) !== false) {
                        $values = array_column($sheet['rows'], $header);
                        $filteredValues = array_filter($values, function($val) {
                            return ($val !== null && $val !== '');
                        });
                        
                        if (count($filteredValues) > 0) {
                            $stringValues = array_map(function($val) {
                                return is_string($val) ? $val : (string)$val;
                            }, $filteredValues);
                            
                            $valueCounts = array_count_values($stringValues);
                            arsort($valueCounts);
                            
                            $chartType = (stripos($insightLower, 'percentage') !== false || 
                                         stripos($insightLower, 'distribution') !== false) ? 'pie' : 'bar';
                            
                            $cleanedTitle = $this->cleanChartTitle($insight);
                            
                            return [
                                'type' => $chartType,
                                'title' => $cleanedTitle,
                                'sheet' => $sheet['sheet_name'],
                                'data' => [
                                    'labels' => array_keys($valueCounts),
                                    'datasets' => [[
                                        'label' => 'Count',
                                        'data' => array_values($valueCounts),
                                    ]],
                                ],
                            ];
                        }
                    }
                }
            }
        }
        
        return null;
    }
    
    /**
     * Check if user insight matches a suggested chart
     */
    protected function insightMatchesChart(string $insight, array $chart): bool
    {
        $insightLower = strtolower($insight);
        $title = strtolower($chart['title'] ?? '');
        $description = strtolower($chart['description'] ?? '');
        
        // Extract key words from insight
        $insightWords = array_filter(explode(' ', $insightLower), function($word) {
            return strlen($word) > 3; // Only meaningful words
        });
        
        foreach ($insightWords as $word) {
            if (stripos($title, $word) !== false || stripos($description, $word) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Clean chart title by removing sheet-derived suffixes
     */
    protected function cleanChartTitle(string $title): string
    {
        // Remove patterns like "(Sheet 1 Derived)", "(Sheet 2 Derived)", etc.
        $title = preg_replace('/\s*\(Sheet\s+\d+\s+Derived\)/i', '', $title);
        // Remove patterns like "(Derived from Sheet 1)", etc.
        $title = preg_replace('/\s*\(Derived\s+from\s+Sheet\s+\d+\)/i', '', $title);
        // Remove patterns like "(Sheet 1)", "(Sheet 2)", "(Sheet 3)", etc. - can appear multiple times
        $title = preg_replace('/\s*\(Sheet\s+\d+\)/i', '', $title);
        // Clean up extra spaces that might be left after removing sheet references
        $title = preg_replace('/\s+/', ' ', $title);
        // Normalize "vs" spacing - ensure consistent " vs. " format
        $title = preg_replace('/\s+vs\.?\s+/i', ' vs. ', $title);
        // Remove any trailing whitespace
        return trim($title);
    }
    
    /**
     * Build chart configuration from AI suggestion
     */
    protected function buildChartFromSuggestion(array $suggestion, array $excelData): ?array
    {
        $rawTitle = $suggestion['title'] ?? 'Chart';
        $cleanedTitle = $this->cleanChartTitle($rawTitle);
        $labels = $suggestion['labels'] ?? [];
        
        // Check if AI provided datasets array (for comparison charts like "X vs. Y")
        if (isset($suggestion['datasets']) && is_array($suggestion['datasets']) && count($suggestion['datasets']) > 0) {
            // Process multiple datasets (comparison chart)
            $processedDatasets = [];
            foreach ($suggestion['datasets'] as $dataset) {
                if (isset($dataset['data']) && is_array($dataset['data'])) {
                    // Ensure data is numeric
                    $numericData = array_map(function($val) {
                        return is_numeric($val) ? (float)$val : 0;
                    }, $dataset['data']);
                    
                    $rawLabel = $dataset['label'] ?? 'Data';
                    $cleanedLabel = $this->cleanChartTitle($rawLabel);
                    
                    $processedDatasets[] = [
                        'label' => $cleanedLabel,
                        'data' => $numericData,
                    ];
                }
            }
            
            if (count($processedDatasets) > 0 && count($labels) > 0) {
                $chartConfig = [
                    'type' => $suggestion['type'] ?? 'bar',
                    'title' => $cleanedTitle,
                    'sheet' => $suggestion['data_source'] ?? 'Sheet 1',
                    'data' => [
                        'labels' => $labels,
                        'datasets' => $processedDatasets,
                    ],
                ];
                
                // Include coordinates if provided (for map charts)
                if (isset($suggestion['coordinates']) && is_array($suggestion['coordinates'])) {
                    $chartConfig['coordinates'] = $suggestion['coordinates'];
                }
                
                return $chartConfig;
            }
        }
        
        // If AI provided data directly with labels and data (single dataset), use it
        if (isset($suggestion['data']) && isset($suggestion['labels']) && 
            is_array($suggestion['labels']) && is_array($suggestion['data']) &&
            count($suggestion['labels']) > 0 && count($suggestion['data']) > 0) {
            
            // Ensure data is numeric
            $numericData = array_map(function($val) {
                return is_numeric($val) ? (float)$val : 0;
            }, $suggestion['data']);
            
            $chartConfig = [
                'type' => $suggestion['type'] ?? 'bar',
                'title' => $cleanedTitle,
                'sheet' => $suggestion['data_source'] ?? 'Sheet 1',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [[
                        'label' => $this->cleanChartTitle($cleanedTitle),
                        'data' => $numericData,
                    ]],
                ],
            ];
            
            // Include coordinates if provided (for map charts)
            if (isset($suggestion['coordinates']) && is_array($suggestion['coordinates'])) {
                $chartConfig['coordinates'] = $suggestion['coordinates'];
            }
            
            return $chartConfig;
        }
        
        // Otherwise, try to extract data based on data_source description
        $dataSource = $suggestion['data_source'] ?? '';
        $description = $suggestion['description'] ?? '';
        
        // Try to find columns mentioned in data_source or description
        foreach ($excelData['sheets'] as $sheet) {
            if (empty($sheet['rows']) || empty($sheet['headers'])) continue;
            
            // Check if this sheet matches
            $sheetMatches = stripos($sheet['sheet_name'], $dataSource) !== false || 
                           stripos($dataSource, $sheet['sheet_name']) !== false ||
                           empty($dataSource);
            
            if ($sheetMatches) {
                // Try to find columns mentioned in description or data_source
                $searchText = strtolower($dataSource . ' ' . $description);
                
                foreach ($sheet['headers'] as $header) {
                    $headerLower = strtolower($header);
                    if (stripos($searchText, $headerLower) !== false || 
                        stripos($headerLower, $searchText) !== false) {
                        return $this->createChartFromColumn($sheet, $header, $suggestion);
                    }
                }
            }
        }
        
        return null;
    }
    
}

