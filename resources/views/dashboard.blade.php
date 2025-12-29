@extends('layouts.app')

@section('content')
<!-- Analysis Platform Section -->
<div class="animated-ai-background" style="position: relative; min-height: calc(100vh - 72px); padding: 30px 24px; display: flex; flex-direction: column; justify-content: center; overflow: hidden;">
    <!-- Animated Background Elements -->
    <div class="ai-particle ai-particle-1"></div>
    <div class="ai-particle ai-particle-2"></div>
    <div class="ai-particle ai-particle-3"></div>
    <div class="ai-particle ai-particle-4"></div>
    <div class="ai-particle ai-particle-5"></div>
    <div class="ai-particle ai-particle-6"></div>
    <div class="ai-particle ai-particle-7"></div>
    <div class="ai-particle ai-particle-8"></div>
    <div class="ai-particle ai-particle-9"></div>
    <div class="ai-particle ai-particle-10"></div>
    <div class="ai-particle ai-particle-11"></div>
    <div class="ai-particle ai-particle-12"></div>
    <div class="ai-wave ai-wave-1"></div>
    <div class="ai-wave ai-wave-2"></div>
    <div class="ai-wave ai-wave-3"></div>
    <div class="ai-wave ai-wave-4"></div>
    <div class="ai-connection ai-connection-1"></div>
    <div class="ai-connection ai-connection-2"></div>
    <div class="ai-connection ai-connection-3"></div>
    <div class="ai-connection ai-connection-4"></div>
    <div class="ai-connection ai-connection-5"></div>
    <div class="ai-connection ai-connection-6"></div>
    
    <div style="position: relative; z-index: 10; background: transparent;">
    <!-- Hero Section -->
    <section style="max-width: 1200px; margin: 0 auto 30px; text-align: center;">
        <h1 id="typing-text" style="font-size: 38px; font-weight: 700; color: #111827; line-height: 1.1; margin-bottom: 10px; letter-spacing: -0.02em; min-height: 50px;">
            <span id="typed-text"></span><span id="cursor" style="display: inline-block; width: 3px; height: 38px; background: #667eea; margin-left: 4px; animation: blink 1s infinite;"></span>
        </h1>
        <p style="font-size: 16px; color: #6b7280; line-height: 1.5; max-width: 640px; margin: 0 auto; font-weight: 400;">
            Transform your data into actionable insights with our two powerful modules: Predictions Analysis and Social Media Analysis.
        </p>
    </section>

    <!-- Main Modules Section -->
    <section style="max-width: 1200px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
            <!-- Predictions Analysis Module -->
            <div class="animate-card-left" style="background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%); border-radius: 16px; padding: 32px; border: 1px solid #dbeafe; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; display: flex; flex-direction: column; opacity: 0; transform: translateX(-30px);" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)';">
                <h2 style="font-size: 24px; font-weight: 700; color: #111827; margin-bottom: 10px; text-align: center;">
                    Predictions Analysis
                </h2>
                <p style="font-size: 15px; color: #4b5563; line-height: 1.5; margin-bottom: 20px; text-align: center; min-height: 72px;">
                    Transform your data into actionable predictions with advanced analysis. Get executive summaries, future forecasts, risk assessments, and strategic recommendations.
                </p>
                <div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; min-height: 120px; display: flex; align-items: center;">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: #f9fafb; border-radius: 6px;">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #667eea;">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                </svg>
                            <span style="font-size: 13px; font-weight: 600; color: #374151;">Executive Summary</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: #f9fafb; border-radius: 6px;">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #ef4444;">
                                <path d="M12,1L3,5V11C3,16.55 6.16,21.74 12,23C17.84,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C15.4,11.5 16,12.4 16,13V16C16,17.1 15.1,18 14,18H10C8.9,18 8,17.1 8,16V13C8,12.4 8.6,11.5 9.2,11.5V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.5,8.7 10.5,9.5V11.5H13.5V9.5C13.5,8.7 12.8,8.2 12,8.2Z"/>
                                </svg>
                            <span style="font-size: 13px; font-weight: 600; color: #374151;">Risk Assessment</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: #f9fafb; border-radius: 6px;">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #10b981;">
                                <path d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z"/>
                                </svg>
                            <span style="font-size: 13px; font-weight: 600; color: #374151;">Future Predictions</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: #f9fafb; border-radius: 6px;">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #f59e0b;">
                                <path d="M12,2A2,2 0 0,1 14,4C14,4.74 13.6,5.39 13,5.73V7H14A7,7 0 0,1 21,14H22A1,1 0 0,1 23,15V18A1,1 0 0,1 22,19H21A7,7 0 0,1 14,26H10A7,7 0 0,1 3,19H2A1,1 0 0,1 1,18V15A1,1 0 0,1 2,14H3A7,7 0 0,1 10,7H11V5.73C10.4,5.39 10,4.74 10,4A2,2 0 0,1 12,2M7.5,13A2.5,2.5 0 0,0 5,15.5A2.5,2.5 0 0,0 7.5,18A2.5,2.5 0 0,0 10,15.5A2.5,2.5 0 0,0 7.5,13M16.5,13A2.5,2.5 0 0,0 14,15.5A2.5,2.5 0 0,0 16.5,18A2.5,2.5 0 0,0 19,15.5A2.5,2.5 0 0,0 16.5,13Z"/>
                                </svg>
                            <span style="font-size: 13px; font-weight: 600; color: #374151;">Strategic Insights</span>
                        </div>
                        </div>
                        </div>
                <a href="{{ route('predictions.create') }}" style="display: block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; text-align: center; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); margin-top: auto;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';">
                            Start Prediction Analysis
                        </a>
            </div>

            <!-- Social Media Analysis Module -->
            <div class="animate-card-right" style="background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%); border-radius: 16px; padding: 32px; border: 1px solid #dbeafe; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; display: flex; flex-direction: column; opacity: 0; transform: translateX(30px);" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)';">
                <h2 style="font-size: 24px; font-weight: 700; color: #111827; margin-bottom: 10px; text-align: center;">
                    Social Media Analysis
                </h2>
                <p style="font-size: 15px; color: #4b5563; line-height: 1.5; margin-bottom: 20px; text-align: center; min-height: 72px;">
                    Analyze profiles across Facebook, Instagram, TikTok, and X (Twitter). Get professional assessments, personality insights, and engagement metrics.
                </p>
                <div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; min-height: 120px; display: flex; align-items: center;">
                    <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: #f9fafb; border-radius: 6px;">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #1877F2;">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span style="font-size: 13px; font-weight: 600; color: #374151;">Facebook</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: #f9fafb; border-radius: 6px;">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                                <defs>
                                    <linearGradient id="instagram-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient)"/>
                            </svg>
                            <span style="font-size: 13px; font-weight: 600; color: #374151;">Instagram</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: #f9fafb; border-radius: 6px;">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                            </svg>
                            <span style="font-size: 13px; font-weight: 600; color: #374151;">TikTok</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: #f9fafb; border-radius: 6px;">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                            <span style="font-size: 13px; font-weight: 600; color: #374151;">X (Twitter)</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('social-media.index') }}" style="display: block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; text-align: center; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); margin-top: auto;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';">
                    Start Social Media Analysis
                </a>
            </div>
        </div>
    </section>
    </div>
                        </div>

