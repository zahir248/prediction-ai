@extends('layouts.app')

@section('content')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="data-show-container" style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div class="data-show-wrapper" style="max-width: 1200px; margin: 0 auto;">
        <!-- Main Card -->
        <div class="data-show-main-card" style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
                    <div style="flex: 1;">
                        <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">{{ $dataAnalysis->file_name }}</h1>
                        <p style="color: #64748b; font-size: 13px; margin: 8px 0 0 0;">
                            Analyzed on {{ $dataAnalysis->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="data-header-actions" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a href="{{ route('data-analysis.history') }}" class="data-action-btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; background: transparent; color: #64748b; text-decoration: none; border: 2px solid #e2e8f0; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease;">
                            ← Back
                        </a>
                        <a href="{{ route('data-analysis.index') }}" class="data-action-btn" style="display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);"
                           onmouseover="this.style.opacity='0.9';"
                           onmouseout="this.style.opacity='1';">
                            New Analysis
                        </a>
                    </div>
                </div>

                <!-- Status Section -->
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Analysis Status</h2>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; 
                            @if($dataAnalysis->status === 'completed') 
                                background: #dcfce7; color: #166534;
                            @elseif($dataAnalysis->status === 'processing') 
                                background: #fef3c7; color: #92400e;
                            @elseif($dataAnalysis->status === 'failed') 
                                background: #fee2e2; color: #991b1b;
                            @else 
                                background: #e2e8f0; color: #475569;
                            @endif">
                            {{ ucfirst($dataAnalysis->status) }}
                        </span>
                        @if($dataAnalysis->model_used)
                            <span style="color: #64748b; font-size: 13px;">
                                Model: {{ $dataAnalysis->model_used }}
                            </span>
                        @endif
                        @if($dataAnalysis->processing_time)
                            <span style="color: #64748b; font-size: 13px;">
                                Processing Time: {{ number_format($dataAnalysis->processing_time, 2) }}s
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        @if($dataAnalysis->status === 'processing')
            <div style="background: white; border-radius: 20px; padding: 48px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); text-align: center;">
                <div style="font-size: 64px; margin-bottom: 24px;">⏳</div>
                <h2 style="font-size: 24px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">Processing Your Data</h2>
                <p style="color: #64748b; font-size: 16px;">Please wait while we analyze your Excel file...</p>
            </div>
        @elseif($dataAnalysis->status === 'failed')
            <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 24px; border-radius: 12px; margin-bottom: 24px;">
                <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 8px;">Analysis Failed</h3>
                <p style="margin: 0;">{{ $dataAnalysis->error_message ?? 'An error occurred during analysis.' }}</p>
            </div>
        @elseif($dataAnalysis->status === 'completed')
            @include('data-analysis.partials.analysis-content', ['dataAnalysis' => $dataAnalysis])
        @endif
    </div>
</div>

@endsection

