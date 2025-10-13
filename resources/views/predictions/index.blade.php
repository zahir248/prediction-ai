@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 32px 16px;">
    <div style="max-width: 1200px; margin: 0 auto;">


        <!-- Header Section -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; flex-wrap: wrap; gap: 24px;">
            <div style="flex: 1; min-width: 300px;">
                <h1 style="font-size: 36px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">AI Prediction Analysis Dashboard</h1>
                <p style="color: #64748b; font-size: 18px; line-height: 1.6; margin: 0;">Comprehensive prediction analysis and forecasting powered by advanced AI</p>
            </div>
            <div style="flex-shrink: 0;">
                <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                    ➕ New Prediction Analysis
                </a>
            </div>
        </div>

        <!-- System Information Card -->
        <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); margin-bottom: 32px; border: 1px solid #e2e8f0;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 16px; margin: -32px -32px 24px -32px;">
                <h3 style="margin: 0; font-size: 20px; font-weight: 600; display: flex; align-items: center;">
                    <span style="margin-right: 12px;">ℹ️</span>
                    System Information
                </h3>
            </div>
            <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6; font-size: 16px;">
                Our advanced AI-powered prediction analysis system (NUJUM) provides comprehensive insights and forecasting capabilities 
                across any domain with instant, professional-grade results powered by advanced AI.
            </p>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                <div style="display: flex; align-items: center; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                        <span style="font-size: 18px; color: white;">✅</span>
                    </div>
                    <span style="font-weight: 600; color: #1e293b;">AI-Powered Analysis</span>
                </div>
                <div style="display: flex; align-items: center; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                        <span style="font-size: 18px; color: white;">⚡</span>
                    </div>
                    <span style="font-weight: 600; color: #1e293b;">Instant Results</span>
                </div>
                <div style="display: flex; align-items: center; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                        <span style="font-size: 18px; color: white;">🌍</span>
                    </div>
                    <span style="font-weight: 600; color: #1e293b;">Topic-Agnostic</span>
                </div>
                <div style="display: flex; align-items: center; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                        <span style="font-size: 18px; color: white;">⭐</span>
                    </div>
                    <span style="font-weight: 600; color: #1e293b;">Professional Quality</span>
                </div>
            </div>
        </div>

        <!-- Recent Predictions Card -->
        <div style="background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; overflow: hidden;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 24px; border: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div style="display: flex; align-items: center;">
                        <div style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                            <span style="font-size: 24px; color: white;">📊</span>
                        </div>
                        <div>
                            <h2 style="font-size: 24px; font-weight: 700; color: white; margin: 0 0 4px 0;">Recent Prediction Analyses</h2>
                            <p style="color: rgba(255, 255, 255, 0.8); margin: 0; font-size: 14px;">Monitor and manage your AI prediction results</p>
                        </div>
                    </div>
                    <div>
                        <div style="background: rgba(255, 255, 255, 0.2); color: white; padding: 8px 16px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.3); font-size: 14px; font-weight: 600;">
                            📊 {{ $predictions->count() }} Analysis
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="padding: 0;">
                @if($predictions->count() > 0)
                    <!-- Analysis Overview Stats -->
                    <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 24px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                            <div>
                                <h6 style="color: #1e293b; font-weight: 600; margin: 0; font-size: 16px; display: flex; align-items: center;">
                                    <span style="color: #667eea; margin-right: 8px;">📈</span>
                                    Analysis Overview
                                </h6>
                            </div>
                            <div style="display: flex; gap: 32px; flex-wrap: wrap;">
                                <div style="text-align: center;">
                                    <div style="font-size: 24px; font-weight: 700; color: #10b981; margin-bottom: 4px;">{{ $predictions->where('status', 'completed')->count() }}</div>
                                    <div style="font-size: 14px; color: #64748b;">Completed</div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="font-size: 24px; font-weight: 700; color: #f59e0b; margin-bottom: 4px;">{{ $predictions->where('status', 'processing')->count() }}</div>
                                    <div style="font-size: 14px; color: #64748b;">Processing</div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="font-size: 24px; font-weight: 700; color: #ef4444; margin-bottom: 4px;">{{ $predictions->where('status', 'failed')->count() }}</div>
                                    <div style="font-size: 14px; color: #64748b;">Failed</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="table-responsive hidden-mobile" style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="padding: 20px 24px; text-align: left; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Topic & Details
                                    </th>
                                    <th style="padding: 20px 24px; text-align: center; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Source
                                    </th>
                                    <th style="padding: 20px 24px; text-align: center; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Horizon
                                    </th>
                                    <th style="padding: 20px 24px; text-align: center; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Status
                                    </th>
                                    <th style="padding: 20px 24px; text-align: center; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Confidence
                                    </th>
                                    <th style="padding: 20px 24px; text-align: center; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Date & Time
                                    </th>
                                    <th style="padding: 20px 24px; text-align: center; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($predictions as $prediction)
                                <tr style="border-bottom: 1px solid #e2e8f0; transition: all 0.3s ease;">
                                    <td style="padding: 20px 24px;">
                                        <div style="display: flex; align-items: center;">
                                            <div style="width: 40px; height: 40px; background: rgba(102, 126, 234, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                                                <span style="font-size: 18px; color: #667eea;">💡</span>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px; font-size: 16px;">{{ Str::limit($prediction->topic, 60) }}</div>
                                                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                                    <span style="background: #f1f5f9; color: #374151; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                                        #{{ $prediction->id }}
                                                    </span>
                                                    <span style="color: #64748b; font-size: 12px; display: flex; align-items: center;">
                                                        {{ $prediction->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 20px 24px; text-align: center;">
                                        @if($prediction->source_urls && count($prediction->source_urls) > 0)
                                            <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                                @foreach($prediction->source_urls as $index => $sourceUrl)
                                                <a href="{{ $sourceUrl }}" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer"
                                                   style="display: inline-block; padding: 4px 8px; background: #fef3c7; color: #92400e; text-decoration: none; border-radius: 12px; font-size: 10px; font-weight: 500; transition: all 0.3s ease; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                                   title="{{ $sourceUrl }}">
                                                    🔗 S{{ $index + 1 }}
                                                </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span style="color: #64748b; font-size: 12px;">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 20px 24px; text-align: center;">
                                        <span style="background: #e0f2fe; color: #0277bd; padding: 6px 12px; border-radius: 16px; font-size: 12px; font-weight: 600; display: inline-block;">
                                            {{ ucwords(str_replace('_', ' ', $prediction->prediction_horizon)) }}
                                        </span>
                                    </td>
                                    <td style="padding: 20px 24px; text-align: center;">
                                        @if($prediction->status === 'completed')
                                            <span style="background: #dcfce7; color: #166534; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center;">
                                                Completed
                                            </span>
                                        @elseif($prediction->status === 'processing')
                                            <span style="background: #fef3c7; color: #92400e; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center;">
                                                Processing
                                            </span>
                                        @elseif($prediction->status === 'failed')
                                            <span style="background: #fee2e2; color: #991b1b; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center;">
                                                Failed
                                            </span>
                                        @else
                                            <span style="background: #f1f5f9; color: #475569; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center;">
                                                {{ ucfirst($prediction->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 20px 24px; text-align: center;">
                                        @if(isset($prediction->confidence_score) && $prediction->confidence_score !== null && is_numeric($prediction->confidence_score))
                                            <div style="display: flex; flex-direction: column; align-items: center;">
                                                <div style="font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 16px;">
                                                    {{ number_format((float) $prediction->confidence_score * 100, 1) }}%
                                                </div>
                                                <div style="width: 80px; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                                    <div style="height: 100%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); width: {{ (float) $prediction->confidence_score * 100 }}%; transition: width 0.3s ease;"></div>
                                                </div>
                                            </div>
                                        @else
                                            <span style="color: #64748b;">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 20px 24px; text-align: center;">
                                        <div style="display: flex; flex-direction: column; align-items: center;">
                                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px; font-size: 14px;">{{ $prediction->created_at->format('M d, Y') }}</div>
                                            <div style="color: #64748b; font-size: 12px;">{{ $prediction->created_at->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td style="padding: 20px 24px; text-align: center;">
                                        <div style="display: flex; flex-direction: column; gap: 8px; align-items: center;">
                                            <a href="{{ route('predictions.show', $prediction) }}" 
                                               style="width: 120px; display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 12px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3); text-align: center;">
                                                View Details
                                            </a>
                                            @if($prediction->status === 'completed')
                                                <button onclick="confirmExport({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" 
                                                        style="width: 120px; display: inline-block; padding: 8px 16px; background: transparent; color: #374151; text-decoration: none; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 12px; transition: all 0.3s ease; cursor: pointer; text-align: center;">
                                                    Export PDF
                                                </button>
                                            @endif
                                            <button onclick="confirmDelete({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" 
                                                    style="width: 120px; display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; text-decoration: none; border: none; border-radius: 8px; font-weight: 500; font-size: 12px; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3); text-align: center;">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-only" style="padding: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            @foreach($predictions as $prediction)
                            <div style="background: #f8fafc; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; transition: all 0.3s ease;">
                                <!-- Card Header -->
                                <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px;">
                                    <div style="display: flex; align-items: center; flex: 1;">
                                        <div style="width: 40px; height: 40px; background: rgba(102, 126, 234, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                                            <span style="font-size: 18px; color: #667eea;">💡</span>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 8px; font-size: 16px; line-height: 1.4;">{{ Str::limit($prediction->topic, 80) }}</div>
                                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                <span style="background: #f1f5f9; color: #374151; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                                    #{{ $prediction->id }}
                                                </span>
                                                <span style="color: #64748b; font-size: 12px;">
                                                    {{ $prediction->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                                    <!-- Status -->
                                    <div style="text-align: center;">
                                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Status</div>
                                        @if($prediction->status === 'completed')
                                            <span style="background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 16px; font-size: 11px; font-weight: 600; display: inline-block;">
                                                Completed
                                            </span>
                                        @elseif($prediction->status === 'processing')
                                            <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 16px; font-size: 11px; font-weight: 600; display: inline-block;">
                                                Processing
                                            </span>
                                        @elseif($prediction->status === 'failed')
                                            <span style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 16px; font-size: 11px; font-weight: 600; display: inline-block;">
                                                Failed
                                            </span>
                                        @else
                                            <span style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 16px; font-size: 11px; font-weight: 600; display: inline-block;">
                                                {{ ucfirst($prediction->status) }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Horizon -->
                                    <div style="text-align: center;">
                                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Horizon</div>
                                        <span style="background: #e0f2fe; color: #0277bd; padding: 6px 12px; border-radius: 16px; font-size: 11px; font-weight: 600; display: inline-block;">
                                            {{ ucwords(str_replace('_', ' ', $prediction->prediction_horizon)) }}
                                        </span>
                                    </div>

                                    <!-- Date -->
                                    <div style="text-align: center;">
                                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Date</div>
                                        <div style="font-weight: 600; color: #1e293b; font-size: 14px;">{{ $prediction->created_at->format('M d, Y') }}</div>
                                        <div style="color: #64748b; font-size: 12px;">{{ $prediction->created_at->format('H:i') }}</div>
                                    </div>
                                </div>

                                <!-- Source URL -->
                                @if($prediction->source_urls && count($prediction->source_urls) > 0)
                                <div style="margin-bottom: 20px; text-align: center;">
                                    <div style="font-size: 12px; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Source</div>
                                    <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                        @foreach($prediction->source_urls as $index => $sourceUrl)
                                        <a href="{{ $sourceUrl }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           style="display: inline-block; padding: 8px 16px; background: #fef3c7; color: #92400e; text-decoration: none; border-radius: 16px; font-size: 12px; font-weight: 500; transition: all 0.3s ease;">
                                            🔗 S{{ $index + 1 }}
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Confidence Score -->
                                @if(isset($prediction->confidence_score) && $prediction->confidence_score !== null && is_numeric($prediction->confidence_score))
                                <div style="margin-bottom: 20px;">
                                    <div style="font-size: 12px; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Confidence Score</div>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="font-weight: 700; color: #1e293b; font-size: 16px;">
                                            {{ number_format((float) $prediction->confidence_score * 100, 1) }}%
                                        </div>
                                        <div style="flex: 1; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                            <div style="height: 100%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); width: {{ (float) $prediction->confidence_score * 100 }}%; transition: width 0.3s ease;"></div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Actions -->
                                <div style="display: flex; gap: 12px; justify-content: stretch;">
                                    <a href="{{ route('predictions.show', $prediction) }}" 
                                       style="flex: 1; display: inline-block; padding: 12px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; text-align: center; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                                        View Details
                                    </a>
                                    @if($prediction->status === 'completed')
                                        <button onclick="confirmExport({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" 
                                                style="flex: 1; display: inline-block; padding: 12px 16px; background: transparent; color: #374151; text-decoration: none; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer; text-align: center;">
                                            Export PDF
                                        </button>
                                    @endif
                                </div>
                                <!-- Delete Button -->
                                <div style="margin-top: 12px;">
                                    <button onclick="confirmDelete({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" 
                                            style="width: 100%; display: inline-block; padding: 12px 16px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; text-decoration: none; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);">
                                        Delete Analysis
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 80px 24px;">
                        <div style="margin-bottom: 24px;">
                            <span style="font-size: 64px; color: #cbd5e1; opacity: 0.5;">📊</span>
                        </div>
                        <h4 style="color: #64748b; margin-bottom: 16px; font-size: 20px;">No prediction analyses yet</h4>
                        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Create your first analysis to get started with AI-powered predictions!</p>
                        <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                            ➕ Create First Analysis
                        </a>
                    </div>
                @endif
            </div>
            
            @if($predictions->count() > 0)
                <div style="background: white; border-top: 1px solid #e2e8f0; padding: 24px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
                        <div>
                            <a href="{{ route('predictions.history') }}" style="display: inline-block; padding: 12px 24px; background: transparent; color: #64748b; text-decoration: none; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease;">
                                📚 View Full History
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                                ➕ Create New Analysis
                            </a>
                        </div>
                    </div>
                    
                    <!-- Clean Pagination -->
                    @if($predictions->hasPages())
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 24px; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 16px;">
                            <div style="display: flex; align-items: center;">
                                @if ($predictions->onFirstPage())
                                    <span style="color: #9ca3af; margin-right: 12px;">« Previous</span>
                                @else
                                    <a href="{{ $predictions->previousPageUrl() }}" style="color: #64748b; text-decoration: none; margin-right: 12px; transition: color 0.3s ease;">« Previous</a>
                                @endif

                                @if ($predictions->hasMorePages())
                                    <a href="{{ $predictions->nextPageUrl() }}" style="color: #64748b; text-decoration: none; transition: color 0.3s ease;">Next »</a>
                                @else
                                    <span style="color: #9ca3af;">Next »</span>
                                @endif
                            </div>
                            
                            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                                <div style="color: #64748b; font-size: 14px;">
                                    Showing {{ $predictions->firstItem() }} to {{ $predictions->lastItem() }} of {{ $predictions->total() }} results
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    @if (!$predictions->onFirstPage())
                                        <a href="{{ $predictions->previousPageUrl() }}" style="color: #64748b; text-decoration: none; padding: 8px; border-radius: 6px; transition: all 0.3s ease;">
                                            <span style="font-size: 16px;">‹</span>
                                        </a>
                                    @endif
                                    
                                    <div style="display: flex; gap: 4px;">
                                        @foreach ($predictions->getUrlRange(1, $predictions->lastPage()) as $page => $url)
                                            @if ($page == $predictions->currentPage())
                                                <span style="background: #667eea; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px;">{{ $page }}</span>
                                            @else
                                                <a href="{{ $url }}" style="color: #64748b; text-decoration: none; padding: 8px 12px; border-radius: 6px; transition: all 0.3s ease; hover:background: #f1f5f9;">{{ $page }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                    
                                    @if ($predictions->hasMorePages())
                                        <a href="{{ $predictions->nextPageUrl() }}" style="color: #64748b; text-decoration: none; padding: 8px; border-radius: 6px; transition: all 0.3s ease;">
                                            <span style="font-size: 16px;">›</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 48px; color: #ef4444;">⚠️</span>
        </div>
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Confirm Deletion</h3>
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you sure you want to delete this prediction analysis?</p>
        <p id="deleteTopic" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
        <p style="color: #ef4444; margin-bottom: 24px; font-size: 14px; font-weight: 500;">This action cannot be undone.</p>
        
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button onclick="closeDeleteModal()" 
                    style="padding: 12px 24px; background: transparent; color: #64748b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer;">
                Cancel
            </button>
            <button id="confirmDeleteBtn" 
                    style="padding: 12px 24px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);">
                Delete Analysis
            </button>
        </div>
    </div>
</div>

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
let currentDeleteId = null;

function confirmDelete(predictionId, topic) {
    currentDeleteId = predictionId;
    document.getElementById('deleteTopic').textContent = topic;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    currentDeleteId = null;
}

function deletePrediction() {
    if (!currentDeleteId) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ url("/predictions") }}/' + currentDeleteId;
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    
    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = '{{ csrf_token() }}';
    
    form.appendChild(methodInput);
    form.appendChild(tokenInput);
    document.body.appendChild(form);
    form.submit();
}

// Set up the confirm delete button
document.getElementById('confirmDeleteBtn').onclick = deletePrediction;

// Close modal when clicking outside
document.getElementById('deleteModal').onclick = function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
};

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeExportModal();
    }
});

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
</script>

<style>
    /* Responsive design improvements */
    @media (max-width: 768px) {
        div[style*="padding: 32px 16px"] {
            padding: 24px 12px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 24px !important;
        }
        
        div[style*="font-size: 36px"] {
            font-size: 28px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 16px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 24px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 20px !important;
        }
        
        div[style*="padding: 16px 32px"] {
            padding: 12px 24px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 20px !important;
        }
        
        div[style*="font-size: 20px"] {
            font-size: 18px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 14px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 16px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 20px !important;
        }
        
        div[style*="min-width: 300px"] {
            min-width: auto !important;
        }
        
        div[style*="minmax(250px, 1fr)"] {
            grid-template-columns: 1fr !important;
        }
    }
    
    @media (max-width: 640px) {
        div[style*="padding: 32px 16px"] {
            padding: 20px 8px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 20px !important;
        }
        
        div[style*="font-size: 36px"] {
            font-size: 24px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 14px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 20px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 16px !important;
        }
        
        div[style*="padding: 16px 32px"] {
            padding: 10px 20px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 18px !important;
        }
        
        div[style*="font-size: 20px"] {
            font-size: 16px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 13px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 12px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 16px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 12px !important;
        }
        
        div[style*="minmax(250px, 1fr)"] {
            grid-template-columns: 1fr !important;
        }
        
        /* Mobile table improvements */
        .table-responsive {
            margin: 0 -16px;
        }
        
        table {
            min-width: 600px !important;
        }
        
        th, td {
            padding: 16px 12px !important;
        }
        
        /* Stack actions vertically on mobile */
        td:last-child div {
            flex-direction: column !important;
            gap: 4px !important;
        }
    }
    
    @media (max-width: 480px) {
        div[style*="padding: 32px 16px"] {
            padding: 16px 4px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 16px !important;
        }
        
        div[style*="font-size: 36px"] {
            font-size: 20px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 12px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 16px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 12px !important;
        }
        
        div[style*="padding: 16px 32px"] {
            padding: 8px 16px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 16px !important;
        }
        
        div[style*="font-size: 20px"] {
            font-size: 14px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 11px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 8px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 12px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 8px !important;
        }
        
        /* Further reduce table padding on very small screens */
        th, td {
            padding: 12px 8px !important;
        }
        
        /* Hide less important elements on very small screens */
        div[style*="font-size: 12px"] {
            font-size: 10px !important;
        }
    }
    
    /* Hover effects for table rows */
    tbody tr:hover {
        background: #f8fafc !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    /* Hover effects for buttons and links */
    a:hover, button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2) !important;
    }
    
    /* Smooth transitions */
    * {
        transition: all 0.3s ease;
    }
    
    /* Pagination hover effects */
    .pagination a:hover {
        background: #f1f5f9 !important;
    }
    
    /* Touch-friendly improvements for mobile */
    @media (max-width: 768px) {
        a, button {
            min-height: 44px;
            min-width: 44px;
        }
        
                    /* Modal responsive improvements */
            #deleteModal > div,
            #exportModal > div {
                margin: 16px !important;
                padding: 24px !important;
                max-width: calc(100% - 32px) !important;
            }
            
            #deleteModal h3,
            #exportModal h3 {
                font-size: 18px !important;
            }
            
            #deleteModal p,
            #exportModal p {
                font-size: 14px !important;
            }
            
            #deleteModal button,
            #exportModal button {
                padding: 14px 20px !important;
                font-size: 14px !important;
            }
        
        /* Improve table scrolling on mobile */
        .table-responsive {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }
        
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    }
    
    /* Responsive grid improvements */
    @media (max-width: 640px) {
        div[style*="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr))"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