<!-- Problem → Solution Section -->
<div style="background: #f9fafb; min-height: calc(100vh - 72px); padding: 30px 24px; display: flex; flex-direction: column; justify-content: center;">
    <section style="max-width: 1200px; margin: 0 auto; width: 100%;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
            <div>
                <h2 style="font-size: 12px; font-weight: 600; color: #667eea; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">
                    The Challenge
                </h2>
                <h3 style="font-size: 28px; font-weight: 700; color: #111827; line-height: 1.2; margin-bottom: 20px; letter-spacing: -0.01em;">
                    Making sense of complex data is overwhelming
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="padding: 10px 0; border-bottom: 1px solid #e5e7eb; color: #4b5563; font-size: 15px; display: flex; align-items: start; gap: 12px;">
                        <span style="color: #ef4444; font-size: 20px; line-height: 1;">×</span>
                        <span>Too much information, too little insight</span>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid #e5e7eb; color: #4b5563; font-size: 15px; display: flex; align-items: start; gap: 12px;">
                        <span style="color: #ef4444; font-size: 20px; line-height: 1;">×</span>
                        <span>Manual analysis takes days or weeks</span>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid #e5e7eb; color: #4b5563; font-size: 15px; display: flex; align-items: start; gap: 12px;">
                        <span style="color: #ef4444; font-size: 20px; line-height: 1;">×</span>
                        <span>Missing critical risks and opportunities</span>
                    </li>
                    <li style="padding: 10px 0; color: #4b5563; font-size: 15px; display: flex; align-items: start; gap: 12px;">
                        <span style="color: #ef4444; font-size: 20px; line-height: 1;">×</span>
                        <span>Unclear strategic recommendations</span>
                    </li>
                </ul>
                        </div>
            <div>
                <h2 style="font-size: 12px; font-weight: 600; color: #667eea; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">
                    Our Solution
                </h2>
                <h3 style="font-size: 28px; font-weight: 700; color: #111827; line-height: 1.2; margin-bottom: 20px; letter-spacing: -0.01em;">
                    Advanced analysis in minutes, not weeks
                </h3>
                <p style="font-size: 15px; color: #6b7280; line-height: 1.6; margin-bottom: 20px;">
                    NUJUM offers two powerful modules: Predictions Analysis for data-driven forecasting and Social Media Analysis for profile insights. Both powered by advanced technology to deliver comprehensive, actionable intelligence.
                </p>
                <div style="background: white; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; background: #f0f9ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: #667eea;">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
                                </svg>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #111827; font-size: 15px;">Instant Analysis</div>
                            <div style="color: #6b7280; font-size: 13px;">Get comprehensive reports in minutes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Feature Overview -->
