@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 32px 16px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Header Section -->
        <div style="text-align: center; margin-bottom: 48px;">
            <h1 style="font-size: 48px; font-weight: 800; color: #1e293b; margin-bottom: 16px;">AI Prediction Analysis</h1>
            <p style="color: #64748b; font-size: 20px; max-width: 800px; margin: 0 auto; line-height: 1.6;">
                Professional-grade prediction analysis powered by advanced AI technology. 
                Transform your data into actionable insights with enterprise-level AI capabilities.
            </p>
        </div>

        <!-- AI Capabilities Section -->
        <div style="background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); margin-bottom: 48px; border: 1px solid #e2e8f0;">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: 36px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">Enterprise AI Capabilities</h2>
                <p style="color: #64748b; font-size: 18px; max-width: 800px; margin: 0 auto; line-height: 1.6;">
                    Powered by advanced AI technology, our system delivers professional-grade analysis with enterprise-level reliability
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <!-- Executive Summary -->
                <div style="display: flex; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; transition: all 0.3s ease; background: #f8fafc;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                        <span style="font-size: 20px; color: white;">📋</span>
                    </div>
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Executive Summary</h4>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin: 0;">Professional-grade executive summaries with key insights and strategic overviews</p>
                    </div>
                </div>

                <!-- Predictive Analytics -->
                <div style="display: flex; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; transition: all 0.3s ease; background: #f8fafc;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                        <span style="font-size: 20px; color: white;">🔮</span>
                    </div>
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Predictive Analytics</h4>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin: 0;">Advanced forecasting with timeline-based predictions and scenario analysis</p>
                    </div>
                </div>

                <!-- Strategic Insights -->
                <div style="display: flex; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; transition: all 0.3s ease; background: #f8fafc;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                        <span style="font-size: 20px; color: white;">💡</span>
                    </div>
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Strategic Insights</h4>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin: 0;">Policy implications and strategic recommendations for decision-making</p>
                    </div>
                </div>

                <!-- Risk Assessment -->
                <div style="display: flex; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; transition: all 0.3s ease; background: #f8fafc;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                        <span style="font-size: 20px; color: white;">⚠️</span>
                    </div>
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Risk Assessment</h4>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin: 0;">Comprehensive risk analysis with probability assessment and mitigation strategies</p>
                    </div>
                </div>

                <!-- Actionable Intelligence -->
                <div style="display: flex; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; transition: all 0.3s ease; background: #f8fafc;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                        <span style="font-size: 20px; color: white;">⚙️</span>
                    </div>
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Actionable Intelligence</h4>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin: 0;">Strategic recommendations and implementation guidance for immediate action</p>
                    </div>
                </div>

                <!-- Enterprise Performance -->
                <div style="display: flex; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; transition: all 0.3s ease; background: #f8fafc;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px; flex-shrink: 0;">
                        <span style="font-size: 20px; color: white;">🚀</span>
                    </div>
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Enterprise Performance</h4>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin: 0;">High-speed processing with enterprise-grade reliability and scalability</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action Section -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 48px; text-align: center; box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);">
            <h2 style="font-size: 36px; font-weight: 700; color: white; margin-bottom: 16px;">Ready to Transform Your Data?</h2>
            <p style="color: rgba(255, 255, 255, 0.9); font-size: 18px; margin-bottom: 32px; max-width: 800px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                Experience enterprise-level AI prediction analysis powered by NUJUM. 
                Create your first professional analysis in minutes.
            </p>
            <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 20px 40px; background: white; color: #667eea; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 18px; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);">
                🚀 Start Your First Analysis
            </a>
        </div>
    </div>
</div>

<style>
    /* Responsive design improvements */
    @media (max-width: 768px) {
        div[style*="padding: 32px 16px"] {
            padding: 24px 12px !important;
        }
        
        div[style*="margin-bottom: 48px"] {
            margin-bottom: 32px !important;
        }
        
        div[style*="font-size: 48px"] {
            font-size: 32px !important;
        }
        
        div[style*="font-size: 20px"] {
            font-size: 16px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 24px !important;
        }
        
        div[style*="padding: 40px"] {
            padding: 24px !important;
        }
        
        div[style*="padding: 48px"] {
            padding: 32px 24px !important;
        }
        
        div[style*="font-size: 36px"] {
            font-size: 28px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 16px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 20px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 14px !important;
        }
        
        div[style*="padding: 16px 32px"] {
            padding: 12px 24px !important;
        }
        
        div[style*="padding: 20px 40px"] {
            padding: 16px 32px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 16px !important;
        }
    }
    
    @media (max-width: 640px) {
        div[style*="padding: 32px 16px"] {
            padding: 20px 8px !important;
        }
        
        div[style*="margin-bottom: 48px"] {
            margin-bottom: 24px !important;
        }
        
        div[style*="font-size: 48px"] {
            font-size: 28px !important;
        }
        
        div[style*="font-size: 20px"] {
            font-size: 14px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 20px !important;
        }
        
        div[style*="padding: 40px"] {
            padding: 20px !important;
        }
        
        div[style*="padding: 48px"] {
            padding: 24px 16px !important;
        }
        
        div[style*="font-size: 36px"] {
            font-size: 24px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 14px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 18px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 13px !important;
        }
        
        div[style*="padding: 16px 32px"] {
            padding: 10px 20px !important;
        }
        
        div[style*="padding: 20px 40px"] {
            padding: 14px 28px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 15px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 16px !important;
        }
        
        div[style*="minmax(350px, 1fr)"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="minmax(300px, 1fr)"] {
            grid-template-columns: 1fr !important;
        }
    }
    
    @media (max-width: 480px) {
        div[style*="padding: 32px 16px"] {
            padding: 16px 4px !important;
        }
        
        div[style*="margin-bottom: 48px"] {
            margin-bottom: 20px !important;
        }
        
        div[style*="font-size: 48px"] {
            font-size: 24px !important;
        }
        
        div[style*="font-size: 20px"] {
            font-size: 13px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 16px !important;
        }
        
        div[style*="padding: 40px"] {
            padding: 16px !important;
        }
        
        div[style*="padding: 48px"] {
            padding: 20px 12px !important;
        }
        
        div[style*="font-size: 36px"] {
            font-size: 20px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 13px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 16px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 12px !important;
        }
        
        div[style*="padding: 16px 32px"] {
            padding: 8px 16px !important;
        }
        
        div[style*="padding: 20px 40px"] {
            padding: 12px 24px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 14px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 12px !important;
        }
        
        div[style*="width: 80px"] {
            width: 60px !important;
        }
        
        div[style*="height: 80px"] {
            height: 60px !important;
        }
        
        div[style*="width: 64px"] {
            width: 48px !important;
        }
        
        div[style*="height: 64px"] {
            height: 48px !important;
        }
        
        div[style*="width: 48px"] {
            width: 40px !important;
        }
        
        div[style*="height: 48px"] {
            height: 40px !important;
        }
    }
    
    /* Hover effects for cards */
    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }
    
    /* Hover effects for capability items */
    .capability-item:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        transform: translateY(-4px);
    }
    
    /* Smooth transitions */
    * {
        transition: all 0.3s ease;
    }
    
    /* Button hover effects */
    a:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2) !important;
    }
    
    /* Touch-friendly improvements for mobile */
    @media (max-width: 768px) {
        a, button {
            min-height: 44px;
            min-width: 44px;
        }
        
        div[style*="padding: 24px"] {
            padding: 20px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 12px !important;
        }
    }
</style>
@endsection
