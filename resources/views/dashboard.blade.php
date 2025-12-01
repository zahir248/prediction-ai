@extends('layouts.app')

@section('content')
<div style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Main Card -->
        <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header Section -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">AI Intelligence Platform</h1>
                <p style="color: #64748b; font-size: 14px; margin: 0;">
                    Professional-grade AI-powered analysis tools. Transform your data into actionable insights with prediction analysis and social media intelligence.
                </p>
            </div>

            <!-- Prediction Analysis Section -->
            <div style="margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid #e2e8f0;">
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Prediction Analysis</h2>
                <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 12px; padding: 24px; border: 1px solid #86efac; margin-bottom: 20px;">
                    <div style="margin-bottom: 20px;">
                        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 12px 0;">AI-Powered Prediction Analysis</h3>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
                            Transform your data into actionable insights with advanced AI prediction analysis. Get professional-grade reports with executive summaries, risk assessments, and strategic recommendations.
                        </p>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 20px;">
                        <!-- Executive Summary -->
                        <div style="padding: 16px; border-radius: 8px; border: 1px solid rgba(134, 239, 172, 0.5); background: rgba(255,255,255,0.8);">
                            <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Executive Summary</h4>
                            <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">Professional-grade executive summaries with key insights and strategic overviews</p>
                        </div>

                        <!-- Predictive Analytics -->
                        <div style="padding: 16px; border-radius: 8px; border: 1px solid rgba(134, 239, 172, 0.5); background: rgba(255,255,255,0.8);">
                            <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Predictive Analytics</h4>
                            <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">Advanced forecasting with timeline-based predictions and scenario analysis</p>
                        </div>

                        <!-- Strategic Insights -->
                        <div style="padding: 16px; border-radius: 8px; border: 1px solid rgba(134, 239, 172, 0.5); background: rgba(255,255,255,0.8);">
                            <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Strategic Insights</h4>
                            <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">Policy implications and strategic recommendations for decision-making</p>
                        </div>

                        <!-- Risk Assessment -->
                        <div style="padding: 16px; border-radius: 8px; border: 1px solid rgba(134, 239, 172, 0.5); background: rgba(255,255,255,0.8);">
                            <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Risk Assessment</h4>
                            <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">Comprehensive risk analysis with probability assessment and mitigation strategies</p>
                        </div>

                        <!-- Actionable Intelligence -->
                        <div style="padding: 16px; border-radius: 8px; border: 1px solid rgba(134, 239, 172, 0.5); background: rgba(255,255,255,0.8);">
                            <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Actionable Intelligence</h4>
                            <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">Strategic recommendations and implementation guidance for immediate action</p>
                        </div>

                        <!-- Enterprise Performance -->
                        <div style="padding: 16px; border-radius: 8px; border: 1px solid rgba(134, 239, 172, 0.5); background: rgba(255,255,255,0.8);">
                            <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Enterprise Performance</h4>
                            <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0;">High-speed processing with enterprise-grade reliability and scalability</p>
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';">
                            Start Prediction Analysis
                        </a>
                    </div>
                </div>
            </div>

            <!-- Social Media Analysis Section -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Social Media Analysis</h2>
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 12px; padding: 24px; border: 1px solid #bae6fd; margin-bottom: 20px;">
                    <div style="margin-bottom: 20px;">
                        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 12px 0;">Comprehensive Social Media Intelligence</h3>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
                            Analyze profiles across Facebook, Instagram, and TikTok with AI-powered insights. Get professional assessments, risk analysis, and engagement metrics in one comprehensive report.
                        </p>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; background: rgba(255,255,255,0.8); border-radius: 8px; font-size: 13px; color: #374151; border: 1px solid rgba(186, 230, 253, 0.5);">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #1877F2;">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span style="font-weight: 600;">Facebook</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; background: rgba(255,255,255,0.8); border-radius: 8px; font-size: 13px; color: #374151; border: 1px solid rgba(186, 230, 253, 0.5);">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                                <defs>
                                    <linearGradient id="instagram-gradient-dash" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-dash)"/>
                            </svg>
                            <span style="font-weight: 600;">Instagram</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; background: rgba(255,255,255,0.8); border-radius: 8px; font-size: 13px; color: #374151; border: 1px solid rgba(186, 230, 253, 0.5);">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                            </svg>
                            <span style="font-weight: 600;">TikTok</span>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px;">
                        <div style="padding: 16px; background: rgba(255,255,255,0.8); border-radius: 8px; border: 1px solid rgba(186, 230, 253, 0.5);">
                            <h4 style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">AI-Powered Insights</h4>
                            <p style="color: #64748b; font-size: 12px; line-height: 1.5; margin: 0;">Professional risk assessment and behavioral analysis</p>
                        </div>
                        <div style="padding: 16px; background: rgba(255,255,255,0.8); border-radius: 8px; border: 1px solid rgba(186, 230, 253, 0.5);">
                            <h4 style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Engagement Metrics</h4>
                            <p style="color: #64748b; font-size: 12px; line-height: 1.5; margin: 0;">Comprehensive engagement and performance analytics</p>
                        </div>
                        <div style="padding: 16px; background: rgba(255,255,255,0.8); border-radius: 8px; border: 1px solid rgba(186, 230, 253, 0.5);">
                            <h4 style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Multi-Platform</h4>
                            <p style="color: #64748b; font-size: 12px; line-height: 1.5; margin: 0;">Search across all platforms simultaneously</p>
                        </div>
                        <div style="padding: 16px; background: rgba(255,255,255,0.8); border-radius: 8px; border: 1px solid rgba(186, 230, 253, 0.5);">
                            <h4 style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Export Reports</h4>
                            <p style="color: #64748b; font-size: 12px; line-height: 1.5; margin: 0;">Download professional analysis reports</p>
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <a href="{{ route('social-media.index') }}" style="display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(14, 165, 233, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(14, 165, 233, 0.3)';">
                            Start Social Media Analysis
                        </a>
                    </div>
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