<div style="background: #ffffff; min-height: calc(100vh - 72px); padding: 30px 24px; display: flex; flex-direction: column; justify-content: center;">
    <section style="max-width: 1200px; margin: 0 auto; width: 100%;">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 38px; font-weight: 700; color: #111827; line-height: 1.2; margin-bottom: 10px; letter-spacing: -0.01em;">
                Everything you need to make better decisions
            </h2>
            <p style="font-size: 16px; color: #6b7280; max-width: 600px; margin: 0 auto;">
                Powerful features designed to give you the insights you need, when you need them.
            </p>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            <!-- Feature 1 -->
            <div class="animate-fade-up" style="text-align: center; padding: 24px 20px; opacity: 0; transform: translateY(30px); transition: all 0.6s ease;">
                <div style="width: 48px; height: 48px; background: #f0f9ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #667eea;">
                        <path d="M12,2A2,2 0 0,1 14,4C14,4.74 13.6,5.39 13,5.73V7H14A7,7 0 0,1 21,14H22A1,1 0 0,1 23,15V18A1,1 0 0,1 22,19H21A7,7 0 0,1 14,26H10A7,7 0 0,1 3,19H2A1,1 0 0,1 1,18V15A1,1 0 0,1 2,14H3A7,7 0 0,1 10,7H11V5.73C10.4,5.39 10,4.74 10,4A2,2 0 0,1 12,2M7.5,13A2.5,2.5 0 0,0 5,15.5A2.5,2.5 0 0,0 7.5,18A2.5,2.5 0 0,0 10,15.5A2.5,2.5 0 0,0 7.5,13M16.5,13A2.5,2.5 0 0,0 14,15.5A2.5,2.5 0 0,0 16.5,18A2.5,2.5 0 0,0 19,15.5A2.5,2.5 0 0,0 16.5,13Z"/>
                                </svg>
                        </div>
                <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 8px;">
                    Advanced Analysis
                </h3>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5;">
                    Advanced models analyze your data to generate comprehensive insights and predictions.
                </p>
            </div>
            <!-- Feature 2 -->
            <div class="animate-fade-up" style="text-align: center; padding: 24px 20px; opacity: 0; transform: translateY(30px); transition: all 0.6s ease;">
                <div style="width: 48px; height: 48px; background: #f0fdf4; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #10b981;">
                        <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,5V19H5V5H19Z"/>
                                </svg>
                        </div>
                <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 8px;">
                    Multi-Format Support
                </h3>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5;">
                    Upload PDFs, Excel files, or paste text. We process everything seamlessly.
                </p>
            </div>
            <!-- Feature 3 -->
            <div class="animate-fade-up" style="text-align: center; padding: 24px 20px; opacity: 0; transform: translateY(30px); transition: all 0.6s ease;">
                <div style="width: 48px; height: 48px; background: #fef2f2; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #ef4444;">
                        <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4M11,16.5L18,9.5L16.59,8.09L11,13.67L7.91,10.59L6.5,12L11,16.5Z"/>
                                </svg>
                        </div>
                <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 8px;">
                    Risk Assessment
                </h3>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5;">
                    Comprehensive risk analysis with probability assessment and mitigation strategies.
                </p>
                    </div>
            <!-- Feature 4 -->
            <div class="animate-fade-up" style="text-align: center; padding: 24px 20px; opacity: 0; transform: translateY(30px); transition: all 0.6s ease;">
                <div style="width: 48px; height: 48px; background: #faf5ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #8b5cf6;">
                        <path d="M22,21H2V3H4V19H6V17H10V19H12V16H16V19H18V17H22V21Z"/>
                    </svg>
                    </div>
                <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 8px;">
                    Predictive Analytics
                </h3>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5;">
                    Timeline-based predictions from short-term to long-term horizons.
                </p>
                </div>
            <!-- Feature 5 -->
            <div class="animate-fade-up" style="text-align: center; padding: 24px 20px; opacity: 0; transform: translateY(30px); transition: all 0.6s ease;">
                <div style="width: 48px; height: 48px; background: #eff6ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #3b82f6;">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                    </svg>
            </div>
                <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 8px;">
                    Executive Reports
                </h3>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5;">
                    Professional-grade reports with executive summaries and strategic recommendations.
                </p>
        </div>
            <!-- Feature 6 -->
            <div class="animate-fade-up" style="text-align: center; padding: 24px 20px; opacity: 0; transform: translateY(30px); transition: all 0.6s ease;">
                <div style="width: 48px; height: 48px; background: #fdf2f8; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #ec4899;">
                        <path d="M18,16.08C17.24,16.08 16.56,16.38 16.04,16.85L8.91,12.7C8.96,12.47 9,12.24 9,12C9,11.76 8.96,11.53 8.91,11.3L15.96,7.19C16.5,7.69 17.21,8 18,8A3,3 0 0,0 21,5A3,3 0 0,0 18,2A3,3 0 0,0 15,5C15,5.24 15.04,5.47 15.09,5.7L8.04,9.81C7.5,9.31 6.79,9 6,9A3,3 0 0,0 3,12A3,3 0 0,0 6,15C6.79,15 7.5,14.69 8.04,14.19L15.16,18.34C15.11,18.55 15.08,18.77 15.08,19C15.08,20.61 16.39,21.91 18,21.91C19.61,21.91 20.92,20.61 20.92,19A2.92,2.92 0 0,0 18,16.08Z"/>
                    </svg>
    </div>
                <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 8px;">
                    Social Media Analysis
                </h3>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5;">
                    Analyze profiles across Facebook, Instagram, TikTok, and X for professional insights.
                </p>
            </div>
        </div>
    </section>
</div>

<!-- How It Works (3 Steps) -->
<div style="background: #f9fafb; min-height: calc(100vh - 72px); padding: 30px 24px; display: flex; flex-direction: column; justify-content: center;">
    <section style="max-width: 1200px; margin: 0 auto; width: 100%;">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 38px; font-weight: 700; color: #111827; line-height: 1.2; margin-bottom: 10px; letter-spacing: -0.01em;">
                How It Works
            </h2>
            <p style="font-size: 16px; color: #6b7280; max-width: 600px; margin: 0 auto;">
                Get comprehensive analysis in three simple steps.
            </p>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            <!-- Step 1 -->
            <div class="animate-fade-up" style="text-align: center; opacity: 0; transform: translateY(30px); transition: all 0.6s ease;">
                <div style="width: 64px; height: 64px; background: #667eea; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 28px; font-weight: 700;">
                    1
                </div>
                <h3 style="font-size: 20px; font-weight: 600; color: #111827; margin-bottom: 8px;">
                    Provide Your Input
                </h3>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5;">
                    Upload your data files, enter your topic, or provide social media profile URLs. Our system accepts multiple input formats.
                </p>
            </div>
            <!-- Step 2 -->
            <div class="animate-fade-up" style="text-align: center; opacity: 0; transform: translateY(30px); transition: all 0.6s ease;">
                <div style="width: 64px; height: 64px; background: #667eea; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 28px; font-weight: 700;">
                    2
                </div>
                <h3 style="font-size: 20px; font-weight: 600; color: #111827; margin-bottom: 8px;">
                    We Analyze & Generate
                </h3>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5;">
                    Our platform analyzes your input and generates comprehensive reports with actionable insights and recommendations.
                </p>
            </div>
            <!-- Step 3 -->
            <div class="animate-fade-up" style="text-align: center; opacity: 0; transform: translateY(30px); transition: all 0.6s ease;">
                <div style="width: 64px; height: 64px; background: #667eea; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 28px; font-weight: 700;">
                    3
                </div>
                <h3 style="font-size: 20px; font-weight: 600; color: #111827; margin-bottom: 8px;">
                    Review & Take Action
                </h3>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5;">
                    Review your comprehensive analysis, export reports, and use the insights to make informed decisions.
                </p>
            </div>
        </div>
    </section>
    </div>
