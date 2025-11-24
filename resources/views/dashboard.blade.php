@extends('layouts.app')

@section('content')
<div style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Main Card -->
        <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header Section -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">AI Prediction Analysis</h1>
                <p style="color: #64748b; font-size: 14px; margin: 0;">
                    Professional-grade prediction analysis powered by advanced AI technology. Transform your data into actionable insights.
                </p>
            </div>

            <!-- AI Capabilities Section -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">AI Capabilities</h2>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                    <!-- Executive Summary -->
                    <div style="padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc;">
                        <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Executive Summary</h4>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">Professional-grade executive summaries with key insights and strategic overviews</p>
                    </div>

                    <!-- Predictive Analytics -->
                    <div style="padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: #f0fdf4;">
                        <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Predictive Analytics</h4>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">Advanced forecasting with timeline-based predictions and scenario analysis</p>
                    </div>

                    <!-- Strategic Insights -->
                    <div style="padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: #eff6ff;">
                        <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Strategic Insights</h4>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">Policy implications and strategic recommendations for decision-making</p>
                    </div>

                    <!-- Risk Assessment -->
                    <div style="padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fef3c7;">
                        <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Risk Assessment</h4>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">Comprehensive risk analysis with probability assessment and mitigation strategies</p>
                    </div>

                    <!-- Actionable Intelligence -->
                    <div style="padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: #f1f5f9;">
                        <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Actionable Intelligence</h4>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">Strategic recommendations and implementation guidance for immediate action</p>
                    </div>

                    <!-- Enterprise Performance -->
                    <div style="padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fef2f2;">
                        <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Enterprise Performance</h4>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">High-speed processing with enterprise-grade reliability and scalability</p>
                    </div>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div style="padding-top: 24px; border-top: 1px solid #e2e8f0; margin-top: 24px;">
                <div style="text-align: center;">
                    <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">Ready to Transform Your Data?</h2>
                    <p style="color: #64748b; font-size: 14px; margin-bottom: 24px; line-height: 1.6;">
                        Experience enterprise-level AI prediction analysis. Create your first professional analysis in minutes.
                    </p>
                    <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                        Start Your First Analysis
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 1024px) {
        div[style*="grid-template-columns: repeat(2, 1fr)"] {
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
