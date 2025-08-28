@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 32px 16px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Header -->
        <div style="margin-bottom: 40px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px;">
                <div>
                    <h1 style="font-size: 36px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">{{ $prediction->topic }}</h1>
                    <p style="color: #64748b; font-size: 16px; margin: 0;">
                        Created on {{ $prediction->created_at->format('F d, Y \a\t g:i A') }}
                    </p>
                </div>
                <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                    <a href="{{ route('predictions.index') }}" style="display: inline-block; padding: 12px 24px; background: transparent; color: #64748b; text-decoration: none; border: 2px solid #e2e8f0; border-radius: 10px; font-weight: 600; font-size: 14px; transition: all 0.3s ease;">
                        ← Back to Dashboard
                    </a>
                    <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                        ➕ New Prediction Analysis
                    </a>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 32px;" class="prediction-layout">
            <!-- Main Content -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <!-- Status Card -->
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 20px;">Prediction Analysis Status</h2>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        <span style="padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; 
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
                        <span style="color: #64748b; font-size: 14px;">
                            Analysis Type: Future Prediction Analysis
                        </span>
                        <span style="color: #64748b; font-size: 14px;">
                            Horizon: {{ ucwords(str_replace('_', ' ', $prediction->prediction_horizon)) }}
                        </span>
                    </div>
                </div>

                <!-- Input Data -->
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 20px;">Input Data</h2>
                    <div style="background: #f8fafc; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0;">
                        <div style="margin-bottom: 16px; padding: 12px 16px; background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%); border-radius: 8px; border: 1px solid #0288d1;">
                            <p style="color: #0277bd; font-size: 14px; margin: 0; font-weight: 600;">
                                📅 <strong>Prediction Horizon:</strong> {{ ucwords(str_replace('_', ' ', $prediction->prediction_horizon)) }}
                            </p>
                        </div>
                        <p style="color: #374151; line-height: 1.6; margin: 0; white-space: pre-wrap;">{{ $prediction->input_data['text'] }}</p>
                    </div>
                </div>

                <!-- Uploaded Files -->
                @if($prediction->uploaded_files && count($prediction->uploaded_files) > 0)
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 20px;">📎 Uploaded Files</h2>
                    <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 24px; border-radius: 16px; border: 1px solid #0ea5e9;">
                        <p style="color: #0369a1; font-size: 16px; margin-bottom: 16px; font-weight: 600;">📊 Files Processed for Analysis</p>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            @foreach($prediction->uploaded_files as $index => $file)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: rgba(14, 165, 233, 0.1); border-radius: 8px; border: 1px solid #0ea5e9;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                        @if($file['extension'] === 'pdf')
                                            <span style="font-size: 20px;">📄</span>
                                        @elseif(in_array($file['extension'], ['xlsx', 'xls']))
                                            <span style="font-size: 20px;">📊</span>
                                        @elseif($file['extension'] === 'csv')
                                            <span style="font-size: 20px;">📋</span>
                                        @elseif($file['extension'] === 'txt')
                                            <span style="font-size: 20px;">📝</span>
                                        @else
                                            <span style="font-size: 20px;">📎</span>
                                        @endif
                                        <span style="font-weight: 600; color: #0369a1;">{{ $file['original_name'] }}</span>
                                    </div>
                                    <div style="font-size: 12px; color: #64748b;">
                                        Size: {{ number_format($file['size'] / 1024 / 1024, 2) }} MB | Type: {{ strtoupper($file['extension']) }}
                                    </div>
                                </div>
                                <a href="{{ Storage::url($file['stored_path']) }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   style="padding: 8px 16px; background: #0ea5e9; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease;">
                                    📥 Download
                                </a>
                            </div>
                            @endforeach
                        </div>
                        <p style="color: #0369a1; font-size: 14px; margin-top: 16px; margin-bottom: 0; opacity: 0.8;">
                            These files were processed and their content was extracted for AI analysis. The extracted text was combined with your input data for comprehensive prediction analysis.
                        </p>
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
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 20px;">Source References</h2>
                    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 24px; border-radius: 16px; border: 1px solid #f59e0b;">
                        <p style="color: #92400e; font-size: 16px; margin-bottom: 16px; font-weight: 600;">📚 Additional Source Information</p>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            @foreach($prediction->source_urls as $index => $sourceUrl)
                            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: rgba(146, 64, 14, 0.1); border-radius: 8px;">
                                <span style="color: #92400e; font-weight: 600; min-width: 60px;">Source {{ $index + 1 }}:</span>
                                <a href="{{ $sourceUrl }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   style="flex: 1; padding: 8px 16px; background: #92400e; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    🔗 View Source
                                </a>
                            </div>
                            @endforeach
                        </div>
                        <p style="color: #92400e; font-size: 14px; margin-top: 16px; margin-bottom: 0; opacity: 0.8;">
                            These sources were referenced during the AI analysis to provide additional context and data points for the prediction.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Scraping Metadata -->
                @if(isset($prediction->prediction_result['scraping_metadata']))
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; margin-top: 24px;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 20px;">🔍 Source Content Analysis</h2>
                    <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 24px; border-radius: 16px; border: 1px solid #16a34a;">
                        <p style="color: #166534; font-size: 16px; margin-bottom: 16px; font-weight: 600;">📊 Content Extraction Results</p>
                        <div style="color: #166534; font-size: 14px; line-height: 1.6;">
                            <p><strong>Total Sources:</strong> {{ $prediction->prediction_result['scraping_metadata']['total_sources'] }}</p>
                            <p><strong>Successfully Scraped:</strong> {{ $prediction->prediction_result['scraping_metadata']['successfully_scraped'] }}</p>
                            <p><strong>Analysis Date:</strong> {{ \Carbon\Carbon::parse($prediction->prediction_result['scraping_metadata']['scraped_at'])->format('M d, Y H:i') }}</p>
                            
                            @if(isset($prediction->prediction_result['scraping_metadata']['source_details']))
                            <div style="margin-top: 16px;">
                                <p style="font-weight: 600; margin-bottom: 12px;">Source Details:</p>
                                @foreach($prediction->prediction_result['scraping_metadata']['source_details'] as $index => $source)
                                <div style="background: rgba(22, 101, 52, 0.1); padding: 12px; border-radius: 8px; margin-bottom: 8px;">
                                    <p style="margin: 0 0 4px 0;"><strong>Source {{ $index + 1 }}:</strong> 
                                        <a href="{{ $source['url'] }}" target="_blank" style="color: #166534; text-decoration: underline;">{{ $source['url'] }}</a>
                                    </p>
                                    <p style="margin: 0 0 4px 0; font-size: 13px;"><strong>Title:</strong> {{ $source['title'] }}</p>
                                    <p style="margin: 0 0 4px 0; font-size: 13px;"><strong>Word Count:</strong> {{ $source['word_count'] }}</p>
                                    <p style="margin: 0; font-size: 13px;"><strong>Status:</strong> 
                                        <span style="color: #059669; font-weight: 600;">
                                            Success
                                        </span>
                                    </p>
                                    @if(isset($source['error']) && $source['error'])
                                    <div style="margin: 4px 0 0 0; padding: 8px; background: rgba(59, 130, 246, 0.1); border-radius: 6px; border-left: 3px solid #3b82f6;">
                                        <p style="margin: 0; font-size: 12px; color: #1e40af;">
                                            <strong>🤖 AI Note:</strong> This source was inaccessible, so the AI used its general knowledge and training data to provide relevant insights for the analysis.
                                        </p>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <p style="color: #166534; font-size: 14px; margin-top: 16px; margin-bottom: 0; opacity: 0.8;">
                            This analysis incorporates actual content extracted from the provided source URLs, ensuring predictions are based on real, current data rather than general knowledge.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Source Analysis Section -->
                @if($prediction->source_urls && count($prediction->source_urls) > 0 && isset($prediction->prediction_result['source_analysis']))
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; margin-top: 24px;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 20px;">📊 Source Analysis & Influence</h2>
                    <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 24px; border-radius: 16px; border: 1px solid #0ea5e9;">
                        <p style="color: #0c4a6e; font-size: 16px; margin-bottom: 16px; font-weight: 600;">🔍 How Sources Influenced This Analysis</p>
                        <div style="color: #0c4a6e; font-size: 14px; line-height: 1.6;">
                            {!! nl2br(e($prediction->prediction_result['source_analysis'])) !!}
                        </div>
                        <p style="color: #0c4a6e; font-size: 14px; margin-top: 16px; margin-bottom: 0; opacity: 0.8;">
                            This analysis shows how each provided source contributed to specific predictions and conclusions, ensuring transparency and traceability of insights.
                        </p>
                    </div>
                </div>
                @endif

                <!-- AI Results -->
                @if(($prediction->status === 'completed' || $prediction->status === 'completed_with_warnings') && $prediction->prediction_result)
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 24px;">AI Future Prediction Analysis Results</h2>
                    
                    @if(isset($prediction->prediction_result['note']))
                        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #f59e0b; color: #92400e; padding: 20px; border-radius: 16px; margin-bottom: 24px;">
                            <strong>Note:</strong> {{ $prediction->prediction_result['note'] }}
                        </div>
                    @endif
                    
                    @if(isset($prediction->prediction_result['raw_response']))
                        <!-- Raw Response Display -->
                        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #f59e0b; color: #92400e; padding: 20px; border-radius: 16px; margin-bottom: 24px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <span style="font-size: 20px;">⚠️</span>
                                <strong style="font-size: 16px;">Partial Analysis Results</strong>
                            </div>
                            <p style="margin-bottom: 16px;">The AI analysis was completed but the response may be incomplete. Below is the raw output from the AI:</p>
                        </div>
                        
                        <div style="background: #f8fafc; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0;">
                            <h4 style="font-weight: 700; color: #374151; margin-bottom: 16px; font-size: 18px;">📄 AI Analysis Output</h4>
                            <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #d1d5db; max-height: 600px; overflow-y: auto;">
                                <pre style="white-space: pre-wrap; color: #374151; line-height: 1.6; margin: 0; font-family: 'Courier New', monospace; font-size: 14px;">{{ $prediction->prediction_result['raw_response'] }}</pre>
                            </div>
                        </div>
                    @elseif(isset($prediction->prediction_result['title']))
                        @php $report = $prediction->prediction_result; @endphp
                        
                        <div style="display: flex; flex-direction: column; gap: 24px;">
                            <!-- Title -->
                            <div style="text-align: center; border-bottom: 2px solid #bfdbfe; padding-bottom: 24px;">
                                <h3 style="font-size: 28px; font-weight: 700; color: #1e40af; margin-bottom: 12px;">{{ $report['title'] }}</h3>
                                @if(isset($report['prediction_horizon']))
                                <p style="color: #64748b; font-size: 16px; margin: 0;">Prediction Horizon: {{ $report['prediction_horizon'] }}</p>
                                @endif
                            </div>
                            
                            <!-- Executive Summary -->
                            @if(isset($report['executive_summary']))
                            <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 24px; border-radius: 16px; border: 1px solid #bfdbfe;">
                                <h4 style="font-weight: 700; color: #1e40af; margin-bottom: 16px; font-size: 18px;">📋 Executive Summary</h4>
                                @if(is_array($report['executive_summary']))
                                    <div style="color: #1e40af; line-height: 1.6;">
                                        @foreach($report['executive_summary'] as $key => $value)
                                            @if(is_string($value))
                                                <p style="margin-bottom: 12px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                            @elseif(is_array($value))
                                                <div style="margin-bottom: 16px;">
                                                    <p style="font-weight: 600; margin-bottom: 8px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                    <ul style="margin: 0; padding-left: 20px;">
                                                        @foreach($value as $item)
                                                            <li style="margin-bottom: 4px;">{{ is_string($item) ? $item : json_encode($item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p style="color: #1e40af; line-height: 1.6; margin: 0;">{{ $report['executive_summary'] }}</p>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Current Situation -->
                            @if(isset($report['current_situation']))
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px; border-radius: 16px; border: 1px solid #cbd5e1;">
                                <h4 style="font-weight: 700; color: #374151; margin-bottom: 16px; font-size: 18px;">🌍 Current Situation & Future Implications</h4>
                                @if(is_array($report['current_situation']))
                                    <div style="color: #374151; line-height: 1.6;">
                                        @foreach($report['current_situation'] as $key => $value)
                                            @if(is_string($value))
                                                <p style="margin-bottom: 12px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                            @elseif(is_array($value))
                                                <div style="margin-bottom: 16px;">
                                                    <p style="font-weight: 600; margin-bottom: 8px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                    <ul style="margin: 0; padding-left: 20px;">
                                                        @foreach($value as $item)
                                                            <li style="margin-bottom: 4px;">{{ is_string($item) ? $item : json_encode($item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p style="color: #374151; line-height: 1.6; margin: 0;">{{ $report['current_situation'] }}</p>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Key Factors -->
                            @if(isset($report['key_factors']) && is_array($report['key_factors']) && count($report['key_factors']) > 0)
                            <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 24px; border-radius: 16px; border: 1px solid #bbf7d0;">
                                <h4 style="font-weight: 700; color: #166534; margin-bottom: 16px; font-size: 18px;">🔑 Key Factors for Future Development</h4>
                                <ul style="margin: 0; padding-left: 20px; color: #166534; line-height: 1.6;">
                                    @foreach($report['key_factors'] as $factor)
                                        <li style="margin-bottom: 8px;">
                                            @if(is_array($factor))
                                                <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid #bbf7d0; margin-top: 8px;">
                                                    @foreach($factor as $key => $value)
                                                        @if(is_string($value))
                                                            <p style="margin-bottom: 8px; color: #166534;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                                        @elseif(is_array($value))
                                                            <div style="margin-bottom: 12px;">
                                                                <p style="font-weight: 600; margin-bottom: 6px; color: #166534;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                                <ul style="margin: 0; padding-left: 16px;">
                                                                    @foreach($value as $item)
                                                                        <li style="margin-bottom: 4px; color: #166534;">{{ is_string($item) ? $item : json_encode($item) }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ $factor }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            <!-- Future Predictions -->
                            @if(isset($report['future_predictions']) && is_array($report['future_predictions']) && count($report['future_predictions']) > 0)
                            <div style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); padding: 24px; border-radius: 16px; border: 1px solid #e9d5ff;">
                                <h4 style="font-weight: 700; color: #7c3aed; margin-bottom: 16px; font-size: 18px;">🔮 Future Predictions</h4>
                                <ul style="margin: 0; padding-left: 20px; color: #7c3aed; line-height: 1.6;">
                                    @foreach($report['future_predictions'] as $prediction)
                                        <li style="margin-bottom: 8px;">
                                            @if(is_array($prediction))
                                                <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid #e9d5ff; margin-top: 8px;">
                                                    @foreach($prediction as $key => $value)
                                                        @if(is_string($value))
                                                            <p style="margin-bottom: 8px; color: #7c3aed;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                                        @elseif(is_array($value))
                                                            <div style="margin-bottom: 12px;">
                                                                <p style="font-weight: 600; margin-bottom: 6px; color: #7c3aed;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                                <ul style="margin: 0; padding-left: 16px;">
                                                                    @foreach($value as $item)
                                                                        <li style="margin-bottom: 4px; color: #7c3aed;">{{ is_string($item) ? $item : json_encode($item) }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ $prediction }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            <!-- Policy Implications -->
                            @if(isset($report['policy_implications']))
                            <div style="background: linear-gradient(135deg, #fef7ee 0%, #fed7aa 100%); padding: 24px; border-radius: 16px; border: 1px solid #fdba74;">
                                <h4 style="font-weight: 700; color: #c2410c; margin-bottom: 16px; font-size: 18px;">⚖️ Policy Implications</h4>
                                @if(is_array($report['policy_implications']))
                                    <div style="color: #c2410c; line-height: 1.6;">
                                        @foreach($report['policy_implications'] as $key => $value)
                                            @if(is_string($value))
                                                <p style="margin-bottom: 12px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                            @elseif(is_array($value))
                                                <div style="margin-bottom: 16px;">
                                                    <p style="font-weight: 600; margin-bottom: 8px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                    <ul style="margin: 0; padding-left: 20px;">
                                                        @foreach($value as $item)
                                                            <li style="margin-bottom: 4px;">{{ is_string($item) ? $item : json_encode($item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p style="color: #c2410c; line-height: 1.6; margin: 0;">{{ $report['policy_implications'] }}</p>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Risk Assessment -->
                            @if(isset($report['risk_assessment']))
                            <div style="background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%); padding: 24px; border-radius: 16px; border: 1px solid #fca5a5;">
                                <h4 style="font-weight: 700; color: #991b1b; margin-bottom: 16px; font-size: 18px;">⚠️ Risk Assessment</h4>
                                @if(is_array($report['risk_assessment']))
                                    <div style="color: #991b1b; line-height: 1.6;">
                                        @foreach($report['risk_assessment'] as $key => $value)
                                            @if(is_string($value))
                                                <p style="margin-bottom: 12px;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                            @elseif(is_array($value))
                                                <div style="margin-bottom: 16px;">
                                                    <p style="font-weight: 600; margin-bottom: 8px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                    <ul style="margin: 0; padding-left: 20px;">
                                                        @foreach($value as $item)
                                                            <li style="margin-bottom: 4px;">{{ is_string($item) ? $item : json_encode($item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p style="color: #991b1b; line-height: 1.6; margin: 0;">{{ $report['risk_assessment'] }}</p>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Recommendations -->
                            @if(isset($report['recommendations']) && is_array($report['recommendations']) && count($report['recommendations']) > 0)
                            <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 24px; border-radius: 16px; border: 1px solid #7dd3fc;">
                                <h4 style="font-weight: 700; color: #0c4a6e; margin-bottom: 16px; font-size: 18px;">💡 Recommendations</h4>
                                <ul style="margin: 0; padding-left: 20px; color: #0c4a6e; line-height: 1.6;">
                                    @foreach($report['recommendations'] as $recommendation)
                                        <li style="margin-bottom: 8px;">
                                            @if(is_array($recommendation))
                                                <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid #7dd3fc; margin-top: 8px;">
                                                    @foreach($recommendation as $key => $value)
                                                        @if(is_string($value))
                                                            <p style="margin-bottom: 8px; color: #0c4a6e;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                                        @elseif(is_array($value))
                                                            <div style="margin-bottom: 12px;">
                                                                <p style="font-weight: 600; margin-bottom: 6px; color: #0c4a6e;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                                                <ul style="margin: 0; padding-left: 16px;">
                                                                    @foreach($value as $item)
                                                                        <li style="margin-bottom: 4px; color: #0c4a6e;">{{ is_string($item) ? $item : json_encode($item) }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ $recommendation }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            <!-- Fallback: Display raw data if structured data is not available -->
                            @if(!isset($report['executive_summary']) && !isset($report['current_situation']) && !isset($report['key_factors']) && !isset($report['future_predictions']) && !isset($report['policy_implications']) && !isset($report['risk_assessment']) && !isset($report['recommendations']))
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px; border-radius: 16px; border: 1px solid #cbd5e1;">
                                <h4 style="font-weight: 700; color: #374151; margin-bottom: 16px; font-size: 18px;">📊 Analysis Results</h4>
                                <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; overflow-x: auto;">
                                    <pre style="color: #374151; line-height: 1.6; margin: 0; font-family: 'Courier New', monospace; font-size: 14px;">{{ json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <!-- Fallback: Display raw prediction result if no title is available -->
                        <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px; border-radius: 16px; border: 1px solid #cbd5e1;">
                            <h4 style="font-weight: 700; color: #374151; margin-bottom: 16px; font-size: 18px;">📊 Analysis Results</h4>
                            <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; overflow-x: auto;">
                                <pre style="color: #374151; line-height: 1.6; margin: 0; font-family: 'Courier New', monospace; font-size: 14px;">{{ json_encode($prediction->prediction_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
                @elseif($prediction->status === 'failed')
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <div style="text-align: center;">
                        <div style="margin-bottom: 24px;">
                            <span style="font-size: 64px; color: #ef4444; opacity: 0.7;">❌</span>
                        </div>
                        <h3 style="font-size: 24px; font-weight: 700; color: #991b1b; margin-bottom: 16px;">Analysis Failed</h3>
                        <p style="color: #64748b; line-height: 1.6; margin: 0;">The NUJUM AI prediction analysis could not be completed. Please try again or contact support.</p>
                    </div>
                </div>
                @elseif($prediction->status === 'processing')
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <div style="text-align: center;">
                        <div style="margin-bottom: 24px;">
                            <span style="font-size: 64px; color: #f59e0b; opacity: 0.7;">🔄</span>
                        </div>
                        <h3 style="font-size: 24px; font-weight: 700; color: #92400e; margin-bottom: 16px;">Processing...</h3>
                        <p style="color: #64748b; line-height: 1.6; margin: 0;">Your prediction analysis is being processed by AI. This may take a few moments.</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <!-- Export Actions -->
                @if($prediction->status === 'completed')
                <div style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 16px;">Export Options</h3>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <button onclick="confirmExport({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); cursor: pointer; border: none; width: 100%; justify-content: center;">
                            📄 Export PDF
                        </button>
                    </div>
                </div>
                @endif

                <!-- Analysis Info -->
                <div style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 16px;">Analysis Information</h3>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="color: #64748b; font-size: 14px;">ID:</span>
                            <span style="color: #1e293b; font-weight: 600; font-size: 14px;">#{{ $prediction->id }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="color: #64748b; font-size: 14px;">Status:</span>
                            <span style="color: #1e293b; font-weight: 600; font-size: 14px;">{{ ucfirst($prediction->status) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="color: #64748b; font-size: 14px;">Created:</span>
                            <span style="color: #1e293b; font-weight: 600; font-size: 14px;">{{ $prediction->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($prediction->updated_at != $prediction->created_at)
                        <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                            <span style="color: #64748b; font-size: 14px;">Updated:</span>
                            <span style="color: #1e293b; font-weight: 600; font-size: 14px;">{{ $prediction->updated_at->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover effects for buttons and links */
    a:hover, button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
    }
    
    /* Smooth transitions */
    * {
        transition: all 0.3s ease;
    }
    
    /* Responsive design */
    @media (max-width: 1024px) {
        .prediction-layout {
            grid-template-columns: 1fr !important;
            gap: 24px !important;
        }
        
        div[style*="padding: 32px 16px"] {
            padding: 24px 12px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 32px !important;
        }
        
        h1[style*="font-size: 36px"] {
            font-size: 28px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 24px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 20px !important;
        }
    }
    
    @media (max-width: 768px) {
        div[style*="padding: 32px 16px"] {
            padding: 20px 8px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 24px !important;
        }
        
        h1[style*="font-size: 36px"] {
            font-size: 24px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 20px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 18px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 16px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 12px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 20px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 16px !important;
        }
        
        div[style*="font-size: 14px"] {
            font-size: 13px !important;
        }
        
        /* Stack header buttons vertically on mobile */
        div[style*="display: flex; gap: 16px; flex-wrap: wrap"] {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        
        div[style*="display: flex; gap: 16px; flex-wrap: wrap"] a {
            text-align: center !important;
        }
    }
    
    @media (max-width: 640px) {
        div[style*="padding: 32px 16px"] {
            padding: 16px 4px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 20px !important;
        }
        
        h1[style*="font-size: 36px"] {
            font-size: 22px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 16px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 14px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 14px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 10px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 18px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 15px !important;
        }
        
        div[style*="font-size: 14px"] {
            font-size: 12px !important;
        }
        
        /* Adjust emoji sizes for mobile */
        span[style*="font-size: 64px"] {
            font-size: 48px !important;
        }
    }
    
    @media (max-width: 480px) {
        div[style*="padding: 32px 16px"] {
            padding: 12px 2px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 16px !important;
        }
        
        h1[style*="font-size: 36px"] {
            font-size: 20px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 12px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 12px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 12px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 8px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 16px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 14px !important;
        }
        
        div[style*="font-size: 14px"] {
            font-size: 11px !important;
        }
        
        /* Adjust emoji sizes for very small screens */
        span[style*="font-size: 64px"] {
            font-size: 40px !important;
        }
        
        /* Make buttons full width on very small screens */
        div[style*="display: flex; gap: 16px; flex-wrap: wrap"] a {
            width: 100% !important;
            padding: 14px 16px !important;
        }
    }
    
    /* Touch-friendly improvements */
    @media (max-width: 768px) {
        a, button {
            min-height: 44px !important;
            min-width: 44px !important;
        }
        
        /* Prevent zoom on iOS */
        input, textarea, select {
            font-size: 16px !important;
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