</div>


<style>
    /* Tablet and iPad Responsive Design */
    /* iPad Pro 12.9" and large tablets (1024px - 1366px) */
    @media (min-width: 1024px) and (max-width: 1366px) {
        /* Analysis Platform Section */
        .animated-ai-background {
            padding: 24px 24px !important;
            min-height: calc(100vh - 72px) !important;
        }
        
        #typing-text {
            font-size: 36px !important;
            min-height: 44px !important;
            margin-bottom: 8px !important;
        }
        
        #cursor {
            height: 36px !important;
        }
        
        section p {
            font-size: 17px !important;
            margin-bottom: 20px !important;
        }
        
        /* Module cards - keep side by side */
        section > div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 16px !important;
        }
        
        .animate-card-left,
        .animate-card-right {
            padding: 20px !important;
        }
        
        .animate-card-left h2,
        .animate-card-right h2 {
            font-size: 22px !important;
            margin-bottom: 8px !important;
        }
        
        .animate-card-left p,
        .animate-card-right p {
            font-size: 15px !important;
            min-height: 50px !important;
            margin-bottom: 16px !important;
        }
        
        /* Feature Overview - 3 columns */
        section > div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 12px !important;
        }
        
        /* Problem → Solution - keep side by side */
        section > div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr 1fr !important;
            gap: 24px !important;
        }
        
        div[style*="background: #f9fafb"] {
            padding: 24px 20px !important;
        }
        
        div[style*="background: #f9fafb"] h3 {
            font-size: 26px !important;
            margin-bottom: 16px !important;
        }
        
        div[style*="background: #f9fafb"] h2 {
            margin-bottom: 10px !important;
        }
        
        div[style*="background: #f9fafb"] p,
        div[style*="background: #f9fafb"] li {
            font-size: 15px !important;
        }
        
        div[style*="background: #f9fafb"] li {
            padding: 8px 0 !important;
        }
        
        /* Feature Overview and How It Works sections */
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"],
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] {
            padding: 24px 20px !important;
        }
        
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"] h2,
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] h2 {
            font-size: 36px !important;
            margin-bottom: 20px !important;
        }
        
        /* White boxes in modules */
        .animate-card-left > div[style*="background: white"],
        .animate-card-right > div[style*="background: white"] {
            padding: 14px !important;
            min-height: 100px !important;
            margin-bottom: 16px !important;
        }
        
        /* Feature cards */
        .animate-fade-up {
            padding: 16px 12px !important;
        }
        
        .animate-fade-up > div[style*="width: 48px"] {
            width: 44px !important;
            height: 44px !important;
        }
        
        .animate-fade-up h3 {
            font-size: 17px !important;
        }
        
        .animate-fade-up p {
            font-size: 14px !important;
        }
        
        /* How It Works step badges */
        .animate-fade-up > div[style*="width: 64px"] {
            width: 60px !important;
            height: 60px !important;
            font-size: 26px !important;
        }
    }
    
    /* iPad Pro 11" and medium tablets (834px - 1024px) */
    @media (min-width: 834px) and (max-width: 1023px) {
        /* Analysis Platform Section */
        .animated-ai-background {
            padding: 20px 20px !important;
            min-height: calc(100vh - 72px) !important;
        }
        
        #typing-text {
            font-size: 34px !important;
            min-height: 42px !important;
            margin-bottom: 8px !important;
        }
        
        #cursor {
            height: 34px !important;
        }
        
        section p {
            font-size: 16px !important;
            margin-bottom: 18px !important;
        }
        
        /* Module cards - keep side by side */
        section > div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 14px !important;
        }
        
        .animate-card-left,
        .animate-card-right {
            padding: 18px !important;
        }
        
        .animate-card-left h2,
        .animate-card-right h2 {
            font-size: 21px !important;
            margin-bottom: 8px !important;
        }
        
        .animate-card-left p,
        .animate-card-right p {
            font-size: 14.5px !important;
            min-height: 48px !important;
            margin-bottom: 14px !important;
        }
        
        /* Feature Overview - 3 columns */
        section > div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 12px !important;
        }
        
        /* Problem → Solution - keep side by side */
        section > div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr 1fr !important;
            gap: 20px !important;
        }
        
        div[style*="background: #f9fafb"] {
            padding: 20px 18px !important;
        }
        
        div[style*="background: #f9fafb"] h3 {
            font-size: 25px !important;
            margin-bottom: 14px !important;
        }
        
        div[style*="background: #f9fafb"] h2 {
            margin-bottom: 8px !important;
        }
        
        div[style*="background: #f9fafb"] p,
        div[style*="background: #f9fafb"] li {
            font-size: 14.5px !important;
        }
        
        div[style*="background: #f9fafb"] li {
            padding: 7px 0 !important;
        }
        
        /* Feature Overview and How It Works sections */
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"],
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] {
            padding: 20px 18px !important;
        }
        
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"] h2,
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] h2 {
            font-size: 34px !important;
            margin-bottom: 18px !important;
        }
        
        /* White boxes in modules */
        .animate-card-left > div[style*="background: white"],
        .animate-card-right > div[style*="background: white"] {
            padding: 12px !important;
            min-height: 95px !important;
            margin-bottom: 14px !important;
        }
        
        /* Feature cards */
        .animate-fade-up {
            padding: 14px 10px !important;
        }
        
        .animate-fade-up > div[style*="width: 48px"] {
            width: 42px !important;
            height: 42px !important;
        }
        
        .animate-fade-up h3 {
            font-size: 16px !important;
        }
        
        .animate-fade-up p {
            font-size: 13.5px !important;
        }
        
        /* How It Works step badges */
        .animate-fade-up > div[style*="width: 64px"] {
            width: 58px !important;
            height: 58px !important;
            font-size: 25px !important;
        }
    }
    
    /* iPad Air and small tablets (768px - 833px) */
    @media (min-width: 768px) and (max-width: 833px) {
        /* Analysis Platform Section */
        .animated-ai-background {
            padding: 18px 18px !important;
            min-height: calc(100vh - 72px) !important;
        }
        
        #typing-text {
            font-size: 32px !important;
            min-height: 40px !important;
            margin-bottom: 8px !important;
        }
        
        #cursor {
            height: 32px !important;
        }
        
        section p {
            font-size: 15px !important;
            margin-bottom: 16px !important;
        }
        
        /* Module cards - keep side by side but with adjusted spacing */
        section > div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        
        .animate-card-left,
        .animate-card-right {
            padding: 16px !important;
        }
        
        .animate-card-left h2,
        .animate-card-right h2 {
            font-size: 20px !important;
            margin-bottom: 6px !important;
        }
        
        .animate-card-left p,
        .animate-card-right p {
            font-size: 14px !important;
            min-height: 45px !important;
            margin-bottom: 12px !important;
        }
        
        /* Feature Overview - 2 columns for better fit */
        section > div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        
        /* Problem → Solution - stack on smaller tablets */
        section > div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
        
        div[style*="background: #f9fafb"] {
            padding: 18px 16px !important;
        }
        
        div[style*="background: #f9fafb"] h3 {
            font-size: 24px !important;
            margin-bottom: 12px !important;
        }
        
        div[style*="background: #f9fafb"] h2 {
            margin-bottom: 8px !important;
        }
        
        div[style*="background: #f9fafb"] p,
        div[style*="background: #f9fafb"] li {
            font-size: 14px !important;
        }
        
        div[style*="background: #f9fafb"] li {
            padding: 6px 0 !important;
        }
        
        /* Feature Overview and How It Works sections */
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"],
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] {
            padding: 18px 16px !important;
        }
        
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"] h2,
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] h2 {
            font-size: 32px !important;
            margin-bottom: 16px !important;
        }
        
        /* White boxes in modules */
        .animate-card-left > div[style*="background: white"],
        .animate-card-right > div[style*="background: white"] {
            padding: 10px !important;
            min-height: 90px !important;
            margin-bottom: 12px !important;
        }
        
        /* Feature cards */
        .animate-fade-up {
            padding: 12px 8px !important;
        }
        
        .animate-fade-up > div[style*="width: 48px"] {
            width: 40px !important;
            height: 40px !important;
        }
        
        .animate-fade-up h3 {
            font-size: 15px !important;
        }
        
        .animate-fade-up p {
            font-size: 13px !important;
        }
        
        /* How It Works step badges */
        .animate-fade-up > div[style*="width: 64px"] {
            width: 56px !important;
            height: 56px !important;
            font-size: 24px !important;
        }
    }
    
    /* Tablet Landscape Orientation Optimizations */
    @media (min-width: 768px) and (max-width: 1366px) and (orientation: landscape) {
        /* Slightly reduce padding in landscape for better space utilization */
        .animated-ai-background {
            padding-top: 16px !important;
            padding-bottom: 16px !important;
        }
        
        div[style*="background: #f9fafb"],
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"],
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] {
            padding-top: 16px !important;
            padding-bottom: 16px !important;
            min-height: auto !important;
        }
        
        /* Reduce section spacing in landscape */
        section {
            margin-bottom: 12px !important;
        }
    }
    
    /* Reduce gaps between sections on all tablets */
    @media (min-width: 768px) and (max-width: 1366px) {
        section {
            margin-bottom: 0 !important;
        }
        
        /* Reduce spacing in feature overview header */
        div[style*="text-align: center; margin-bottom: 40px"] {
            margin-bottom: 24px !important;
        }
        
        /* Reduce spacing in how it works header */
        div[style*="text-align: center; margin-bottom: 40px"] {
            margin-bottom: 24px !important;
        }
    }
    
    /* Responsive Design for Smaller Screens (below 768px - phones) */
    @media (max-width: 767px) {
        /* Analysis Platform Section */
        .animated-ai-background {
            padding: 24px 16px !important;
            min-height: auto !important;
        }
        
        #typing-text {
            font-size: 32px !important;
            min-height: 40px !important;
        }
        
        #cursor {
            height: 32px !important;
        }
        
        section > div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 16px !important;
        }
        
        section > div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
        
        section > div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
            gap: 30px !important;
        }
        
        /* Module cards */
        .animate-card-left,
        .animate-card-right {
            padding: 24px !important;
        }
        
        .animate-card-left h2,
        .animate-card-right h2 {
            font-size: 20px !important;
        }
        
        .animate-card-left p,
        .animate-card-right p {
            font-size: 14px !important;
            min-height: auto !important;
        }
        
        /* White boxes in modules */
        .animate-card-left > div[style*="background: white"],
        .animate-card-right > div[style*="background: white"] {
            padding: 16px !important;
            min-height: auto !important;
        }
        
        /* Problem → Solution Section */
        div[style*="background: #f9fafb"] h3 {
            font-size: 24px !important;
        }
        
        div[style*="background: #f9fafb"] h2 {
            font-size: 11px !important;
        }
        
        div[style*="background: #f9fafb"] p,
        div[style*="background: #f9fafb"] li {
            font-size: 14px !important;
        }
    }
    
    @media (max-width: 768px) {
        /* Analysis Platform Section */
        .animated-ai-background {
            padding: 20px 16px !important;
        }
        
        #typing-text {
            font-size: 28px !important;
            min-height: 36px !important;
            margin-bottom: 8px !important;
        }
        
        #cursor {
            height: 28px !important;
        }
        
        section p {
            font-size: 14px !important;
        }
        
        /* Module cards */
        .animate-card-left,
        .animate-card-right {
            padding: 20px !important;
            opacity: 1 !important;
            transform: none !important;
        }
        
        .animate-card-left h2,
        .animate-card-right h2 {
            font-size: 18px !important;
            margin-bottom: 8px !important;
        }
        
        .animate-card-left p,
        .animate-card-right p {
            font-size: 13px !important;
            margin-bottom: 16px !important;
            min-height: auto !important;
        }
        
        .animate-card-left > div[style*="background: white"],
        .animate-card-right > div[style*="background: white"] {
            padding: 12px !important;
            margin-bottom: 16px !important;
            min-height: auto !important;
        }
        
        .animate-card-left > div[style*="background: white"] > div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: 1fr !important;
            gap: 8px !important;
        }
        
        .animate-card-left a,
        .animate-card-right a {
            padding: 10px 20px !important;
            font-size: 14px !important;
        }
        
        /* Problem → Solution Section */
        div[style*="background: #f9fafb"] {
            padding: 24px 16px !important;
            min-height: auto !important;
        }
        
        div[style*="background: #f9fafb"] h3 {
            font-size: 22px !important;
            margin-bottom: 16px !important;
        }
        
        div[style*="background: #f9fafb"] h2 {
            font-size: 10px !important;
            margin-bottom: 10px !important;
        }
        
        div[style*="background: #f9fafb"] p {
            font-size: 13px !important;
            margin-bottom: 16px !important;
        }
        
        div[style*="background: #f9fafb"] li {
            font-size: 13px !important;
            padding: 8px 0 !important;
        }
        
        /* Feature Overview Section */
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"] {
            padding: 24px 16px !important;
            min-height: auto !important;
        }
        
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"] h2 {
            font-size: 28px !important;
            margin-bottom: 24px !important;
        }
        
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"] p {
            font-size: 13px !important;
        }
        
        .animate-fade-up {
            padding: 20px 16px !important;
            opacity: 1 !important;
            transform: none !important;
        }
        
        .animate-fade-up > div[style*="width: 48px"] {
            width: 40px !important;
            height: 40px !important;
            margin-bottom: 16px !important;
        }
        
        .animate-fade-up > div[style*="width: 48px"] svg {
            width: 20px !important;
            height: 20px !important;
        }
        
        .animate-fade-up h3 {
            font-size: 16px !important;
            margin-bottom: 6px !important;
        }
        
        .animate-fade-up p {
            font-size: 12px !important;
        }
        
        /* How It Works Section */
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] {
            padding: 24px 16px !important;
            min-height: auto !important;
        }
        
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] h2 {
            font-size: 28px !important;
            margin-bottom: 24px !important;
        }
        
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] p {
            font-size: 13px !important;
        }
        
        .animate-fade-up > div[style*="width: 64px"] {
            width: 56px !important;
            height: 56px !important;
            margin-bottom: 16px !important;
        }
        
        .animate-fade-up > div[style*="width: 64px"] {
            font-size: 24px !important;
        }
        
        .animate-fade-up h3[style*="font-size: 20px"] {
            font-size: 18px !important;
            margin-bottom: 6px !important;
        }
        
        .animate-fade-up p[style*="font-size: 14px"] {
            font-size: 12px !important;
        }
        
        /* General section adjustments */
        section {
            padding: 20px 16px !important;
        }
        
        section > div[style*="gap: 60px"],
        section > div[style*="gap: 50px"],
        section > div[style*="gap: 24px"] {
            gap: 20px !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Extra small screens */
        .animated-ai-background {
            padding: 16px 12px !important;
        }
        
        #typing-text {
            font-size: 24px !important;
            min-height: 32px !important;
        }
        
        #cursor {
            height: 24px !important;
        }
        
        .animate-card-left,
        .animate-card-right {
            padding: 16px !important;
        }
        
        .animate-card-left h2,
        .animate-card-right h2 {
            font-size: 16px !important;
        }
        
        .animate-card-left p,
        .animate-card-right p {
            font-size: 12px !important;
        }
        
        .animate-card-left > div[style*="background: white"],
        .animate-card-right > div[style*="background: white"] {
            padding: 10px !important;
        }
        
        .animate-card-left > div[style*="background: white"] > div[style*="grid-template-columns: repeat(2, 1fr)"] > div {
            padding: 6px 8px !important;
        }
        
        .animate-card-left > div[style*="background: white"] > div[style*="grid-template-columns: repeat(2, 1fr)"] > div span {
            font-size: 11px !important;
        }
        
        .animate-card-left > div[style*="background: white"] > div[style*="grid-template-columns: repeat(2, 1fr)"] > div svg {
            width: 16px !important;
            height: 16px !important;
        }
        
        .animate-card-left a,
        .animate-card-right a {
            padding: 10px 16px !important;
            font-size: 13px !important;
        }
        
        div[style*="background: #f9fafb"] h3,
        div[style*="background: #ffffff"][style*="min-height: calc(100vh - 72px)"] h2,
        div[style*="background: #f9fafb"][style*="min-height: calc(100vh - 72px)"] h2 {
            font-size: 20px !important;
        }
        
        .animate-fade-up {
            padding: 16px 12px !important;
        }
        
        .animate-fade-up > div[style*="width: 48px"],
        .animate-fade-up > div[style*="width: 64px"] {
            width: 36px !important;
            height: 36px !important;
        }
        
        .animate-fade-up > div[style*="width: 64px"] {
            font-size: 20px !important;
        }
        
        .animate-fade-up h3 {
            font-size: 14px !important;
        }
        
        .animate-fade-up p {
            font-size: 11px !important;
        }
        
        /* Social media badges */
        .animate-card-right > div[style*="background: white"] > div[style*="display: flex"] > div {
            padding: 6px 10px !important;
        }
        
        .animate-card-right > div[style*="background: white"] > div[style*="display: flex"] > div span {
            font-size: 11px !important;
        }
        
        .animate-card-right > div[style*="background: white"] > div[style*="display: flex"] > div svg {
            width: 16px !important;
            height: 16px !important;
        }
    }
    
    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }
    
    /* AI-Themed Animated Background */
    .animated-ai-background {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);
        position: relative;
    }
    
    /* Floating Particles (Neural Network Nodes) */
    .ai-particle {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.4) 0%, rgba(102, 126, 234, 0.2) 50%, rgba(102, 126, 234, 0.05) 100%);
        pointer-events: none;
        filter: blur(2px);
        z-index: 0;
    }
    
    .ai-particle-1 {
        width: 120px;
        height: 120px;
        top: 5%;
        left: 5%;
        animation: float-particle-1 20s ease-in-out infinite;
    }
    
    .ai-particle-2 {
        width: 80px;
        height: 80px;
        top: 15%;
        right: 10%;
        animation: float-particle-2 25s ease-in-out infinite;
    }
    
    .ai-particle-3 {
        width: 100px;
        height: 100px;
        top: 25%;
        left: 15%;
        animation: float-particle-3 18s ease-in-out infinite;
    }
    
    .ai-particle-4 {
        width: 90px;
        height: 90px;
        top: 35%;
        right: 20%;
        animation: float-particle-4 22s ease-in-out infinite;
    }
    
    .ai-particle-5 {
        width: 70px;
        height: 70px;
        top: 45%;
        right: 15%;
        animation: float-particle-5 30s ease-in-out infinite;
    }
    
    .ai-particle-6 {
        width: 60px;
        height: 60px;
        top: 55%;
        left: 50%;
        animation: float-particle-6 16s ease-in-out infinite;
    }
    
    .ai-particle-7 {
        width: 85px;
        height: 85px;
        top: 65%;
        left: 60%;
        animation: float-particle-7 24s ease-in-out infinite;
    }
    
    .ai-particle-8 {
        width: 75px;
        height: 75px;
        top: 75%;
        left: 25%;
        animation: float-particle-8 19s ease-in-out infinite;
    }
    
    .ai-particle-9 {
        width: 95px;
        height: 95px;
        top: 20%;
        right: 25%;
        animation: float-particle-9 21s ease-in-out infinite;
    }
    
    .ai-particle-10 {
        width: 65px;
        height: 65px;
        top: 50%;
        left: 45%;
        animation: float-particle-10 17s ease-in-out infinite;
    }
    
    .ai-particle-11 {
        width: 110px;
        height: 110px;
        top: 80%;
        left: 70%;
        animation: float-particle-11 23s ease-in-out infinite;
    }
    
    .ai-particle-12 {
        width: 85px;
        height: 85px;
        top: 90%;
        right: 40%;
        animation: float-particle-12 26s ease-in-out infinite;
    }
    
    /* Gradient Waves */
    .ai-wave {
        position: absolute;
        width: 200%;
        height: 200px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(102, 126, 234, 0.08) 25%, 
            rgba(139, 92, 246, 0.12) 50%, 
            rgba(102, 126, 234, 0.08) 75%, 
            transparent 100%);
        border-radius: 50%;
        opacity: 0.8;
        pointer-events: none;
        filter: blur(40px);
        z-index: 0;
    }
    
    .ai-wave-1 {
        top: -100px;
        left: -50%;
        animation: wave-move-1 15s ease-in-out infinite;
    }
    
    .ai-wave-2 {
        bottom: -100px;
        right: -50%;
        animation: wave-move-2 20s ease-in-out infinite;
    }
    
    .ai-wave-3 {
        top: 50%;
        left: -50%;
        animation: wave-move-3 18s ease-in-out infinite;
    }
    
    .ai-wave-4 {
        top: 75%;
        right: -50%;
        animation: wave-move-4 22s ease-in-out infinite;
    }
    
    /* Neural Network Connections */
    .ai-connection {
        position: absolute;
        height: 2px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(102, 126, 234, 0.4) 50%, 
            transparent 100%);
        pointer-events: none;
        opacity: 0.5;
        z-index: 0;
    }
    
    .ai-connection-1 {
        width: 300px;
        top: 25%;
        left: 10%;
        transform: rotate(25deg);
        animation: connection-pulse-1 3s ease-in-out infinite;
    }
    
    .ai-connection-2 {
        width: 250px;
        bottom: 30%;
        right: 15%;
        transform: rotate(-35deg);
        animation: connection-pulse-2 4s ease-in-out infinite;
    }
    
    .ai-connection-3 {
        width: 200px;
        top: 55%;
        left: 40%;
        transform: rotate(45deg);
        animation: connection-pulse-3 3.5s ease-in-out infinite;
    }
    
    .ai-connection-4 {
        width: 280px;
        top: 70%;
        right: 20%;
        transform: rotate(-20deg);
        animation: connection-pulse-4 3.8s ease-in-out infinite;
    }
    
    .ai-connection-5 {
        width: 220px;
        top: 35%;
        left: 60%;
        transform: rotate(60deg);
        animation: connection-pulse-5 4.2s ease-in-out infinite;
    }
    
    .ai-connection-6 {
        width: 260px;
        top: 80%;
        left: 30%;
        transform: rotate(-50deg);
        animation: connection-pulse-6 3.3s ease-in-out infinite;
    }
    
    /* Animations - Updated to move across full page height */
    @keyframes float-particle-1 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(80px, 150px) scale(1.2); }
        50% { transform: translate(-40px, 300px) scale(0.9); }
        75% { transform: translate(60px, 450px) scale(1.1); }
    }
    
    @keyframes float-particle-2 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(-100px, -100px) scale(1.15); }
        50% { transform: translate(60px, -200px) scale(0.85); }
        75% { transform: translate(-80px, -300px) scale(1.2); }
    }
    
    @keyframes float-particle-3 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(120px, -150px) scale(1.3); }
        66% { transform: translate(-90px, -300px) scale(0.9); }
    }
    
    @keyframes float-particle-4 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(-80px, 200px) scale(1.1); }
        50% { transform: translate(100px, 400px) scale(0.9); }
        75% { transform: translate(-60px, 600px) scale(1.15); }
    }
    
    @keyframes float-particle-5 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        30% { transform: translate(-120px, -200px) scale(1.4); }
        60% { transform: translate(80px, -400px) scale(0.8); }
        90% { transform: translate(-100px, -600px) scale(1.2); }
    }
    
    @keyframes float-particle-6 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(100px, 250px) scale(1.25); }
        66% { transform: translate(-70px, 500px) scale(0.9); }
    }
    
    @keyframes float-particle-7 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(-90px, 300px) scale(1.2); }
        50% { transform: translate(70px, 600px) scale(0.9); }
        75% { transform: translate(-80px, 900px) scale(1.1); }
    }
    
    @keyframes float-particle-8 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-110px, 70px) scale(1.3); }
    }
    
    @keyframes float-particle-9 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(90px, -80px) scale(1.25); }
        66% { transform: translate(-70px, 90px) scale(0.9); }
    }
    
    @keyframes float-particle-10 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(100px, -100px) scale(1.35); }
    }
    
    @keyframes float-particle-11 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(-90px, 110px) scale(1.2); }
        75% { transform: translate(80px, -90px) scale(0.85); }
    }
    
    @keyframes float-particle-12 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        40% { transform: translate(-120px, -100px) scale(1.4); }
        80% { transform: translate(100px, 120px) scale(0.8); }
    }
    
    @keyframes wave-move-1 {
        0%, 100% { transform: translateX(0) rotate(0deg); }
        50% { transform: translateX(100px) rotate(180deg); }
    }
    
    @keyframes wave-move-2 {
        0%, 100% { transform: translateX(0) rotate(0deg); }
        50% { transform: translateX(-100px) rotate(-180deg); }
    }
    
    @keyframes wave-move-3 {
        0%, 100% { transform: translateX(0) rotate(0deg); }
        50% { transform: translateX(120px) rotate(180deg); }
    }
    
    @keyframes wave-move-4 {
        0%, 100% { transform: translateX(0) rotate(0deg); }
        50% { transform: translateX(-120px) rotate(-180deg); }
    }
    
    @keyframes connection-pulse-1 {
        0%, 100% { opacity: 0.2; transform: rotate(25deg) scaleX(1); }
        50% { opacity: 0.4; transform: rotate(25deg) scaleX(1.2); }
    }
    
    @keyframes connection-pulse-2 {
        0%, 100% { opacity: 0.2; transform: rotate(-35deg) scaleX(1); }
        50% { opacity: 0.4; transform: rotate(-35deg) scaleX(1.15); }
    }
    
    @keyframes connection-pulse-3 {
        0%, 100% { opacity: 0.2; transform: rotate(45deg) scaleX(1); }
        50% { opacity: 0.4; transform: rotate(45deg) scaleX(1.1); }
    }
    
    @keyframes connection-pulse-4 {
        0%, 100% { opacity: 0.2; transform: rotate(-20deg) scaleX(1); }
        50% { opacity: 0.4; transform: rotate(-20deg) scaleX(1.15); }
    }
    
    @keyframes connection-pulse-5 {
        0%, 100% { opacity: 0.2; transform: rotate(60deg) scaleX(1); }
        50% { opacity: 0.4; transform: rotate(60deg) scaleX(1.2); }
    }
    
    @keyframes connection-pulse-6 {
        0%, 100% { opacity: 0.2; transform: rotate(-50deg) scaleX(1); }
        50% { opacity: 0.4; transform: rotate(-50deg) scaleX(1.1); }
    }
    
    /* Ensure content is above background */
    .animated-ai-background > div[style*="z-index"] {
        position: relative;
        z-index: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const text = 'Analysis Platform';
        const typedTextElement = document.getElementById('typed-text');
        const cursorElement = document.getElementById('cursor');
        let index = 0;
        let isDeleting = false;
        
        function typeText() {
            if (!isDeleting && index < text.length) {
                // Typing
                typedTextElement.textContent += text.charAt(index);
                index++;
                setTimeout(typeText, 100); // Typing speed
            } else if (!isDeleting && index === text.length) {
                // Finished typing, wait then start deleting
                setTimeout(function() {
                    isDeleting = true;
                    typeText();
                }, 2000); // Wait 2 seconds before deleting
            } else if (isDeleting && index > 0) {
                // Deleting
                typedTextElement.textContent = text.substring(0, index - 1);
                index--;
                setTimeout(typeText, 50); // Deleting speed (faster than typing)
            } else if (isDeleting && index === 0) {
                // Finished deleting, wait then start typing again
                isDeleting = false;
                setTimeout(typeText, 500); // Wait 0.5 seconds before typing again
            }
        }
        
        // Start typing animation
        typeText();
        
        // Card animations with Intersection Observer
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0) translateX(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        // Animate module cards (left and right)
        const moduleCards = document.querySelectorAll('.animate-card-left, .animate-card-right');
        moduleCards.forEach((card, index) => {
            setTimeout(() => {
                observer.observe(card);
            }, index * 200); // Stagger animation
        });
        
        // Animate feature cards with stagger
        const featureCards = document.querySelectorAll('.animate-fade-up');
        featureCards.forEach((card, index) => {
            setTimeout(() => {
                observer.observe(card);
            }, index * 100); // Stagger animation
        });
        
        // Add hover scale effect to feature cards
        featureCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                this.style.transition = 'all 0.3s ease';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Module cards already have hover effects via inline handlers
        // The animations will work with existing hover effects
    });
</script>
@endsection
