@extends('layouts.app')

@section('content')
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    .cursor-layout {
        display: flex;
        height: calc(100vh - 72px);
        background: #ffffff;
        overflow: hidden;
    }
    
    .cursor-sidebar {
        width: 400px;
        background: #fafafa;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        position: relative;
        z-index: 1;
    }
    
    .cursor-main {
        flex: 1;
        background: #ffffff;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        border-left: 1px solid #e5e7eb;
        position: relative;
    }
    
    .cursor-sidebar-header {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
    }
    
    .cursor-sidebar-content {
        flex: 1;
        padding: 16px;
        padding-bottom: 16px;
        overflow-y: auto;
        overflow-x: visible;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    
    .cursor-main-content {
        flex: 1;
        padding: 24px;
        padding-bottom: 120px;
        max-width: 100%;
        width: 100%;
        position: relative;
        z-index: 1;
        min-height: 100%;
    }
    
    .cursor-section {
        margin-bottom: 24px;
    }
    
    .cursor-section-title {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        padding-bottom: 6px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .metric-card {
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }
    
    .metric-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    
    .metric-value {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 4px;
    }
    
    .metric-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }
    
    .metric-description {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }
    
    .analytics-section {
        margin-bottom: 32px;
    }
    
    .analytics-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    /* Custom scrollbar for sidebar */
    .cursor-sidebar-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .cursor-sidebar-content::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    .cursor-sidebar-content::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    .cursor-sidebar-content::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    .cursor-main::-webkit-scrollbar {
        width: 6px;
    }
    
    .cursor-main::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    .cursor-main::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    .cursor-main::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    @media (max-width: 1024px) {
        .cursor-layout {
            flex-direction: column;
            height: auto;
        }
        
        .cursor-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #e5e7eb;
            order: 1;
            max-height: 50vh;
        }
        
        .cursor-main {
            border-left: none;
            border-top: 1px solid #e5e7eb;
            order: 2;
        }
    }
    
    @media (max-width: 768px) {
        .cursor-main-content {
            padding: 20px 16px;
            padding-bottom: 80px;
        }
    }
</style>

<div class="cursor-layout">
    <!-- Left Panel: Date Range & Key Metrics -->
    <div class="cursor-sidebar">
        <div class="cursor-sidebar-header">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Analytics Dashboard</h2>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Track your NUJUM analysis usage</p>
            </div>

        <div class="cursor-sidebar-content">
            <!-- Filter Section -->
            <div class="cursor-section">
                <div class="cursor-section-title">
                    <span>Filter</span>
                </div>
                <form method="GET" action="{{ route('predictions.analytics') }}" id="dateRangeForm">
                    <!-- Date From and To Row -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                        <!-- Date From -->
                        <div>
                            <label for="start_date" style="display: block; margin-bottom: 4px; font-weight: 600; color: #374151; font-size: 11px;">From Date</label>
                            <input type="date" 
                                   id="start_date" 
                                   name="start_date" 
                               value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}"
                                   style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; transition: all 0.15s ease; background: #ffffff;">
                    </div>
                        
                        <!-- Date To -->
                        <div>
                            <label for="end_date" style="display: block; margin-bottom: 4px; font-weight: 600; color: #374151; font-size: 11px;">To Date</label>
                            <input type="date" 
                                   id="end_date" 
                                   name="end_date" 
                               value="{{ request('end_date', now()->format('Y-m-d')) }}"
                                   style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; transition: all 0.15s ease; background: #ffffff;">
                    </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 6px;">
                        <a href="{{ route('predictions.analytics') }}" 
                           style="flex: 1; padding: 6px 10px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 4px; font-weight: 500; font-size: 11px; text-decoration: none; text-align: center; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                            <i class="bi bi-x-circle" style="font-size: 11px;"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
            </div>

    <!-- Right Panel: Detailed Analytics -->
    <div class="cursor-main scrollable">
        <div class="cursor-main-content">
            <!-- Key Metrics -->
            <div class="analytics-section">
                <div class="analytics-section-title">Key Metrics</div>
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
                        <div style="font-size: 12px; color: #9ca3af;">NUJUM processing units</div>
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

                    <!-- Performance Metrics -->
            <div class="analytics-section">
                <div class="analytics-section-title">Performance Metrics</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
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

            <!-- Usage Trends -->
            <div class="analytics-section">
                <div class="analytics-section-title">Usage Trends</div>
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

            <!-- Prediction Period Breakdown -->
            <div class="analytics-section">
                <div class="analytics-section-title">Prediction Period Usage</div>
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
        </div>
    </div>
</div>

<script>
// Auto-submit on date change
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const dateRangeForm = document.getElementById('dateRangeForm');
    
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            dateRangeForm.submit();
        });
    }
    
    if (endDateInput) {
        endDateInput.addEventListener('change', function() {
            dateRangeForm.submit();
        });
    }
});
</script>
@endsection
