@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<div style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Main Card -->
        <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
                    <div style="flex: 1;">
                        <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">{{ $prediction->topic }}</h1>
                        @if($prediction->target)
                            <p style="color: #166534; font-size: 13px; margin: 8px 0; font-weight: 500; background: #f0fdf4; padding: 8px 12px; border-radius: 6px; border: 1px solid #bbf7d0; display: inline-block;">
                                <strong>Target:</strong> {{ $prediction->target }}
                            </p>
                        @endif
                        <p style="color: #64748b; font-size: 13px; margin: 8px 0 0 0;">
                            Created on {{ $prediction->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a href="{{ route('predictions.history') }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; background: transparent; color: #64748b; text-decoration: none; border: 2px solid #e2e8f0; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease;">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        @if($prediction->status === 'completed')
                        <button onclick="confirmExport({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" style="padding: 10px 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3); cursor: pointer;">
                            Export PDF
                        </button>
                        @endif
                        <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 10px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                            New Analysis
                        </a>
                    </div>
                </div>

                <!-- Status Section -->
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Analysis Status</h2>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; 
                            @if($prediction->status === 'completed') 
                                background: #dcfce7; color: #166534;
                            @elseif($prediction->status === 'processing') 
                                background: #fef3c7; color: #92400e;
                            @elseif($prediction->status === 'failed') 
                                background: #fee2e2; color: #991b1b;
                            @else 
                                background: #e2e8f0; color: #475569;
                            @endif">
                            {{ ucfirst($prediction->status) }}
                        </span>
                        <span style="color: #64748b; font-size: 13px;">
                            Period: {{ ucwords(str_replace('_', ' ', $prediction->prediction_horizon)) }}
                        </span>
                    </div>
                </div>

                <!-- Issue Details Section -->
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Issue Details</h2>
                    <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <p style="color: #374151; line-height: 1.6; margin: 0; white-space: pre-wrap; font-size: 14px;">{{ $prediction->input_data['text'] }}</p>
                    </div>
                </div>

                <!-- Uploaded Files -->
                @if($prediction->uploaded_files && count($prediction->uploaded_files) > 0)
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Uploaded Files</h2>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($prediction->uploaded_files as $index => $file)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #374151; font-size: 14px; margin-bottom: 4px;">{{ $file['original_name'] }}</div>
                                <div style="font-size: 12px; color: #64748b;">
                                    {{ number_format($file['size'] / 1024 / 1024, 2) }} MB • {{ strtoupper($file['extension']) }}
                                </div>
                            </div>
                            <a href="{{ Storage::url($file['stored_path']) }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               style="padding: 8px 16px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 12px; transition: all 0.3s ease;">
                                Download
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Extracted Text from Files -->
                @if($prediction->extracted_text)
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 20px;">📋 Extracted Content from Files</h2>
                    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 24px; border-radius: 16px; border: 1px solid #f59e0b;">
                        <p style="color: #92400e; font-size: 16px; margin-bottom: 16px; font-weight: 600;">📊 AI-Processed File Content</p>
                        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #f59e0b; max-height: 400px; overflow-y: auto;">
                            <pre style="color: #92400e; line-height: 1.6; margin: 0; white-space: pre-wrap; font-family: inherit; font-size: 14px;">{{ $prediction->extracted_text }}</pre>
                        </div>
                        <p style="color: #92400e; font-size: 14px; margin-top: 16px; margin-bottom: 0; opacity: 0.8;">
                            This content was automatically extracted from your uploaded files and analyzed by the AI system to enhance the prediction accuracy.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Source URLs -->
                @if($prediction->source_urls && count($prediction->source_urls) > 0)
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Source References</h2>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($prediction->source_urls as $index => $sourceUrl)
                        <a href="{{ $sourceUrl }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           style="display: block; padding: 10px 12px; background: #fef3c7; color: #92400e; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; border: 1px solid #fde68a;">
                            Source {{ $index + 1 }}: {{ Str::limit($sourceUrl, 60) }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Scraping Metadata -->
                @if(isset($prediction->prediction_result['scraping_metadata']))
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Source Content Analysis</h2>
                    <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #10b981;">
                        <p style="color: #374151; font-size: 14px; margin-bottom: 12px; font-weight: 600;">Content Extraction Results</p>
                        <div style="color: #374151; font-size: 14px; line-height: 1.6;">
                            <p style="margin-bottom: 8px;"><strong>Total Sources:</strong> {{ $prediction->prediction_result['scraping_metadata']['total_sources'] }}</p>
                            <p style="margin-bottom: 8px;"><strong>Successfully Scraped:</strong> {{ $prediction->prediction_result['scraping_metadata']['successfully_scraped'] }}</p>
                            <p style="margin-bottom: 12px;"><strong>Analysis Date:</strong> {{ \Carbon\Carbon::parse($prediction->prediction_result['scraping_metadata']['scraped_at'])->format('M d, Y H:i') }}</p>
                            
                            @if(isset($prediction->prediction_result['scraping_metadata']['source_details']))
                            <div style="margin-top: 16px;">
                                <p style="font-weight: 600; margin-bottom: 12px; color: #374151;">Source Details:</p>
                                @foreach($prediction->prediction_result['scraping_metadata']['source_details'] as $index => $source)
                                <div style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 8px;">
                                    <p style="margin: 0 0 4px 0; color: #374151;"><strong>Source {{ $index + 1 }}:</strong> 
                                        <a href="{{ $source['url'] }}" target="_blank" style="color: #3b82f6; text-decoration: underline;">{{ $source['url'] }}</a>
                                    </p>
                                    <p style="margin: 0 0 4px 0; font-size: 13px; color: #64748b;"><strong>Title:</strong> {{ $source['title'] }}</p>
                                    <p style="margin: 0 0 4px 0; font-size: 13px; color: #64748b;"><strong>Word Count:</strong> {{ $source['word_count'] }}</p>
                                    <p style="margin: 0; font-size: 13px; color: #64748b;"><strong>Status:</strong> 
                                        <span style="color: #10b981; font-weight: 600;">
                                            Success
                                        </span>
                                    </p>
                                    @if(isset($source['error']) && $source['error'])
                                    <div style="margin: 8px 0 0 0; padding: 8px; background: #eff6ff; border-radius: 6px; border-left: 3px solid #3b82f6;">
                                        <p style="margin: 0; font-size: 12px; color: #1e40af;">
                                            <strong>AI Note:</strong> This source was inaccessible, so the AI used its general knowledge and training data to provide relevant insights for the analysis.
                                        </p>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <p style="color: #64748b; font-size: 13px; margin-top: 16px; margin-bottom: 0;">
                            This analysis incorporates actual content extracted from the provided source URLs, ensuring predictions are based on real, current data rather than general knowledge.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Source Analysis Section -->
                @if($prediction->source_urls && count($prediction->source_urls) > 0 && isset($prediction->prediction_result['source_analysis']))
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Source Analysis & Influence</h2>
                    <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #3b82f6;">
                        <p style="color: #374151; font-size: 14px; margin-bottom: 12px; font-weight: 600;">How Sources Influenced This Analysis</p>
                        <div style="color: #374151; font-size: 14px; line-height: 1.6;">
                            {!! nl2br(e($prediction->prediction_result['source_analysis'])) !!}
                        </div>
                        <p style="color: #64748b; font-size: 13px; margin-top: 16px; margin-bottom: 0;">
                            This analysis shows how each provided source contributed to specific predictions and conclusions, ensuring transparency and traceability of insights.
                        </p>
                    </div>
                </div>
                @endif

                <!-- AI Results -->
                @if(($prediction->status === 'completed' || $prediction->status === 'completed_with_warnings') && $prediction->prediction_result)
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">AI Analysis Results</h2>
                    
                    @if(isset($prediction->prediction_result['note']) && is_string($prediction->prediction_result['note']))
                        <div style="background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; padding: 16px; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #f59e0b;">
                            <strong>Important Note:</strong> {{ $prediction->prediction_result['note'] }}
                        </div>
                    @endif
                    
                    @if(isset($prediction->prediction_result['title']) && is_string($prediction->prediction_result['title']))
                        @php $report = $prediction->prediction_result; @endphp
                        
                        <div style="display: flex; flex-direction: column; gap: 24px;">
                            <!-- Title and Horizon -->
                            <div style="text-align: center; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0;">
                                <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">{{ $report['title'] }}</h3>
                                @if(isset($report['prediction_horizon']) && is_string($report['prediction_horizon']))
                                <p style="color: #64748b; font-size: 14px; margin: 0;">
                                    <strong>Prediction Horizon:</strong> {{ $report['prediction_horizon'] }}
                                </p>
                                @endif
                            </div>
                            
                            <!-- Executive Summary -->
                            @if(isset($report['executive_summary']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #3b82f6;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Executive Summary</h4>
                                <div style="color: #374151; line-height: 1.6; font-size: 14px;">
                                    @if(is_array($report['executive_summary']))
                                        @foreach($report['executive_summary'] as $key => $value)
                                            @if(is_string($value))
                                                <p style="margin-bottom: 10px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                            @elseif(is_array($value))
                                                <div style="margin-bottom: 12px;">
                                                    <p style="font-weight: 600; margin-bottom: 6px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                    <ul style="margin: 0; padding-left: 18px;">
                                                        @foreach($value as $item)
                                                            <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <p style="margin-bottom: 10px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
                                            @endif
                                        @endforeach
                                    @elseif(is_string($report['executive_summary']))
                                        <p style="margin: 0;">{{ $report['executive_summary'] }}</p>
                                    @else
                                        <p style="margin: 0;">{{ (string)$report['executive_summary'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <!-- Current Situation -->
                            @if(isset($report['current_situation']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #6b7280;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Current Situation & Future Implications</h4>
                                <div style="color: #374151; line-height: 1.6; font-size: 14px;">
                                    @if(is_array($report['current_situation']))
                                        @foreach($report['current_situation'] as $key => $value)
                                            @if(is_string($value))
                                                <p style="margin-bottom: 10px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                            @elseif(is_array($value))
                                                <div style="margin-bottom: 12px;">
                                                    <p style="font-weight: 600; margin-bottom: 6px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                    <ul style="margin: 0; padding-left: 18px;">
                                                        @foreach($value as $item)
                                                            <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <p style="margin-bottom: 10px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
                                            @endif
                                        @endforeach
                                    @elseif(is_string($report['current_situation']))
                                        <p style="margin: 0;">{{ $report['current_situation'] }}</p>
                                    @else
                                        <p style="margin: 0;">{{ (string)$report['current_situation'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <!-- Key Factors -->
                            @if(isset($report['key_factors']) && is_array($report['key_factors']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #10b981;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Key Factors for Future Development</h4>
                                <ul style="margin: 0; padding-left: 18px; color: #374151; line-height: 1.6; font-size: 14px;">
                                    @foreach($report['key_factors'] as $factor)
                                        <li style="margin-bottom: 8px;">
                                            @if(is_array($factor))
                                                @foreach($factor as $key => $value)
                                                    @if(is_string($value))
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                    @elseif(is_array($value))
                                                        <div style="margin-top: 6px;">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            <ul style="margin: 6px 0 0 18px;">
                                                                @foreach($value as $item)
                                                                    <li>{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @else
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}
                                                    @endif
                                                @endforeach
                                            @elseif(is_string($factor))
                                                {{ $factor }}
                                            @else
                                                {{ (string)$factor }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            <!-- Future Predictions -->
                            @if(isset($report['future_predictions']) && is_array($report['future_predictions']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #8b5cf6;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Future Predictions</h4>
                                <ul style="margin: 0; padding-left: 18px; color: #374151; line-height: 1.6; font-size: 14px;">
                                    @foreach($report['future_predictions'] as $prediction_item)
                                        <li style="margin-bottom: 8px;">
                                            @if(is_array($prediction_item))
                                                @foreach($prediction_item as $key => $value)
                                                    @if(is_string($value))
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                    @elseif(is_array($value))
                                                        <div style="margin-top: 6px;">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            <ul style="margin: 6px 0 0 18px;">
                                                                @foreach($value as $item)
                                                                    <li>{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @else
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}
                                                    @endif
                                                @endforeach
                                            @elseif(is_string($prediction_item))
                                                {{ $prediction_item }}
                                            @else
                                                {{ (string)$prediction_item }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @elseif(isset($report['predictions']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #8b5cf6;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Future Predictions</h4>
                                @if(is_array($report['predictions']))
                                    <ul style="margin: 0; padding-left: 18px; color: #374151; line-height: 1.6; font-size: 14px;">
                                        @foreach($report['predictions'] as $prediction_item)
                                            <li style="margin-bottom: 8px;">
                                                @if(is_array($prediction_item))
                                                    @foreach($prediction_item as $key => $value)
                                                        @if(is_string($value))
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                        @elseif(is_array($value))
                                                            <div style="margin-top: 6px;">
                                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                <ul style="margin: 6px 0 0 18px;">
                                                                    @foreach($value as $item)
                                                                        <li>{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @else
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}
                                                        @endif
                                                    @endforeach
                                                @elseif(is_string($prediction_item))
                                                    {{ $prediction_item }}
                                                @else
                                                    {{ (string)$prediction_item }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @elseif(is_string($report['predictions']))
                                    <p style="margin: 0; color: #374151; line-height: 1.6; font-size: 14px;">{{ $report['predictions'] }}</p>
                                @else
                                    <p style="margin: 0; color: #374151; line-height: 1.6; font-size: 14px;">{{ (string)$report['predictions'] }}</p>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Risk Assessment -->
                            @if(isset($report['risk_assessment']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #ef4444;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Risk Assessment</h4>
                                <div style="color: #374151; line-height: 1.6; font-size: 14px;">
                                    @if(is_array($report['risk_assessment']))
                                        @php
                                            $isArrayOfObjects = false;
                                            $firstItem = reset($report['risk_assessment']);
                                            if (is_array($firstItem) && isset($firstItem['risk'])) {
                                                $isArrayOfObjects = true;
                                            }
                                        @endphp
                                        
                                        @if($isArrayOfObjects)
                                            <!-- Display as table if it's an array of risk objects -->
                                            <div style="overflow-x: auto;">
                                                <table style="width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 14px;">
                                                    <thead>
                                                        <tr>
                                                            <th style="background-color: #f0f0f0; color: #1e293b; font-weight: 600; padding: 10px; text-align: left; border: 1px solid #d1d5db; width: 35%;">Risk</th>
                                                            <th style="background-color: #f0f0f0; color: #1e293b; font-weight: 600; padding: 10px; text-align: left; border: 1px solid #d1d5db; width: 15%;">Level</th>
                                                            <th style="background-color: #f0f0f0; color: #1e293b; font-weight: 600; padding: 10px; text-align: left; border: 1px solid #d1d5db; width: 15%;">Probability</th>
                                                            <th style="background-color: #f0f0f0; color: #1e293b; font-weight: 600; padding: 10px; text-align: left; border: 1px solid #d1d5db; width: 35%;">Mitigation Strategy</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($report['risk_assessment'] as $risk)
                                                            @if(is_array($risk))
                                                                <tr style="background-color: {{ $loop->even ? '#f9fafb' : 'white' }};">
                                                                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top;">{{ isset($risk['risk']) ? (is_string($risk['risk']) ? $risk['risk'] : (string)$risk['risk']) : 'N/A' }}</td>
                                                                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top;">{{ isset($risk['level']) ? (is_string($risk['level']) ? $risk['level'] : (string)$risk['level']) : '-' }}</td>
                                                                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top;">{{ isset($risk['probability']) ? (is_string($risk['probability']) ? $risk['probability'] : (string)$risk['probability']) : '-' }}</td>
                                                                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top;">{{ isset($risk['mitigation']) ? (is_string($risk['mitigation']) ? $risk['mitigation'] : (string)$risk['mitigation']) : '-' }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <!-- Display as key-value pairs if it's a simple associative array -->
                                            @foreach($report['risk_assessment'] as $key => $value)
                                                @if(is_string($value))
                                                    <p style="margin-bottom: 10px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                                @elseif(is_array($value))
                                                    <div style="margin-bottom: 12px;">
                                                        <p style="font-weight: 600; margin-bottom: 6px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                        <ul style="margin: 0; padding-left: 18px;">
                                                            @foreach($value as $item)
                                                                <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @else
                                                    <p style="margin-bottom: 10px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
                                                @endif
                                            @endforeach
                                        @endif
                                    @elseif(is_string($report['risk_assessment']))
                                        <p style="margin: 0;">{{ $report['risk_assessment'] }}</p>
                                    @else
                                        <p style="margin: 0;">{{ (string)$report['risk_assessment'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <!-- Policy Implications -->
                            @if(isset($report['policy_implications']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #f59e0b;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Policy Implications</h4>
                                <div style="color: #374151; line-height: 1.6; font-size: 14px;">
                                    @if(is_array($report['policy_implications']))
                                        @foreach($report['policy_implications'] as $key => $value)
                                            @if(is_string($value))
                                                <p style="margin-bottom: 10px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                            @elseif(is_array($value))
                                                <div style="margin-bottom: 12px;">
                                                    <p style="font-weight: 600; margin-bottom: 6px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                    <ul style="margin: 0; padding-left: 18px;">
                                                        @foreach($value as $item)
                                                            <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <p style="margin-bottom: 10px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
                                            @endif
                                        @endforeach
                                    @elseif(is_string($report['policy_implications']))
                                        <p style="margin: 0;">{{ $report['policy_implications'] }}</p>
                                    @else
                                        <p style="margin: 0;">{{ (string)$report['policy_implications'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <!-- Recommendations -->
                            @if(isset($report['recommendations']) && is_array($report['recommendations']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #0ea5e9;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Strategic Recommendations</h4>
                                <ul style="margin: 0; padding-left: 18px; color: #374151; line-height: 1.6; font-size: 14px;">
                                    @foreach($report['recommendations'] as $recommendation)
                                        <li style="margin-bottom: 8px;">
                                            @if(is_array($recommendation))
                                                @foreach($recommendation as $key => $value)
                                                    @if(is_string($value))
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                    @elseif(is_array($value))
                                                        <div style="margin-top: 6px;">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            <ul style="margin: 6px 0 0 18px;">
                                                                @foreach($value as $item)
                                                                    <li>{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @else
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}
                                                    @endif
                                                @endforeach
                                            @elseif(is_string($recommendation))
                                                {{ $recommendation }}
                                            @else
                                                {{ (string)$recommendation }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            <!-- Confidence Level -->
                            @if(isset($report['confidence_level']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #10b981;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Prediction Confidence</h4>
                                <div style="display: inline-block; padding: 8px 16px; background: #10b981; color: white; border-radius: 6px; font-weight: 600; font-size: 14px; margin-bottom: 12px;">
                                    {{ is_string($report['confidence_level']) ? $report['confidence_level'] : (string)$report['confidence_level'] }}
                                </div>
                                @if(isset($report['methodology']) && is_string($report['methodology']))
                                    <p style="margin-top: 12px; color: #64748b; font-size: 13px; margin: 0;">
                                        <strong>Methodology:</strong> {{ $report['methodology'] }}
                                    </p>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Strategic Implications -->
                            @if(isset($report['strategic_implications']) && is_array($report['strategic_implications']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #fbbf24;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Strategic Implications</h4>
                                <ul style="margin: 0; padding-left: 18px; color: #374151; line-height: 1.6; font-size: 14px;">
                                    @foreach($report['strategic_implications'] as $implication)
                                        <li style="margin-bottom: 8px;">
                                            @if(is_array($implication))
                                                @foreach($implication as $key => $value)
                                                    @if(is_string($value))
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                    @elseif(is_array($value))
                                                        <div style="margin-top: 6px;">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            <ul style="margin: 6px 0 0 18px;">
                                                                @foreach($value as $item)
                                                                    <li>{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @else
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}
                                                    @endif
                                                @endforeach
                                            @elseif(is_string($implication))
                                                {{ $implication }}
                                            @else
                                                {{ (string)$implication }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            <!-- Data Sources -->
                            @if(isset($report['data_sources']) && is_array($report['data_sources']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #3b82f6;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Data Sources & Methodology</h4>
                                <ul style="margin: 0; padding-left: 18px; color: #374151; line-height: 1.6; font-size: 14px;">
                                    @foreach($report['data_sources'] as $source)
                                        <li style="margin-bottom: 6px;">{{ is_string($source) ? $source : (string)$source }}</li>
                                    @endforeach
                                </ul>
                                @if(isset($report['methodology']) && is_string($report['methodology']))
                                    <p style="margin-top: 12px; font-style: italic; color: #64748b; font-size: 13px; margin: 0;">
                                        <strong>Methodology:</strong> {{ $report['methodology'] }}
                                    </p>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Assumptions -->
                            @if(isset($report['assumptions']) && is_array($report['assumptions']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #a855f7;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Key Assumptions</h4>
                                <ul style="margin: 0; padding-left: 18px; color: #374151; line-height: 1.6; font-size: 14px;">
                                    @foreach($report['assumptions'] as $assumption)
                                        <li style="margin-bottom: 6px;">{{ is_string($assumption) ? $assumption : (string)$assumption }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            <!-- Success Metrics -->
                            @if(isset($report['success_metrics']) && is_array($report['success_metrics']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #10b981;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Success Metrics & KPIs</h4>
                                <ul style="margin: 0; padding-left: 18px; color: #374151; line-height: 1.6; font-size: 14px;">
                                    @foreach($report['success_metrics'] as $metric)
                                        <li style="margin-bottom: 6px;">{{ is_string($metric) ? $metric : (string)$metric }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            <!-- Timeline Information -->
                            @if(isset($report['critical_timeline']) || isset($report['next_review']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #f59e0b;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Timeline & Review Schedule</h4>
                                <div style="color: #374151; line-height: 1.6; font-size: 14px;">
                                    @if(isset($report['critical_timeline']) && is_string($report['critical_timeline']))
                                        <p style="margin-bottom: 8px;"><strong>Critical Timeline:</strong> {{ $report['critical_timeline'] }}</p>
                                    @endif
                                    @if(isset($report['next_review']) && is_string($report['next_review']))
                                        <p style="margin-bottom: 8px;"><strong>Next Review Date:</strong> {{ $report['next_review'] }}</p>
                                    @endif
                                    @if(isset($report['analysis_date']) && is_string($report['analysis_date']))
                                        <p style="margin: 0;"><strong>Analysis Date:</strong> {{ $report['analysis_date'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <!-- Fallback: Display raw data if structured data is not available -->
                            @if(!isset($report['executive_summary']) && !isset($report['current_situation']) && !isset($report['key_factors']) && !isset($report['future_predictions']) && !isset($report['policy_implications']) && !isset($report['risk_assessment']) && !isset($report['recommendations']))
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Analysis Results</h4>
                                <div style="background: white; padding: 16px; border-radius: 6px; border: 1px solid #e2e8f0; overflow-x: auto;">
                                    <pre style="color: #374151; line-height: 1.6; margin: 0; font-family: 'Courier New', monospace; font-size: 13px; white-space: pre-wrap;">{{ json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                            @endif
                        </div>
                    @elseif(isset($prediction->prediction_result['raw_response']))
                        <!-- Raw Response Display -->
                        <div style="padding: 20px; background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f59e0b;">
                            <div style="margin-bottom: 12px;">
                                <strong style="font-size: 14px;">Partial Analysis Results</strong>
                            </div>
                            <p style="margin-bottom: 16px; font-size: 13px;">The AI analysis was completed but the response may be incomplete. Below is the raw output from the AI:</p>
                        </div>
                        
                        <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">AI Analysis Output</h4>
                            <div style="background: white; padding: 16px; border-radius: 6px; border: 1px solid #d1d5db; max-height: 600px; overflow-y: auto;">
                                <pre style="white-space: pre-wrap; color: #374151; line-height: 1.6; margin: 0; font-family: 'Courier New', monospace; font-size: 13px;">{{ $prediction->prediction_result['raw_response'] }}</pre>
                            </div>
                        </div>
                    @else
                        <!-- Fallback: Display raw prediction result if no title is available -->
                        <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 16px;">Analysis Results</h4>
                            <div style="background: white; padding: 16px; border-radius: 6px; border: 1px solid #e2e8f0; overflow-x: auto;">
                                <pre style="color: #374151; line-height: 1.6; margin: 0; font-family: 'Courier New', monospace; font-size: 13px; white-space: pre-wrap;">{{ json_encode($prediction->prediction_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
                @elseif($prediction->status === 'failed')
                <div style="margin-bottom: 32px; text-align: center; padding: 40px 20px; background: #fee2e2; border-radius: 8px; border: 1px solid #fca5a5;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #991b1b; margin-bottom: 12px;">Analysis Failed</h3>
                    <p style="color: #64748b; line-height: 1.6; margin: 0; font-size: 14px;">The AI prediction analysis could not be completed. Please try again or contact support.</p>
                </div>
                @elseif($prediction->status === 'processing')
                <div style="margin-bottom: 32px; text-align: center; padding: 40px 20px; background: #fef3c7; border-radius: 8px; border: 1px solid #fde68a;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #92400e; margin-bottom: 12px;">Processing...</h3>
                    <p style="color: #64748b; line-height: 1.6; margin: 0; font-size: 14px;">Your prediction analysis is being processed by AI. This may take a few moments.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        div[style*="max-width: 900px"] {
            padding: 16px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 20px !important;
        }
    }
</style>

<!-- Export Confirmation Modal -->
<div id="exportModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 48px; color: #10b981;">📄</span>
        </div>
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Export PDF Report</h3>
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you ready to export this prediction analysis as a PDF report?</p>
        <p id="exportTopic" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
        <p style="color: #10b981; margin-bottom: 24px; font-size: 14px; font-weight: 500;">The report will include all analysis details and AI insights.</p>
        
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button onclick="closeExportModal()" 
                    style="padding: 12px 24px; background: transparent; color: #64748b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer;">
                Cancel
            </button>
            <button id="confirmExportBtn" 
                    style="padding: 12px 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
                Export PDF
            </button>
        </div>
    </div>
</div>

<script>
// Export modal functions
let currentExportId = null;

function confirmExport(predictionId, topic) {
    currentExportId = predictionId;
    document.getElementById('exportTopic').textContent = topic;
    document.getElementById('exportModal').style.display = 'flex';
}

function closeExportModal() {
    document.getElementById('exportModal').style.display = 'none';
    currentExportId = null;
}

function exportPrediction() {
    if (!currentExportId) return;
    
    // Redirect to the export route
    window.location.href = '{{ url("/predictions") }}/' + currentExportId + '/export';
}

// Set up the confirm export button
document.getElementById('confirmExportBtn').onclick = exportPrediction;

// Close export modal when clicking outside
document.getElementById('exportModal').onclick = function(e) {
    if (e.target === this) {
        closeExportModal();
    }
};

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeExportModal();
    }
});
</script>
@endsection
