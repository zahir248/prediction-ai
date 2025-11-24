@extends('layouts.app')

@section('content')
<div style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Main Card -->
        <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header Section -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">My Analytics Dashboard</h1>
                <p style="color: #64748b; font-size: 14px; margin: 0;">Track your AI analysis usage, costs, and performance metrics</p>
            </div>

            <!-- Date Range Selector Section -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Date Range Selection</h2>
                <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr auto auto; gap: 12px; align-items: end;">
                    <div>
                        <label for="start_date" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                            Start Date
                        </label>
                        <input type="date" id="start_date" name="start_date" 
                               value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}"
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; transition: all 0.3s ease; background: #f9fafb;">
                    </div>
                    <div>
                        <label for="end_date" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                            End Date
                        </label>
                        <input type="date" id="end_date" name="end_date" 
                               value="{{ request('end_date', now()->format('Y-m-d')) }}"
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; transition: all 0.3s ease; background: #f9fafb;">
                    </div>
                    <div>
                        <button type="submit" style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); white-space: nowrap;">
                            Update
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('predictions.analytics') }}" style="padding: 12px 24px; background: transparent; color: #64748b; border: 2px solid #e2e8f0; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; text-decoration: none; display: inline-block; white-space: nowrap;">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Key Metrics Section -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Key Metrics</h2>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                    <!-- Total Analyses -->
                    <div style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 28px; font-weight: 700; color: #667eea; margin-bottom: 8px;">{{ number_format($analytics['total_analyses']) }}</div>
                        <div style="font-size: 13px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Total Analyses</div>
                        <div style="font-size: 12px; color: #9ca3af;">Completed sessions</div>
                    </div>

                    <!-- Total Tokens -->
                    <div style="text-align: center; padding: 20px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                        <div style="font-size: 28px; font-weight: 700; color: #166534; margin-bottom: 8px;">{{ number_format($analytics['total_tokens']) }}</div>
                        <div style="font-size: 13px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Total Tokens</div>
                        <div style="font-size: 12px; color: #9ca3af;">AI processing units</div>
                    </div>

                    <!-- Total Cost -->
                    <div style="text-align: center; padding: 20px; background: #fef3c7; border-radius: 8px; border: 1px solid #fde68a;">
                        <div style="font-size: 28px; font-weight: 700; color: #92400e; margin-bottom: 8px;">${{ number_format($analytics['total_cost'], 4) }}</div>
                        <div style="font-size: 13px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Total Cost</div>
                        <div style="font-size: 12px; color: #9ca3af;">Estimated API costs</div>
                    </div>

                    <!-- Success Rate -->
                    <div style="text-align: center; padding: 20px; background: #eff6ff; border-radius: 8px; border: 1px solid #bfdbfe;">
                        <div style="font-size: 28px; font-weight: 700; color: #1e40af; margin-bottom: 8px;">{{ number_format($analytics['success_rate'], 1) }}%</div>
                        <div style="font-size: 13px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Success Rate</div>
                        <div style="font-size: 12px; color: #9ca3af;">Completion rate</div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics Section -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Performance Metrics</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <!-- Performance Metrics -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f8fafc; border-radius: 8px; margin-bottom: 12px;">
                            <span style="color: #64748b; font-weight: 500; font-size: 14px;">Average Processing Time</span>
                            <span style="font-weight: 700; color: #667eea; font-size: 16px;">{{ number_format($analytics['average_processing_time'], 3) }}s</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f8fafc; border-radius: 8px; margin-bottom: 12px;">
                            <span style="color: #64748b; font-weight: 500; font-size: 14px;">Avg Cost per Analysis</span>
                            <span style="font-weight: 700; color: #f59e0b; font-size: 16px;">${{ $analytics['total_analyses'] > 0 ? number_format($analytics['total_cost'] / $analytics['total_analyses'], 6) : '0.000000' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f8fafc; border-radius: 8px;">
                            <span style="color: #64748b; font-weight: 500; font-size: 14px;">Avg Tokens per Analysis</span>
                            <span style="font-weight: 700; color: #10b981; font-size: 16px;">{{ $analytics['total_analyses'] > 0 ? number_format($analytics['total_tokens'] / $analytics['total_analyses']) : '0' }}</span>
                        </div>
                    </div>

                    <!-- Analysis Breakdown -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f8fafc; border-radius: 8px; margin-bottom: 12px;">
                            <span style="color: #64748b; font-weight: 500; font-size: 14px;">Analysis Type</span>
                            <span style="color: #64748b; font-weight: 500; font-size: 14px;">Count</span>
                        </div>
                        @if(!empty($analytics['analysis_type_breakdown']))
                            @foreach($analytics['analysis_type_breakdown'] as $type => $count)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px;">
                                <span style="color: #374151; font-weight: 500; font-size: 14px; text-transform: capitalize;">{{ str_replace('-', ' ', $type) }}</span>
                                <span style="font-weight: 700; color: #1e293b; font-size: 14px;">{{ number_format($count) }}</span>
                            </div>
                            @endforeach
                        @else
                            <div style="text-align: center; padding: 16px; background: #f8fafc; border-radius: 8px;">
                                <span style="color: #9ca3af; font-size: 13px;">No analysis types found</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Prediction Period Breakdown Section -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Prediction Period Usage</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px;">
                    @if(!empty($analytics['prediction_horizon_breakdown']))
                        @foreach($analytics['prediction_horizon_breakdown'] as $horizon => $count)
                        <div style="text-align: center; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="font-size: 24px; font-weight: 700; color: #667eea; margin-bottom: 6px;">{{ number_format($count) }}</div>
                            <div style="color: #64748b; font-size: 12px; text-transform: capitalize;">{{ str_replace('_', ' ', $horizon) }}</div>
                        </div>
                        @endforeach
                    @else
                        <div style="text-align: center; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="color: #9ca3af; font-size: 13px;">No prediction period data</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Usage Trends Section -->
            <div>
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Usage Trends</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Daily Analysis Count -->
                    <div>
                        <h3 style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 16px;">Daily Analysis Count</h3>
                        <div style="min-height: 220px; display: flex; align-items: flex-end; justify-content: space-between; padding: 20px 12px 12px 12px; background: #f8fafc; border-radius: 8px; box-sizing: border-box; gap: 4px;">
                            @if(!empty($analytics['token_usage_trend']))
                                @php
                                    $maxTokens = max(array_values($analytics['token_usage_trend']));
                                    $chartHeight = 140;
                                @endphp
                                @foreach($analytics['token_usage_trend'] as $date => $tokens)
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end; flex: 1; min-width: 0; gap: 4px;">
                                    <div style="width: 100%; max-width: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 4px 4px 0 0; height: {{ $tokens > 0 && $maxTokens > 0 ? ($tokens / $maxTokens) * $chartHeight : 0 }}px; min-height: 0; flex-shrink: 0;"></div>
                                    <span style="color: #64748b; font-size: 10px; text-align: center; line-height: 1.2;">{{ \Carbon\Carbon::parse($date)->format('M d') }}</span>
                                    <span style="color: #9ca3af; font-size: 9px; text-align: center; line-height: 1.2;">{{ $tokens }}</span>
                                </div>
                                @endforeach
                            @else
                                <div style="text-align: center; width: 100%; color: #9ca3af; font-size: 13px; padding: 40px 0;">
                                    No token usage data available
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Daily Cost Trend -->
                    <div>
                        <h3 style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 16px;">Daily Cost Trend</h3>
                        <div style="min-height: 220px; display: flex; align-items: flex-end; justify-content: space-between; padding: 20px 12px 12px 12px; background: #f8fafc; border-radius: 8px; box-sizing: border-box; gap: 4px;">
                            @if(!empty($analytics['cost_trend']))
                                @php
                                    $maxCost = max(array_values($analytics['cost_trend']));
                                    $chartHeight = 140;
                                @endphp
                                @foreach($analytics['cost_trend'] as $date => $cost)
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end; flex: 1; min-width: 0; gap: 4px;">
                                    <div style="width: 100%; max-width: 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 4px 4px 0 0; height: {{ $cost > 0 && $maxCost > 0 ? ($cost / $maxCost) * $chartHeight : 0 }}px; min-height: 0; flex-shrink: 0;"></div>
                                    <span style="color: #64748b; font-size: 10px; text-align: center; line-height: 1.2;">{{ \Carbon\Carbon::parse($date)->format('M d') }}</span>
                                    <span style="color: #9ca3af; font-size: 9px; text-align: center; line-height: 1.2;">${{ number_format($cost, 4) }}</span>
                                </div>
                                @endforeach
                            @else
                                <div style="text-align: center; width: 100%; color: #9ca3af; font-size: 13px; padding: 40px 0;">
                                    No cost data available
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 1024px) {
        div[style*="grid-template-columns: 1fr 1fr auto auto"] {
            grid-template-columns: 1fr 1fr !important;
        }
        
        div[style*="grid-template-columns: repeat(4, 1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
    
    @media (max-width: 768px) {
        div[style*="max-width: 900px"] {
            padding: 16px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 20px !important;
        }
    }
</style>
@endsection
