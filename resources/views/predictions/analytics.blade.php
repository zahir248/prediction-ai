@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 16px 8px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Header Section -->
        <div style="text-align: center; margin-bottom: 32px;">
            <h1 style="font-size: clamp(28px, 8vw, 48px); font-weight: 800; color: #1e293b; margin-bottom: 16px;">My Analytics Dashboard</h1>
            <p style="color: #64748b; font-size: clamp(16px, 4vw, 20px); max-width: 800px; margin: 0 auto; line-height: 1.6;">
                Track your AI analysis usage, costs, and performance metrics with comprehensive insights
            </p>
        </div>

        <!-- Date Range Selector -->
        <div style="background: white; border-radius: 20px; padding: clamp(20px, 5vw, 40px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); margin-bottom: 32px; border: 1px solid #e2e8f0;">
            <div style="text-align: center; margin-bottom: 24px;">
                <h2 style="font-size: clamp(20px, 6vw, 28px); font-weight: 700; color: #1e293b; margin-bottom: 16px;">Date Range Selection</h2>
                <p style="color: #64748b; font-size: clamp(14px, 3.5vw, 16px);">Select the time period for your analytics overview</p>
            </div>
            
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: clamp(16px, 4vw, 24px); align-items: end; @media (max-width: 640px) { grid-template-columns: 1fr; }">
                <div>
                    <label for="start_date" style="display: block; margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Start Date
                    </label>
                    <input type="date" id="start_date" name="start_date" 
                           value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}"
                           style="width: 100%; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb;">
                </div>
                <div>
                    <label for="end_date" style="display: block; margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                        End Date
                    </label>
                    <input type="date" id="end_date" name="end_date" 
                           value="{{ request('end_date', now()->format('Y-m-d')) }}"
                           style="width: 100%; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb;">
                </div>
                <div>
                    <button type="submit" style="width: 100%; padding: 16px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                        Update Analytics
                    </button>
                </div>
                <div>
                    <a href="{{ route('predictions.analytics') }}" style="width: 100%; padding: 16px 20px; background: #f3f4f6; color: #374151; border: 2px solid #e5e7eb; border-radius: 12px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        Clear Filter
                    </a>
                </div>
            </form>
        </div>

        <!-- Key Metrics Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: clamp(16px, 4vw, 24px); margin-bottom: 32px;">
            <!-- Total Analyses -->
            <div style="background: white; border-radius: 20px; padding: clamp(20px, 5vw, 32px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; border-left: 6px solid #667eea;">
                <div style="display: flex; align-items: center; margin-bottom: clamp(16px, 4vw, 24px); flex-wrap: wrap;">
                    <div style="width: clamp(40px, 10vw, 56px); height: clamp(40px, 10vw, 56px); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-right: clamp(12px, 3vw, 20px); margin-bottom: 8px;">
                        <span style="font-size: clamp(18px, 4.5vw, 24px); color: white;">📊</span>
                    </div>
                    <div>
                        <h3 style="font-size: clamp(12px, 3vw, 14px); font-weight: 600; color: #667eea; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Total Analyses</h3>
                        <p style="font-size: clamp(24px, 6vw, 32px); font-weight: 800; color: #1e293b; margin: 0;">{{ number_format($analytics['total_analyses']) }}</p>
                    </div>
                </div>
                <p style="color: #64748b; font-size: clamp(12px, 3vw, 14px); margin: 0;">Your completed AI analysis sessions</p>
            </div>

            <!-- Total Tokens -->
            <div style="background: white; border-radius: 20px; padding: clamp(20px, 5vw, 32px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; border-left: 6px solid #10b981;">
                <div style="display: flex; align-items: center; margin-bottom: clamp(16px, 4vw, 24px); flex-wrap: wrap;">
                    <div style="width: clamp(40px, 10vw, 56px); height: clamp(40px, 10vw, 56px); background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-right: clamp(12px, 3vw, 20px); margin-bottom: 8px;">
                        <span style="font-size: clamp(18px, 4.5vw, 24px); color: white;">⚡</span>
                    </div>
                    <div>
                        <h3 style="font-size: clamp(12px, 3vw, 14px); font-weight: 600; color: #10b981; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Total Tokens</h3>
                        <p style="font-size: clamp(24px, 6vw, 32px); font-weight: 800; color: #1e293b; margin: 0;">{{ number_format($analytics['total_tokens']) }}</p>
                    </div>
                </div>
                <p style="color: #64748b; font-size: clamp(12px, 3vw, 14px); margin: 0;">AI processing units consumed</p>
            </div>

            <!-- Total Cost -->
            <div style="background: white; border-radius: 20px; padding: clamp(20px, 5vw, 32px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; border-left: 6px solid #f59e0b;">
                <div style="display: flex; align-items: center; margin-bottom: clamp(16px, 4vw, 24px); flex-wrap: wrap;">
                    <div style="width: clamp(40px, 10vw, 56px); height: clamp(40px, 10vw, 56px); background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-right: clamp(12px, 3vw, 20px); margin-bottom: 8px;">
                        <span style="font-size: clamp(18px, 4.5vw, 24px); color: white;">💰</span>
                    </div>
                    <div>
                        <h3 style="font-size: clamp(12px, 3vw, 14px); font-weight: 600; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Total Cost</h3>
                        <p style="font-size: clamp(24px, 6vw, 32px); font-weight: 800; color: #1e293b; margin: 0;">${{ number_format($analytics['total_cost'], 4) }}</p>
                    </div>
                </div>
                <p style="color: #64748b; font-size: clamp(12px, 3vw, 14px); margin: 0;">Estimated API usage costs</p>
            </div>

            <!-- Success Rate -->
            <div style="background: white; border-radius: 20px; padding: clamp(20px, 5vw, 32px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; border-left: 6px solid #06b6d4;">
                <div style="display: flex; align-items: center; margin-bottom: clamp(16px, 4vw, 24px); flex-wrap: wrap;">
                    <div style="width: clamp(40px, 10vw, 56px); height: clamp(40px, 10vw, 56px); background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-right: clamp(12px, 3vw, 20px); margin-bottom: 8px;">
                        <span style="font-size: clamp(18px, 4.5vw, 24px); color: white;">✅</span>
                    </div>
                    <div>
                        <h3 style="font-size: clamp(12px, 3vw, 14px); font-weight: 600; color: #06b6d4; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Success Rate</h3>
                        <p style="font-size: clamp(24px, 6vw, 32px); font-weight: 800; color: #1e293b; margin: 0;">{{ number_format($analytics['success_rate'], 1) }}%</p>
                    </div>
                </div>
                <p style="color: #64748b; font-size: clamp(12px, 3vw, 14px); margin: 0;">Successful analysis completion rate</p>
            </div>
        </div>

        <!-- Performance Metrics and Analysis Breakdown -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: clamp(20px, 5vw, 32px); margin-bottom: 32px;">
            <!-- Performance Metrics -->
            <div style="background: white; border-radius: 20px; padding: clamp(20px, 5vw, 32px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                <h3 style="font-size: clamp(18px, 5vw, 24px); font-weight: 700; color: #1e293b; margin-bottom: clamp(16px, 4vw, 24px); display: flex; align-items: center;">
                    <span style="margin-right: clamp(8px, 2vw, 12px);">🚀</span>
                    Performance Metrics
                </h3>
                <div style="space-y: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f8fafc; border-radius: 12px;">
                        <span style="color: #64748b; font-weight: 500;">Average Processing Time</span>
                        <span style="font-weight: 700; color: #667eea; font-size: 18px;">{{ number_format($analytics['average_processing_time'], 3) }}s</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f8fafc; border-radius: 12px;">
                        <span style="color: #64748b; font-weight: 500;">Average Cost per Analysis</span>
                        <span style="font-weight: 700; color: #f59e0b; font-size: 18px;">${{ $analytics['total_analyses'] > 0 ? number_format($analytics['total_cost'] / $analytics['total_analyses'], 6) : '0.000000' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f8fafc; border-radius: 12px;">
                        <span style="color: #64748b; font-weight: 500;">Average Tokens per Analysis</span>
                        <span style="font-weight: 700; color: #10b981; font-size: 18px;">{{ $analytics['total_analyses'] > 0 ? number_format($analytics['total_tokens'] / $analytics['total_analyses']) : '0' }}</span>
                    </div>
                </div>
            </div>

            <!-- Analysis Breakdown -->
            <div style="background: white; border-radius: 20px; padding: clamp(20px, 5vw, 32px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                <h3 style="font-size: clamp(18px, 5vw, 24px); font-weight: 700; color: #1e293b; margin-bottom: clamp(16px, 4vw, 24px); display: flex; align-items: center;">
                    <span style="margin-right: clamp(8px, 2vw, 12px);">📈</span>
                    Analysis Breakdown
                </h3>
                <div style="space-y: 16px;">
                    @if(!empty($analytics['analysis_type_breakdown']))
                        @foreach($analytics['analysis_type_breakdown'] as $type => $count)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f8fafc; border-radius: 12px;">
                            <span style="color: #64748b; font-weight: 500; text-transform: capitalize;">{{ str_replace('-', ' ', $type) }}</span>
                            <span style="font-weight: 700; color: #6b7280; font-size: 18px;">{{ number_format($count) }}</span>
                        </div>
                        @endforeach
                    @else
                        <div style="text-align: center; padding: 16px; background: #f8fafc; border-radius: 12px;">
                            <span style="color: #9ca3af; font-size: 14px;">No analysis types found</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Prediction Horizon Breakdown -->
        <div style="background: white; border-radius: 20px; padding: clamp(20px, 5vw, 32px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); margin-bottom: 32px; border: 1px solid #e2e8f0;">
            <h3 style="font-size: clamp(18px, 5vw, 24px); font-weight: 700; color: #1e293b; margin-bottom: clamp(16px, 4vw, 24px); display: flex; align-items: center;">
                <span style="margin-right: clamp(8px, 2vw, 12px);">⏰</span>
                Prediction Horizon Usage
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: clamp(16px, 4vw, 24px);">
                @if(!empty($analytics['prediction_horizon_breakdown']))
                    @foreach($analytics['prediction_horizon_breakdown'] as $horizon => $count)
                    <div style="text-align: center; padding: 24px; background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 36px; font-weight: 800; color: #667eea; margin-bottom: 8px;">{{ number_format($count) }}</div>
                        <div style="color: #64748b; font-size: 14px; text-transform: capitalize;">{{ str_replace('_', ' ', $horizon) }}</div>
                    </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 24px; background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0;">
                        <div style="color: #9ca3af; font-size: 14px;">No prediction horizon data</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Usage Trends -->
        <div style="background: white; border-radius: 20px; padding: clamp(20px, 5vw, 32px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <h3 style="font-size: clamp(18px, 5vw, 24px); font-weight: 700; color: #1e293b; margin-bottom: clamp(16px, 4vw, 24px); display: flex; align-items: center;">
                <span style="margin-right: clamp(8px, 2vw, 12px);">📊</span>
                Usage Trends
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: clamp(20px, 5vw, 32px);">
                <!-- Daily Analysis Count -->
                <div>
                    <h4 style="font-size: clamp(16px, 4vw, 18px); font-weight: 600; color: #374151; margin-bottom: clamp(16px, 4vw, 20px); display: flex; align-items: center;">
                        <span style="margin-right: clamp(6px, 1.5vw, 8px);">📅</span>
                        Daily Analysis Count
                    </h4>
                    <div style="height: clamp(200px, 50vw, 250px); display: flex; align-items: end; justify-content: space-between; padding: clamp(12px, 3vw, 20px); background: #f8fafc; border-radius: 12px;">
                        @if(!empty($analytics['token_usage_trend']))
                            @foreach($analytics['token_usage_trend'] as $date => $tokens)
                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <div style="width: clamp(20px, 5vw, 30px); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px 8px 0 0; height: {{ $tokens > 0 ? ($tokens / max(array_values($analytics['token_usage_trend']))) * 180 : 0 }}px;"></div>
                                <span style="color: #64748b; font-size: clamp(10px, 2.5vw, 12px); margin-top: clamp(6px, 1.5vw, 8px);">{{ \Carbon\Carbon::parse($date)->format('M d') }}</span>
                                <span style="color: #9ca3af; font-size: clamp(9px, 2vw, 11px);">{{ $tokens }}</span>
                            </div>
                            @endforeach
                        @else
                            <div style="text-align: center; width: 100%; color: #9ca3af; font-size: 14px;">
                                No token usage data available
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Daily Cost Trend -->
                <div>
                    <h4 style="font-size: clamp(16px, 4vw, 18px); font-weight: 600; color: #374151; margin-bottom: clamp(16px, 4vw, 20px); display: flex; align-items: center;">
                        <span style="margin-right: clamp(6px, 1.5vw, 8px);">💵</span>
                        Daily Cost Trend
                    </h4>
                    <div style="height: clamp(200px, 50vw, 250px); display: flex; align-items: end; justify-content: space-between; padding: clamp(12px, 3vw, 20px); background: #f8fafc; border-radius: 12px;">
                        @if(!empty($analytics['cost_trend']))
                            @foreach($analytics['cost_trend'] as $date => $cost)
                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <div style="width: clamp(20px, 5vw, 30px); background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 8px 8px 0 0; height: {{ $cost > 0 ? ($cost / max(array_values($analytics['cost_trend']))) * 180 : 0 }}px;"></div>
                                <span style="color: #64748b; font-size: clamp(10px, 2.5vw, 12px); margin-top: clamp(6px, 1.5vw, 8px);">{{ \Carbon\Carbon::parse($date)->format('M d') }}</span>
                                <span style="color: #9ca3af; font-size: clamp(9px, 2vw, 11px);">${{ number_format($cost, 4) }}</span>
                            </div>
                            @endforeach
                        @else
                            <div style="text-align: center; width: 100%; color: #9ca3af; font-size: 14px;">
                                No cost data available
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
