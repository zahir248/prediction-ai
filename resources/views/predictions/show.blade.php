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
                    </div>
                </div>

                <!-- Input Data -->
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 20px;">Input Data</h2>
                    <div style="background: #f8fafc; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0;">
                        <p style="color: #374151; line-height: 1.6; margin: 0; white-space: pre-wrap;">{{ $prediction->input_data['text'] }}</p>
                    </div>
                </div>

                <!-- AI Results -->
                @if($prediction->status === 'completed' && $prediction->prediction_result)
                <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 24px;">AI Future Prediction Analysis Results</h2>
                    
                    @if(isset($prediction->prediction_result['note']))
                        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #f59e0b; color: #92400e; padding: 20px; border-radius: 16px; margin-bottom: 24px;">
                            <strong>Note:</strong> {{ $prediction->prediction_result['note'] }}
                        </div>
                    @endif
                    
                    @if(isset($prediction->prediction_result['title']))
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
                        <p style="color: #64748b; line-height: 1.6; margin: 0;">The AI prediction analysis could not be completed. Please try again or contact support.</p>
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
                        <a href="{{ route('predictions.export', $prediction) }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                            📄 Export PDF
                        </a>
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
@endsection
