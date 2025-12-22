@extends('layouts.app')

@section('content')
<div class="analytics-page-container" style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div class="analytics-content-wrapper" style="max-width: 900px; margin: 0 auto;">
        <!-- Main Card -->
        <div class="analytics-main-card" style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header Section -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">My Analytics Dashboard</h1>
                <p style="color: #64748b; font-size: 14px; margin: 0;">Track your NUJUM analysis usage, costs, and performance metrics</p>
            </div>

            <!-- Date Range Selector Section -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Date Range Selection</h2>
                <form method="GET" class="date-range-form" style="display: grid; grid-template-columns: 1fr 1fr auto auto; gap: 12px; align-items: end;">
                    <div class="form-field">
                        <label for="start_date" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                            Start Date
                        </label>
                        <input type="date" id="start_date" name="start_date" 
                               value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}"
                               class="date-input"
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; transition: all 0.3s ease; background: #f9fafb;">
                    </div>
                    <div class="form-field">
                        <label for="end_date" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                            End Date
                        </label>
                        <input type="date" id="end_date" name="end_date" 
                               value="{{ request('end_date', now()->format('Y-m-d')) }}"
                               class="date-input"
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; transition: all 0.3s ease; background: #f9fafb;">
                    </div>
                    <div class="form-button">
                        <button type="submit" class="update-btn" style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); white-space: nowrap;">
                            Update
                        </button>
                    </div>
                    <div class="form-button">
                        <a href="{{ route('predictions.analytics') }}" class="clear-btn" style="padding: 12px 24px; background: transparent; color: #64748b; border: 2px solid #e2e8f0; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; text-decoration: none; display: inline-block; white-space: nowrap;">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Key Metrics Section -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Key Metrics</h2>
                <div class="key-metrics-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
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

            <!-- Performance Metrics Section -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Performance Metrics</h2>
                <div class="performance-metrics-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
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
                <div class="usage-trends-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
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
    /* Mobile Responsive Styles */
    @media (max-width: 1024px) {
        /* Date range form - 2 columns on tablet */
        .date-range-form {
            grid-template-columns: 1fr 1fr !important;
        }
        
        .form-button {
            grid-column: span 1 !important;
        }
        
        /* Key metrics - 2 columns on tablet */
        .key-metrics-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        
        /* Performance metrics - stack on tablet */
        .performance-metrics-grid {
            grid-template-columns: 1fr !important;
        }
        
        /* Usage trends - stack on tablet */
        .usage-trends-grid {
            grid-template-columns: 1fr !important;
        }
    }
    
    @media (max-width: 768px) {
        /* Container and card padding */
        .analytics-page-container {
            padding: 16px 8px !important;
        }
        
        .analytics-content-wrapper {
            padding: 0 !important;
        }
        
        .analytics-main-card {
            padding: 20px 16px !important;
            border-radius: 12px !important;
        }
        
        /* Header section */
        h1 {
            font-size: 20px !important;
        }
        
        p[style*="color: #64748b; font-size: 14px"] {
            font-size: 12px !important;
        }
        
        /* Date range form - stack on mobile */
        .date-range-form {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }
        
        .form-field {
            width: 100% !important;
        }
        
        .form-button {
            width: 100% !important;
        }
        
        .update-btn,
        .clear-btn {
            width: 100% !important;
            padding: 12px 16px !important;
            text-align: center !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .date-input {
            font-size: 16px !important; /* Prevent zoom on iOS */
        }
        
        /* Key metrics - 2 columns on mobile */
        .key-metrics-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        
        .key-metrics-grid > div {
            padding: 16px !important;
        }
        
        .key-metrics-grid > div > div[style*="font-size: 28px"] {
            font-size: 22px !important;
        }
        
        .key-metrics-grid > div > div[style*="font-size: 13px"] {
            font-size: 12px !important;
        }
        
        .key-metrics-grid > div > div[style*="font-size: 12px"] {
            font-size: 11px !important;
        }
        
        /* Performance metrics - already stacked */
        .performance-metrics-grid {
            gap: 12px !important;
        }
        
        .performance-metrics-grid > div > div {
            padding: 12px !important;
            flex-wrap: wrap !important;
        }
        
        .performance-metrics-grid span {
            font-size: 13px !important;
        }
        
        /* Prediction period breakdown */
        div[style*="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr))"] {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
        }
        
        div[style*="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr))"] > div {
            padding: 12px !important;
        }
        
        div[style*="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr))"] > div > div[style*="font-size: 24px"] {
            font-size: 20px !important;
        }
        
        /* Usage trends - already stacked */
        .usage-trends-grid {
            gap: 16px !important;
        }
        
        .usage-trends-grid > div {
            min-width: 0 !important;
        }
        
        .usage-trends-grid h3 {
            font-size: 13px !important;
        }
        
        .usage-trends-grid > div > div[style*="min-height: 220px"] {
            min-height: 180px !important;
            padding: 16px 8px 8px 8px !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
        
        .usage-trends-grid > div > div > div {
            min-width: 30px !important;
        }
        
        .usage-trends-grid span[style*="font-size: 10px"] {
            font-size: 9px !important;
        }
        
        .usage-trends-grid span[style*="font-size: 9px"] {
            font-size: 8px !important;
        }
        
        /* Section headings */
        h2 {
            font-size: 14px !important;
        }
        
        h3 {
            font-size: 13px !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Very small screens */
        .analytics-page-container {
            padding: 12px 4px !important;
        }
        
        .analytics-main-card {
            padding: 16px 12px !important;
            border-radius: 10px !important;
        }
        
        h1 {
            font-size: 18px !important;
        }
        
        /* Date range form */
        .date-range-form {
            gap: 10px !important;
        }
        
        .update-btn,
        .clear-btn {
            padding: 10px 14px !important;
            font-size: 13px !important;
        }
        
        /* Key metrics - single column on very small screens */
        .key-metrics-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        
        .key-metrics-grid > div {
            padding: 14px !important;
        }
        
        .key-metrics-grid > div > div[style*="font-size: 28px"] {
            font-size: 20px !important;
        }
        
        /* Performance metrics */
        .performance-metrics-grid > div > div {
            padding: 10px !important;
        }
        
        .performance-metrics-grid span {
            font-size: 12px !important;
        }
        
        /* Prediction period breakdown */
        div[style*="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr))"] {
            grid-template-columns: 1fr !important;
            gap: 8px !important;
        }
        
        div[style*="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr))"] > div {
            padding: 10px !important;
        }
        
        /* Usage trends */
        .usage-trends-grid > div > div[style*="min-height: 220px"] {
            min-height: 160px !important;
            padding: 12px 6px 6px 6px !important;
        }
        
        .usage-trends-grid > div > div > div {
            min-width: 25px !important;
        }
        
        h2 {
            font-size: 13px !important;
        }
        
        h3 {
            font-size: 12px !important;
        }
    }
    
    /* Ensure all text wraps properly */
    * {
        box-sizing: border-box;
    }
    
    /* Prevent horizontal overflow */
    body {
        overflow-x: hidden;
    }
    
    /* Improve chart scrolling on mobile */
    @media (max-width: 768px) {
        .usage-trends-grid > div > div[style*="min-height: 220px"]::-webkit-scrollbar {
            height: 4px !important;
        }
        
        .usage-trends-grid > div > div[style*="min-height: 220px"]::-webkit-scrollbar-track {
            background: #f1f5f9 !important;
            border-radius: 2px !important;
        }
        
        .usage-trends-grid > div > div[style*="min-height: 220px"]::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 2px !important;
        }
    }
</style>
@endsection
