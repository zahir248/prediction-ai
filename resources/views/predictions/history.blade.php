@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 32px 16px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Header Section -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; flex-wrap: wrap; gap: 24px;">
            <div>
                <h1 style="font-size: 36px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">Prediction History</h1>
                <p style="color: #64748b; font-size: 18px; line-height: 1.6; margin: 0;">Complete history of all your AI prediction analyses</p>
            </div>
            <div>
                <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                    ➕ New Prediction Analysis
                </a>
            </div>
        </div>

        <!-- History Stats Card -->
        <div style="background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; margin-bottom: 32px; overflow: hidden;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 24px; border: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div style="display: flex; align-items: center;">
                        <div style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                            <span style="font-size: 24px; color: white;">📚</span>
                        </div>
                        <div>
                            <h2 style="font-size: 24px; font-weight: 700; color: white; margin: 0 0 4px 0;">Complete Analysis History</h2>
                            <p style="color: rgba(255, 255, 255, 0.8); margin: 0; font-size: 14px;">Track all your prediction analyses and results</p>
                        </div>
                    </div>
                    <div>
                        <div style="background: rgba(255, 255, 255, 0.2); color: white; padding: 8px 16px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.3); font-size: 14px; font-weight: 600;">
                            📊 {{ $predictions->total() }} Total Analyses
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="padding: 0;">
                @if($predictions->count() > 0)
                    <!-- History Stats Overview -->
                    <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 24px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                            <div>
                                <h6 style="color: #1e293b; font-weight: 600; margin: 0; font-size: 16px; display: flex; align-items: center;">
                                    <span style="color: #667eea; margin-right: 8px;">📈</span>
                                    History Overview
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
                    <div class="hidden-mobile" style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="padding: 20px 24px; text-align: left; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Topic & Details
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
                                               style="display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 12px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                                                View Details
                                            </a>
                                            @if($prediction->status === 'completed')
                                                <a href="{{ route('predictions.export', $prediction) }}" 
                                                   style="display: inline-block; padding: 8px 16px; background: transparent; color: #374151; text-decoration: none; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 12px; transition: all 0.3s ease;">
                                                    Export PDF
                                                </a>
                                            @endif
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
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
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

                                    <!-- Date -->
                                    <div style="text-align: center;">
                                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Date</div>
                                        <div style="font-weight: 600; color: #1e293b; font-size: 14px;">{{ $prediction->created_at->format('M d, Y') }}</div>
                                        <div style="color: #64748b; font-size: 12px;">{{ $prediction->created_at->format('H:i') }}</div>
                                    </div>
                                </div>

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
                                        <a href="{{ route('predictions.export', $prediction) }}" 
                                           style="flex: 1; display: inline-block; padding: 12px 16px; background: transparent; color: #374151; text-decoration: none; border: 1px solid #d1d5db; border-radius: 16px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; text-align: center;">
                                            Export PDF
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 80px 24px;">
                        <div style="margin-bottom: 24px;">
                            <span style="font-size: 64px; color: #cbd5e1; opacity: 0.5;">📚</span>
                        </div>
                        <h4 style="color: #64748b; margin-bottom: 16px; font-size: 20px;">No prediction history yet</h4>
                        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Create your first analysis to start building your prediction history!</p>
                        <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                            ➕ Create First Analysis
                        </a>
                    </div>
                @endif
            </div>
            
            @if($predictions->count() > 0)
                <div style="background: white; border-top: 1px solid #e2e8f0; padding: 24px;">
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

<style>
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
    
    /* Responsive design */
    @media (max-width: 1024px) {
        div[style*="padding: 32px 16px"] {
            padding: 24px 12px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 32px !important;
        }
        
        h1[style*="font-size: 36px"] {
            font-size: 28px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 20px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 20px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 24px !important;
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
        
        div[style*="font-size: 18px"] {
            font-size: 16px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 18px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 16px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 20px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 12px !important;
        }
        
        /* Stack header elements vertically on mobile */
        div[style*="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; flex-wrap: wrap; gap: 24px"] {
            flex-direction: column !important;
            align-items: stretch !important;
            text-align: center !important;
        }
        
        div[style*="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; flex-wrap: wrap; gap: 24px"] > div:last-child {
            order: -1 !important;
        }
        
        /* Make table responsive */
        div[style*="overflow-x: auto"] {
            margin: 0 -8px !important;
        }
        
        table {
            min-width: 600px !important;
        }
        
        /* Adjust table padding for mobile */
        th[style*="padding: 20px 24px"],
        td[style*="padding: 20px 24px"] {
            padding: 16px 12px !important;
        }
        
        /* Stack stats vertically on mobile */
        div[style*="display: flex; gap: 32px; flex-wrap: wrap"] {
            justify-content: center !important;
            gap: 20px !important;
        }
        
        /* Adjust emoji sizes for mobile */
        span[style*="font-size: 64px"] {
            font-size: 48px !important;
        }
        
        span[style*="font-size: 24px"] {
            font-size: 20px !important;
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
        
        div[style*="font-size: 18px"] {
            font-size: 15px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 14px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 14px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 16px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 10px !important;
        }
        
        /* Adjust table padding for very small screens */
        th[style*="padding: 20px 24px"],
        td[style*="padding: 20px 24px"] {
            padding: 12px 8px !important;
        }
        
        /* Make table scrollable on very small screens */
        table {
            min-width: 500px !important;
        }
        
        /* Adjust emoji sizes for very small screens */
        span[style*="font-size: 64px"] {
            font-size: 40px !important;
        }
        
        span[style*="font-size: 24px"] {
            font-size: 18px !important;
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
        
        div[style*="font-size: 18px"] {
            font-size: 14px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 12px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 12px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 12px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 8px !important;
        }
        
        /* Make table scrollable on very small screens */
        table {
            min-width: 400px !important;
        }
        
        /* Adjust table padding for very small screens */
        th[style*="padding: 20px 24px"],
        td[style*="padding: 20px 24px"] {
            padding: 8px 6px !important;
        }
        
        /* Adjust emoji sizes for very small screens */
        span[style*="font-size: 64px"] {
            font-size: 36px !important;
        }
        
        span[style*="font-size: 24px"] {
            font-size: 16px !important;
        }
        
        /* Make buttons full width on very small screens */
        div[style*="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; flex-wrap: wrap; gap: 24px"] a {
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
            
            /* Improve table scrolling on mobile */
            div[style*="overflow-x: auto"] {
                -webkit-overflow-scrolling: touch !important;
                scrollbar-width: thin !important;
            }
            
            div[style*="overflow-x: auto"]::-webkit-scrollbar {
                height: 6px !important;
            }
            
            div[style*="overflow-x: auto"]::-webkit-scrollbar-track {
                background: #f1f5f9 !important;
                border-radius: 3px !important;
            }
            
            div[style*="overflow-x: auto"]::-webkit-scrollbar-thumb {
                background: #cbd5e1 !important;
                border-radius: 3px !important;
            }
            
            div[style*="overflow-x: auto"]::-webkit-scrollbar-thumb:hover {
                background: #94a3b8 !important;
            }
            
            /* Mobile card improvements */
            .mobile-only {
                padding: 16px !important;
            }
            
            .mobile-only > div {
                gap: 12px !important;
            }
            
            .mobile-only > div > div {
                padding: 16px !important;
            }
        }
        
        @media (max-width: 480px) {
            /* Mobile card improvements for very small screens */
            .mobile-only {
                padding: 12px !important;
            }
            
            .mobile-only > div > div {
                padding: 12px !important;
            }
            
            .mobile-only > div > div > div:first-child {
                margin-bottom: 12px !important;
            }
            
            .mobile-only > div > div > div:nth-child(2) {
                margin-bottom: 16px !important;
            }
        }
</style>
@endsection
