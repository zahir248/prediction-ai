@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    .cursor-layout {
        display: flex;
        height: calc(100vh - 72px);
        background: #ffffff;
        overflow: hidden;
        position: relative;
    }
    .cursor-sidebar {
        width: 400px;
        background: #fafafa;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
        position: relative;
        z-index: 1;
    }
    .cursor-main {
        flex: 1;
        background: #ffffff;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border-left: 1px solid #e5e7eb;
        position: relative;
    }
    .cursor-main.scrollable {
        overflow-y: auto;
        overflow-x: hidden;
    }
    .cursor-main-content {
        flex: 1;
        padding: 24px;
        max-width: 100%;
        width: 100%;
        box-sizing: border-box;
    }
    .animated-ai-background { position: relative; }
    .ai-particle {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(124, 58, 237, 0.28) 0%, rgba(124, 58, 237, 0.12) 50%, rgba(124, 58, 237, 0.04) 100%);
        pointer-events: none;
        filter: blur(2px);
        z-index: 0;
    }
    .ai-particle-1 { width: 80px; height: 80px; top: 10%; left: 10%; animation: sa-float-1 20s ease-in-out infinite; }
    .ai-particle-2 { width: 60px; height: 60px; top: 25%; right: 15%; animation: sa-float-2 25s ease-in-out infinite; }
    .ai-particle-3 { width: 70px; height: 70px; top: 40%; left: 20%; animation: sa-float-3 18s ease-in-out infinite; }
    .ai-particle-4 { width: 65px; height: 65px; top: 55%; right: 25%; animation: sa-float-4 22s ease-in-out infinite; }
    .ai-particle-5 { width: 55px; height: 55px; top: 70%; left: 15%; animation: sa-float-5 30s ease-in-out infinite; }
    .ai-particle-6 { width: 75px; height: 75px; top: 85%; right: 20%; animation: sa-float-6 16s ease-in-out infinite; }
    .ai-wave {
        position: absolute;
        width: 200%;
        height: 150px;
        background: linear-gradient(90deg, transparent 0%, rgba(124, 58, 237, 0.07) 50%, transparent 100%);
        border-radius: 50%;
        opacity: 0.85;
        pointer-events: none;
        filter: blur(30px);
        z-index: 0;
    }
    .ai-wave-1 { top: -75px; left: -50%; animation: sa-wave-1 15s ease-in-out infinite; }
    .ai-wave-2 { bottom: -75px; right: -50%; animation: sa-wave-2 20s ease-in-out infinite; }
    .ai-connection {
        position: absolute;
        height: 2px;
        background: linear-gradient(90deg, transparent 0%, rgba(124, 58, 237, 0.35) 50%, transparent 100%);
        pointer-events: none;
        opacity: 0.4;
        z-index: 0;
    }
    .ai-connection-1 { width: 200px; top: 30%; left: 15%; transform: rotate(25deg); animation: sa-conn-1 3s ease-in-out infinite; }
    .ai-connection-2 { width: 180px; bottom: 35%; right: 20%; transform: rotate(-35deg); animation: sa-conn-2 4s ease-in-out infinite; }
    .ai-connection-3 { width: 160px; top: 60%; left: 45%; transform: rotate(45deg); animation: sa-conn-3 3.5s ease-in-out infinite; }
    @keyframes sa-float-1 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(20px, 60px) scale(1.1); }
    }
    @keyframes sa-float-2 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-30px, -40px) scale(1.12); }
    }
    @keyframes sa-float-3 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(40px, -50px) scale(1.08); }
    }
    @keyframes sa-float-4 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-25px, 80px) scale(1.15); }
    }
    @keyframes sa-float-5 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-45px, -60px) scale(1.2); }
    }
    @keyframes sa-float-6 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(35px, 50px) scale(1.1); }
    }
    @keyframes sa-wave-1 {
        0%, 100% { transform: translateX(0) rotate(0deg); }
        50% { transform: translateX(40px) rotate(180deg); }
    }
    @keyframes sa-wave-2 {
        0%, 100% { transform: translateX(0) rotate(0deg); }
        50% { transform: translateX(-40px) rotate(-180deg); }
    }
    @keyframes sa-conn-1 {
        0%, 100% { opacity: 0.2; }
        50% { opacity: 0.45; }
    }
    @keyframes sa-conn-2 {
        0%, 100% { opacity: 0.15; }
        50% { opacity: 0.4; }
    }
    @keyframes sa-conn-3 {
        0%, 100% { opacity: 0.2; }
        50% { opacity: 0.38; }
    }
    @keyframes sa-blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }
    @keyframes sa-pulse-glow {
        0%, 100% { transform: scale(1); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2); }
        50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4); }
    }
    .cursor-sidebar-header {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
        flex-shrink: 0;
    }
    .cursor-sidebar-content {
        flex: 1;
        min-height: 0;
        padding: 16px;
        overflow-x: visible;
        overflow-y: auto;
    }
    .floating-submit-container {
        position: sticky;
        bottom: 0;
        width: 100%;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        padding: 16px;
        z-index: 100;
        box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.05);
        flex-shrink: 0;
    }
    .floating-submit-btn {
        width: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 14px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .floating-submit-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    }
    .floating-submit-btn:disabled {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        cursor: not-allowed;
        opacity: 0.7;
        box-shadow: none;
    }
    @keyframes shine {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    .progress-bar-fill {
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
    }
    /* Info tooltip — matches Social Media / Create Predictions (dark floating panel + arrow) */
    .info-tooltip {
        position: relative;
        display: inline-block;
        cursor: help;
    }
    .info-tooltip-tooltip {
        position: fixed;
        background: #1f2937;
        color: white;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 400;
        white-space: normal;
        width: 280px;
        z-index: 99999;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        line-height: 1.5;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }
    .info-tooltip-tooltip.show {
        opacity: 1;
        visibility: visible;
    }
    .info-tooltip-arrow {
        position: fixed;
        border: 6px solid transparent;
        z-index: 99999;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }
    .info-tooltip-arrow.show {
        opacity: 1;
        visibility: visible;
    }
    .cursor-sidebar-content .info-tooltip {
        font-size: 12px;
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
            max-height: none;
        }
        .cursor-main {
            border-left: none;
            min-height: 360px;
        }
    }
</style>

<div class="cursor-layout">
    <!-- Left: First / Second profile + Generate -->
    <div class="cursor-sidebar">
        <div class="cursor-sidebar-header" id="sentimentSidebarHeader">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Compare Profiles</h2>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Fill in the form to generate analysis</p>
        </div>
        <div class="cursor-sidebar-header" id="sentimentPromptDetailsHeader" style="display: none;">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Analysis Input Details</h2>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Review the details being analyzed</p>
        </div>

        <div class="cursor-sidebar-content">
            @if (session('status'))
                <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 12px;">{{ session('status') }}</div>
            @endif

            @auth
                @if ($completedAnalyses->count() < 2)
                    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
                        <h3 style="font-size: 13px; font-weight: 600; color: #374151; margin: 0 0 8px;">Need more profiles</h3>
                        <p style="color: #64748b; font-size: 12px; line-height: 1.6; margin: 0 0 12px;">Complete at least two social media analyses with AI results first.</p>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <a href="{{ route('social-media.index') }}" style="text-align: center; padding: 10px 14px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 12px;">Run Social Media Analysis</a>
                            <a href="{{ route('social-media.history') }}" style="text-align: center; padding: 10px 14px; border: 1px solid #e5e7eb; color: #64748b; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 12px;">Open profile history</a>
                        </div>
                    </div>
                @endif
            @endauth

            @if (Auth::guest() || $completedAnalyses->count() >= 2)
                @if (Auth::check() && $errors->any())
                    <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 12px;">
                        <ul style="margin: 0; padding-left: 16px;">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="sentimentFormSection">
                    <form id="sentimentCompareForm" method="POST" action="{{ route('sentiment-analysis.compare') }}">
                        @csrf
                        <h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Profile selection</h2>

                        @guest
                            <div style="margin-bottom: 18px;">
                                <label for="analysis_a_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">First profile <span style="color: #dc2626;">*</span></label>
                                <select id="analysis_a_id" disabled aria-disabled="true" style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: #f9fafb; color: #6b7280; cursor: not-allowed;">
                                    <option value="">Log in to load your profiles…</option>
                                </select>
                            </div>
                            <div style="margin-bottom: 18px;">
                                <label for="analysis_b_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">Second profile <span style="color: #dc2626;">*</span></label>
                                <select id="analysis_b_id" disabled aria-disabled="true" style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: #f9fafb; color: #6b7280; cursor: not-allowed;">
                                    <option value="">Log in to load your profiles…</option>
                                </select>
                            </div>
                        @else
                            @php
                                $sentimentAnalysisOptionLabels = $completedAnalyses->mapWithKeys(function ($row) {
                                    $pf = $row->found_platforms ?? [];
                                    $platformLabel = count($pf) > 0
                                        ? collect($pf)->map(fn ($p) => ucfirst((string) $p))->implode(', ')
                                        : '—';

                                    return [$row->id => $row->username.' ('.$platformLabel.')'];
                                });
                            @endphp
                            <div style="margin-bottom: 18px;">
                                <label for="analysis_a_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">First profile <span style="color: #dc2626;">*</span></label>
                                <select name="analysis_a_id" id="analysis_a_id" required style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: #ffffff;">
                                    <option value="" @selected(blank(old('analysis_a_id')))>Select first profile…</option>
                                    @foreach ($completedAnalyses as $a)
                                        <option value="{{ $a->id }}" @selected((string) old('analysis_a_id') === (string) $a->id)>
                                            {{ $sentimentAnalysisOptionLabels[$a->id] ?? $a->username }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="margin-bottom: 18px;">
                                <label for="analysis_b_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">Second profile <span style="color: #dc2626;">*</span></label>
                                <select name="analysis_b_id" id="analysis_b_id" required style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: #ffffff;">
                                    <option value="" @selected(blank(old('analysis_b_id')))>Select second profile…</option>
                                    @foreach ($completedAnalyses as $a)
                                        <option value="{{ $a->id }}" @selected((string) old('analysis_b_id') === (string) $a->id)>
                                            {{ $sentimentAnalysisOptionLabels[$a->id] ?? $a->username }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endguest

                        <div id="saReportLanguageSection" style="margin-bottom: 24px;">
                            <h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Report Language</h2>
                            <div style="position: relative; margin-bottom: 8px;">
                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 10px; font-weight: 600; color: #374151; font-size: 12px;">
                                    Language <span style="color: #dc2626;">*</span>
                                    <span class="info-tooltip" data-tooltip="Choose the language for the generated report text. The analysis structure stays the same; headings and narrative will match your selection.">
                                        <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
                                    </span>
                                </div>
                                <div role="radiogroup" aria-label="Report language" style="display: flex; flex-direction: row; flex-wrap: nowrap; align-items: stretch; gap: 8px; width: 100%;">
                                    <label style="display: flex; align-items: center; justify-content: center; gap: 8px; flex: 1 1 0; min-width: 0; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; background: #ffffff; cursor: pointer; font-size: 12px; color: #374151; font-weight: 500;">
                                        <input type="radio" name="report_language" value="en" @checked(old('report_language') === 'en') style="width: 16px; height: 16px; accent-color: #2563eb; cursor: pointer; flex-shrink: 0;">
                                        <span style="white-space: nowrap;">English</span>
                                    </label>
                                    <label style="display: flex; align-items: center; justify-content: center; gap: 8px; flex: 1 1 0; min-width: 0; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; background: #ffffff; cursor: pointer; font-size: 12px; color: #374151; font-weight: 500;">
                                        <input type="radio" name="report_language" value="ms" @checked(old('report_language') === 'ms') style="width: 16px; height: 16px; accent-color: #2563eb; cursor: pointer; flex-shrink: 0;">
                                        <span style="white-space: nowrap;">Bahasa Melayu</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="sentimentPromptDetailsCard" style="display: none;">
                    <div id="sentimentPromptDetailsContent"></div>
                </div>
            @endif
        </div>

        <div class="floating-submit-container" id="sentimentFloatingSubmit">
            @auth
                @if ($completedAnalyses->count() >= 2)
                    <button type="submit" form="sentimentCompareForm" class="floating-submit-btn" id="sentimentGenerateBtn">Generate</button>
                @else
                    <button type="button" class="floating-submit-btn" disabled>Generate</button>
                @endif
            @else
                <button type="submit" form="sentimentCompareForm" class="floating-submit-btn" id="sentimentGenerateBtn" disabled>Login to Generate</button>
            @endauth
        </div>
        <div class="floating-submit-container" id="sentimentPostResultActions" style="display: none;">
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <a href="#" id="sentimentExportPdfBtn" onclick="event.preventDefault(); confirmSentimentExportFromComparison(); return false;" class="floating-submit-btn" style="flex: 1 1 120px; min-width: 0; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">Export</a>
                <button type="button" class="floating-submit-btn" id="sentimentNewComparisonBtn" style="flex: 1 1 120px; min-width: 0; background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">New comparison</button>
            </div>
        </div>
    </div>

    <!-- Right: Did you know / progress (matches Data & Social Media modules) -->
    <div class="cursor-main animated-ai-background" id="sentimentMainPanel">
        <div id="saAnimatedBg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1;">
            <div class="ai-particle ai-particle-1"></div>
            <div class="ai-particle ai-particle-2"></div>
            <div class="ai-particle ai-particle-3"></div>
            <div class="ai-particle ai-particle-4"></div>
            <div class="ai-particle ai-particle-5"></div>
            <div class="ai-particle ai-particle-6"></div>
            <div class="ai-wave ai-wave-1"></div>
            <div class="ai-wave ai-wave-2"></div>
            <div class="ai-connection ai-connection-1"></div>
            <div class="ai-connection ai-connection-2"></div>
            <div class="ai-connection ai-connection-3"></div>
        </div>

        <div class="cursor-main-content" id="sentimentMainContent" style="position: relative; z-index: 10; padding: 24px; display: flex; align-items: center; justify-content: center; min-height: 100%; box-sizing: border-box;">
            <div id="sentimentProgressCard" style="display: none; background: transparent; padding: 24px; max-width: 500px; width: 100%; margin: 0 auto;">
                <h2 style="color: #1e293b; margin-bottom: 8px; font-size: 18px; font-weight: 700; text-align: center; border: none; border-bottom: none; text-decoration: none;">NUJUM Analysis in Progress</h2>
                <p style="color: #64748b; margin-bottom: 20px; font-size: 13px; line-height: 1.5; text-align: center; border: none; border-top: none;">
                    Comparing sentiment between your two profiles. This may take 2–5 minutes.
                </p>
                <div style="margin-bottom: 16px;">
                    <div style="background: #e2e8f0; border-radius: 8px; height: 10px; overflow: hidden; position: relative;">
                        <div id="sentimentProgressBar" class="progress-bar-fill" style="background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%); height: 100%; border-radius: 8px; width: 0%; transition: width 0.3s ease; position: relative; overflow: hidden;">
                            <div class="progress-bar-shine" style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent); animation: shine 2s infinite;"></div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                        <span id="sentimentProgressText" style="color: #64748b; font-size: 12px; font-weight: 500;">0%</span>
                        <span id="sentimentProgressStatus" style="color: #0ea5e9; font-size: 12px; font-weight: 600;">Initializing...</span>
                    </div>
                </div>
                <p style="color: #94a3b8; margin-top: 16px; font-size: 11px; line-height: 1.4; text-align: center;">
                    Please do not close this window. Results will open when complete.
                </p>
            </div>
            <div id="sentimentDidYouKnow" style="max-width: 600px; width: 100%; text-align: center;">
                <div style="margin-bottom: 28px;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 50%; margin-bottom: 18px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2); animation: sa-pulse-glow 2s ease-in-out infinite;">
                        <i class="bi bi-lightbulb-fill" style="color: #f59e0b; font-size: 32px;"></i>
                    </div>
                    <h2 style="font-size: 24px; font-weight: 700; color: #111827; margin: 0; letter-spacing: -0.5px;">Did you know?</h2>
                </div>
                <div id="saTypingFacts" style="min-height: 200px; color: #374151; font-size: 16px; line-height: 2; font-family: Georgia, 'Times New Roman', serif; padding: 16px 0;">
                    <span id="saTypingText" style="white-space: pre-wrap; word-wrap: break-word; display: inline;"></span><span id="saTypingCursor" style="display: inline-block; width: 3px; height: 22px; background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); margin-left: 4px; animation: sa-blink 1s infinite; vertical-align: middle; border-radius: 2px;"></span>
                </div>
            </div>
            <div id="sentimentResultCard" style="display: none; width: 100%; max-width: 100%; padding: 0;">
                <div id="sentimentResultContent" style="width: 100%; max-width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    var SENTIMENT_COMPARE_URL = @json(route('sentiment-analysis.compare'));
    var SENTIMENT_CONTENT_HTML_BASE = @json(url('/sentiment-analysis'));
function syncSentimentProfileSelects() {
    var a = document.getElementById('analysis_a_id');
    var b = document.getElementById('analysis_b_id');
    if (!a || !b) return;

    if (a.value && b.value && a.value === b.value) {
        b.selectedIndex = 0;
    }

    var aVal = a.value;
    var bVal = b.value;

    for (var i = 1; i < b.options.length; i++) {
        var optB = b.options[i];
        var hideB = !!aVal && optB.value === aVal;
        optB.disabled = hideB;
        optB.hidden = hideB;
    }
    for (var j = 1; j < a.options.length; j++) {
        var optA = a.options[j];
        var hideA = !!bVal && optA.value === bVal;
        optA.disabled = hideA;
        optA.hidden = hideA;
    }
}

function validateSentimentProfiles() {
    var a = document.getElementById('analysis_a_id');
    var b = document.getElementById('analysis_b_id');
    if (!a || !b) return true;
    if (!a.value || !b.value) {
        alert('Please select both First profile and Second profile.');
        return false;
    }
    if (a.value === b.value) {
        alert('Please choose two different profiles for First profile and Second profile.');
        return false;
    }
    if (!document.querySelector('#sentimentCompareForm input[name="report_language"]:checked')) {
        alert('Please select a report language.');
        return false;
    }
    return true;
}

function fillSentimentPromptDetails() {
    var a = document.getElementById('analysis_a_id');
    var b = document.getElementById('analysis_b_id');
    var langEl = document.querySelector('#sentimentCompareForm input[name="report_language"]:checked');
    var content = document.getElementById('sentimentPromptDetailsContent');
    if (!content || !a || !b) return;
    var aLabel = (a.options[a.selectedIndex] && a.options[a.selectedIndex].value)
        ? a.options[a.selectedIndex].text.trim()
        : '';
    var bLabel = (b.options[b.selectedIndex] && b.options[b.selectedIndex].value)
        ? b.options[b.selectedIndex].text.trim()
        : '';
    var langLabel = (langEl && langEl.value === 'ms') ? 'Bahasa Melayu' : 'English';
    content.innerHTML =
        '<div style="margin-bottom: 24px;">' +
        '<h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Profiles</h2>' +
        '<div style="font-size: 13px; color: #111827; line-height: 1.6;">' +
        '<div style="margin-bottom: 10px;"><strong style="color: #374151;">First profile</strong><div style="color: #64748b; margin-top: 4px;">' + escapeHtml(aLabel) + '</div></div>' +
        '<div><strong style="color: #374151;">Second profile</strong><div style="color: #64748b; margin-top: 4px;">' + escapeHtml(bLabel) + '</div></div>' +
        '</div></div>' +
        '<div style="margin-bottom: 24px;">' +
        '<h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Report language</h2>' +
        '<div style="font-size: 13px; color: #64748b;">' + escapeHtml(langLabel) + '</div></div>';
}

function escapeHtml(text) {
    if (!text) return '';
    var d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}

function startSentimentProgressAnimation() {
    var statusSequence = [
        { text: 'Processing your request...', progress: 10 },
        { text: 'Loading profile analyses...', progress: 25 },
        { text: 'Preparing comparison context...', progress: 40 },
        { text: 'Analyzing with NUJUM AI...', progress: 60 },
        { text: 'Comparing sentiment and tone...', progress: 75 },
        { text: 'Generating comparison report...', progress: 85 },
        { text: 'Finalizing results...', progress: 95 }
    ];
    var progressBar = document.getElementById('sentimentProgressBar');
    var progressText = document.getElementById('sentimentProgressText');
    var progressStatus = document.getElementById('sentimentProgressStatus');
    var progressCard = document.getElementById('sentimentProgressCard');
    if (!progressBar || !progressText || !progressStatus) return;

    if (progressCard && progressCard.dataset.progressInterval) {
        clearInterval(parseInt(progressCard.dataset.progressInterval, 10));
    }
    if (progressCard && progressCard.dataset.statusInterval) {
        clearInterval(parseInt(progressCard.dataset.statusInterval, 10));
    }

    var currentStatusIndex = 0;
    var currentProgress = 0;
    progressBar.style.width = '0%';
    progressText.textContent = '0%';
    progressStatus.textContent = 'Initializing...';

    var progressUpdateInterval = setInterval(function () {
        if (currentProgress < 95) {
            currentProgress += 0.3;
            progressBar.style.width = currentProgress + '%';
            progressText.textContent = Math.round(currentProgress) + '%';
        }
    }, 500);

    var statusInterval = setInterval(function () {
        if (currentStatusIndex < statusSequence.length) {
            var st = statusSequence[currentStatusIndex];
            progressStatus.textContent = st.text;
            currentStatusIndex++;
        }
    }, 3000);

    if (progressCard) {
        progressCard.dataset.progressInterval = String(progressUpdateInterval);
        progressCard.dataset.statusInterval = String(statusInterval);
    }
}

function stopSentimentProgressAnimation() {
    var progressCard = document.getElementById('sentimentProgressCard');
    if (!progressCard) return;
    if (progressCard.dataset.progressInterval) {
        clearInterval(parseInt(progressCard.dataset.progressInterval, 10));
        delete progressCard.dataset.progressInterval;
    }
    if (progressCard.dataset.statusInterval) {
        clearInterval(parseInt(progressCard.dataset.statusInterval, 10));
        delete progressCard.dataset.statusInterval;
    }
}

function updateSentimentProgress(percent, status) {
    var progressBar = document.getElementById('sentimentProgressBar');
    var progressText = document.getElementById('sentimentProgressText');
    var progressStatus = document.getElementById('sentimentProgressStatus');
    stopSentimentProgressAnimation();
    if (progressBar) progressBar.style.width = percent + '%';
    if (progressText) progressText.textContent = Math.round(percent) + '%';
    if (progressStatus) progressStatus.textContent = status || 'Processing...';
}

function resetSentimentCompareFormUI() {
    stopSentimentProgressAnimation();

    var sidebarHeader = document.getElementById('sentimentSidebarHeader');
    var promptHeader = document.getElementById('sentimentPromptDetailsHeader');
    var formSection = document.getElementById('sentimentFormSection');
    var promptCard = document.getElementById('sentimentPromptDetailsCard');
    var floating = document.getElementById('sentimentFloatingSubmit');
    var postActions = document.getElementById('sentimentPostResultActions');
    var animatedBg = document.getElementById('saAnimatedBg');
    var mainPanel = document.getElementById('sentimentMainPanel');
    var mainContent = document.getElementById('sentimentMainContent');
    var didYouKnow = document.getElementById('sentimentDidYouKnow');
    var progressCard = document.getElementById('sentimentProgressCard');
    var resultCard = document.getElementById('sentimentResultCard');
    var resultContent = document.getElementById('sentimentResultContent');

    if (sidebarHeader) sidebarHeader.style.display = '';
    if (promptHeader) promptHeader.style.display = 'none';
    if (formSection) formSection.style.display = '';
    if (promptCard) promptCard.style.display = 'none';
    if (floating) floating.style.display = '';
    if (postActions) postActions.style.display = 'none';
    if (animatedBg) animatedBg.style.display = '';
    if (mainPanel) {
        mainPanel.style.background = '';
        mainPanel.classList.add('animated-ai-background');
        mainPanel.classList.remove('scrollable');
    }
    if (mainContent) {
        mainContent.style.display = 'flex';
        mainContent.style.alignItems = 'center';
        mainContent.style.justifyContent = 'center';
        mainContent.style.padding = '24px';
        mainContent.style.minHeight = '100%';
        mainContent.style.background = '';
    }
    if (didYouKnow) didYouKnow.style.display = '';
    if (progressCard) progressCard.style.display = 'none';
    if (resultCard) resultCard.style.display = 'none';
    if (resultContent) resultContent.innerHTML = '';

    var promptDetailsContent = document.getElementById('sentimentPromptDetailsContent');
    if (promptDetailsContent) promptDetailsContent.innerHTML = '';

    var compareForm = document.getElementById('sentimentCompareForm');
    if (compareForm) {
        compareForm.reset();
    }
    var selA = document.getElementById('analysis_a_id');
    var selB = document.getElementById('analysis_b_id');
    if (selA) selA.selectedIndex = 0;
    if (selB) selB.selectedIndex = 0;
    if (compareForm) {
        compareForm.querySelectorAll('input[name="report_language"]').forEach(function (r) {
            r.checked = false;
        });
    }
    syncSentimentProfileSelects();

    var progressBar = document.getElementById('sentimentProgressBar');
    if (progressBar) progressBar.style.width = '0%';

    try {
        delete window.lastSentimentComparisonId;
    } catch (e) {}
}

function showToast(message, type) {
    document.querySelectorAll('.toast-notification').forEach(function (toast) {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
    });
    var toast = document.createElement('div');
    toast.className = 'toast-notification';
    var bg = type === 'success' ? '#10b981' : '#ef4444';
    toast.style.cssText = 'position:fixed;top:20px;right:20px;background:' + bg + ';color:white;padding:16px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:10000;font-size:14px;font-weight:500;';
    var icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    toast.innerHTML = '<i class="bi ' + icon + '" style="margin-right:8px;"></i>' + message;
    document.body.appendChild(toast);
    setTimeout(function () {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 4000);
}

function displaySentimentComparisonResult(html, meta) {
    var progressCard = document.getElementById('sentimentProgressCard');
    var didYouKnow = document.getElementById('sentimentDidYouKnow');
    var resultCard = document.getElementById('sentimentResultCard');
    var resultContent = document.getElementById('sentimentResultContent');
    var mainContent = document.getElementById('sentimentMainContent');
    var mainPanel = document.getElementById('sentimentMainPanel');
    var postActions = document.getElementById('sentimentPostResultActions');
    var animatedBg = document.getElementById('saAnimatedBg');

    var banner = '';
    if (meta && meta.incomplete) {
        banner = '<div style="background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">'
            + escapeHtml(meta.message || 'Comparison was saved but the AI returned an incomplete result. You may retry.')
            + '</div>';
    }

    if (progressCard) progressCard.style.display = 'none';
    if (didYouKnow) didYouKnow.style.display = 'none';
    if (resultCard) resultCard.style.display = 'block';
    if (resultContent) resultContent.innerHTML = banner + html;

    if (mainContent) {
        mainContent.style.display = 'block';
        mainContent.style.alignItems = 'flex-start';
        mainContent.style.justifyContent = 'flex-start';
        mainContent.style.padding = '24px';
        mainContent.style.minHeight = '100%';
        mainContent.style.background = '#ffffff';
    }
    if (mainPanel) {
        mainPanel.classList.add('scrollable');
        mainPanel.style.background = '#ffffff';
        mainPanel.classList.remove('animated-ai-background');
    }
    if (animatedBg) animatedBg.style.display = 'none';

    if (postActions) postActions.style.display = 'block';

    if (meta && meta.comparison_id) {
        window.lastSentimentComparisonId = meta.comparison_id;
    }

    var innerReport = resultContent ? resultContent.querySelector('.sentiment-comparison-inner') : null;
    if (innerReport && innerReport.getAttribute('data-export-profile-line')) {
        window.lastSentimentExportProfileLine = innerReport.getAttribute('data-export-profile-line');
    }

    if (resultContent && typeof window.initSentimentReportChartsFromDom === 'function') {
        setTimeout(function () {
            window.initSentimentReportChartsFromDom(resultContent);
        }, 100);
    }

    if (meta && !meta.incomplete) {
        showToast('Analysis completed successfully!', 'success');
    }
}

function showSentimentCompareProgress() {
    var sidebarHeader = document.getElementById('sentimentSidebarHeader');
    var promptHeader = document.getElementById('sentimentPromptDetailsHeader');
    var formSection = document.getElementById('sentimentFormSection');
    var promptCard = document.getElementById('sentimentPromptDetailsCard');
    var floating = document.getElementById('sentimentFloatingSubmit');
    var animatedBg = document.getElementById('saAnimatedBg');
    var mainPanel = document.getElementById('sentimentMainPanel');
    var mainContent = document.getElementById('sentimentMainContent');
    var didYouKnow = document.getElementById('sentimentDidYouKnow');
    var progressCard = document.getElementById('sentimentProgressCard');

    fillSentimentPromptDetails();

    if (sidebarHeader) sidebarHeader.style.display = 'none';
    if (promptHeader) promptHeader.style.display = 'block';
    if (formSection) formSection.style.display = 'none';
    if (promptCard) promptCard.style.display = 'block';

    if (floating) floating.style.display = 'none';

    if (animatedBg) animatedBg.style.display = 'none';
    if (mainPanel) {
        mainPanel.style.background = '#ffffff';
        mainPanel.classList.remove('animated-ai-background');
    }
    if (mainContent) {
        mainContent.style.display = 'flex';
        mainContent.style.alignItems = 'center';
        mainContent.style.justifyContent = 'center';
        mainContent.style.padding = '24px';
        mainContent.style.minHeight = '100%';
        mainContent.style.background = '#ffffff';
    }
    if (didYouKnow) didYouKnow.style.display = 'none';
    if (progressCard) progressCard.style.display = 'block';

    startSentimentProgressAnimation();
}

document.addEventListener('DOMContentLoaded', function() {
    var compareForm = document.getElementById('sentimentCompareForm');
    if (compareForm) {
        compareForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!validateSentimentProfiles()) return;

            showSentimentCompareProgress();

            var token = document.querySelector('meta[name="csrf-token"]');
            token = token ? token.getAttribute('content') : (document.querySelector('input[name="_token"]') || {}).value;

            var fd = new FormData(compareForm);

            fetch(SENTIMENT_COMPARE_URL, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token || ''
                },
                body: fd
            })
                .then(function (r) {
                    return r.json().then(function (data) {
                        return { ok: r.ok, status: r.status, data: data };
                    }).catch(function () {
                        return { ok: r.ok, status: r.status, data: null };
                    });
                })
                .then(function (res) {
                    if (res.status === 422) {
                        stopSentimentProgressAnimation();
                        resetSentimentCompareFormUI();
                        var msg = (res.data && res.data.message) ? res.data.message : 'Validation failed.';
                        if (res.data && res.data.errors) {
                            var errs = res.data.errors;
                            var parts = [];
                            Object.keys(errs).forEach(function (k) {
                                if (Array.isArray(errs[k])) parts.push(errs[k].join(' '));
                            });
                            if (parts.length) msg = parts.join('\n');
                        }
                        alert(msg);
                        return null;
                    }
                    if (res.status === 403 || !res.ok) {
                        stopSentimentProgressAnimation();
                        resetSentimentCompareFormUI();
                        alert((res.data && res.data.message) ? res.data.message : 'Request failed. Please try again.');
                        return null;
                    }
                    if (!res.data || !res.data.comparison_id) {
                        stopSentimentProgressAnimation();
                        resetSentimentCompareFormUI();
                        alert('Unexpected response from server.');
                        return null;
                    }
                    return res.data;
                })
                .then(function (data) {
                    if (!data) return;
                    updateSentimentProgress(100, 'Loading report…');
                    var url = SENTIMENT_CONTENT_HTML_BASE + '/' + data.comparison_id + '/content-html';
                    return fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    }).then(function (r2) {
                        if (!r2.ok) throw new Error('Failed to load report HTML');
                        return r2.text().then(function (html) {
                            displaySentimentComparisonResult(html, data);
                        });
                    });
                })
                .catch(function () {
                    stopSentimentProgressAnimation();
                    resetSentimentCompareFormUI();
                    alert('Something went wrong. Please try again.');
                });
        });
    }

    var newCompareBtn = document.getElementById('sentimentNewComparisonBtn');
    if (newCompareBtn) {
        newCompareBtn.addEventListener('click', function () {
            resetSentimentCompareFormUI();
        });
    }

    var selA = document.getElementById('analysis_a_id');
    var selB = document.getElementById('analysis_b_id');
    if (selA && selB) {
        selA.addEventListener('change', syncSentimentProfileSelects);
        selB.addEventListener('change', syncSentimentProfileSelects);
        syncSentimentProfileSelects();
    }

    (function initSentimentAnalysisTooltips() {
        var tooltipElement = null;
        var tooltipArrow = null;

        function createTooltipElements() {
            if (!tooltipElement) {
                tooltipElement = document.createElement('div');
                tooltipElement.className = 'info-tooltip-tooltip';
                document.body.appendChild(tooltipElement);
            }
            if (!tooltipArrow) {
                tooltipArrow = document.createElement('div');
                tooltipArrow.className = 'info-tooltip-arrow';
                document.body.appendChild(tooltipArrow);
            }
        }

        function showTooltip(el, text) {
            createTooltipElements();
            var rect = el.getBoundingClientRect();
            var isInSidebar = el.closest('.cursor-sidebar');
            var tooltipWidth = 280;
            var spacing = 12;

            tooltipElement.textContent = text;
            tooltipElement.style.width = tooltipWidth + 'px';
            tooltipElement.style.visibility = 'hidden';
            tooltipElement.style.display = 'block';
            var tooltipHeight = tooltipElement.offsetHeight;
            tooltipElement.style.display = '';
            tooltipElement.style.visibility = '';

            var tooltipLeft, tooltipTop, arrowLeft, arrowTop, arrowDirection = 'top';

            if (isInSidebar) {
                tooltipLeft = rect.left - tooltipWidth - spacing;
                tooltipTop = rect.top + (rect.height / 2) - (tooltipHeight / 2);
                arrowLeft = rect.left - spacing;
                arrowTop = rect.top + (rect.height / 2);
                arrowDirection = 'right';
                if (tooltipLeft < 10) {
                    tooltipLeft = rect.right + spacing;
                    arrowLeft = rect.right;
                    arrowDirection = 'left';
                }
            } else {
                tooltipLeft = rect.left + (rect.width / 2) - (tooltipWidth / 2);
                tooltipTop = rect.top - tooltipHeight - spacing;
                arrowLeft = rect.left + (rect.width / 2);
                arrowTop = rect.top - spacing;
                arrowDirection = 'top';
                if (tooltipTop < 10) {
                    tooltipTop = rect.bottom + spacing;
                    arrowTop = rect.bottom;
                    arrowDirection = 'bottom';
                }
                if (tooltipLeft < 10) tooltipLeft = 10;
                if (tooltipLeft + tooltipWidth > window.innerWidth - 10) {
                    tooltipLeft = window.innerWidth - tooltipWidth - 10;
                }
            }

            tooltipElement.style.left = tooltipLeft + 'px';
            tooltipElement.style.top = tooltipTop + 'px';
            tooltipElement.classList.add('show');

            tooltipArrow.style.left = arrowLeft + 'px';
            tooltipArrow.style.top = arrowTop + 'px';
            tooltipArrow.style.transform = 'translate(-50%, -50%)';
            tooltipArrow.style.borderTopColor = arrowDirection === 'top' ? '#1f2937' : 'transparent';
            tooltipArrow.style.borderBottomColor = arrowDirection === 'bottom' ? '#1f2937' : 'transparent';
            tooltipArrow.style.borderLeftColor = arrowDirection === 'left' ? '#1f2937' : 'transparent';
            tooltipArrow.style.borderRightColor = arrowDirection === 'right' ? '#1f2937' : 'transparent';
            tooltipArrow.classList.add('show');
        }

        function hideTooltip() {
            if (tooltipElement) tooltipElement.classList.remove('show');
            if (tooltipArrow) tooltipArrow.classList.remove('show');
        }

        function attachListeners() {
            var roots = document.querySelectorAll('.cursor-sidebar-content .info-tooltip:not([data-sa-tooltip-bound])');
            roots.forEach(function (tooltip) {
                var text = tooltip.getAttribute('data-tooltip');
                if (!text) return;
                tooltip.setAttribute('data-sa-tooltip-bound', '1');
                tooltip.addEventListener('mouseenter', function () {
                    showTooltip(this, text);
                });
                tooltip.addEventListener('mouseleave', hideTooltip);
                tooltip.addEventListener('click', function (e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        e.stopPropagation();
                        var active = tooltip.classList.contains('active');
                        document.querySelectorAll('.cursor-sidebar-content .info-tooltip').forEach(function (t) {
                            t.classList.remove('active');
                        });
                        if (!active) {
                            tooltip.classList.add('active');
                            showTooltip(tooltip, text);
                        } else {
                            hideTooltip();
                        }
                    }
                });
            });
        }

        attachListeners();
        setTimeout(attachListeners, 200);

        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 768 && !e.target.closest('.info-tooltip')) {
                document.querySelectorAll('.cursor-sidebar-content .info-tooltip').forEach(function (t) {
                    t.classList.remove('active');
                });
                hideTooltip();
            }
        });

        window.addEventListener('scroll', hideTooltip, true);
    })();

    var typingText = document.getElementById('saTypingText');
    if (!typingText) return;

    var sentimentFacts = [
        "Sentiment analysis compares how two profiles feel to readers—tone, warmth, and emotional cues—not just what they post about.",
        "NUJUM uses your existing Social Media Analysis reports, so run those first for the most accurate comparison.",
        "Comparing sentiment can help HR and communications teams spot differences in public-facing style before campaigns or partnerships.",
        "Mixed sentiment in text often reads as neutral-professional; side-by-side comparison makes subtle differences easier to see.",
        "Report language applies to the whole comparison output—switch to Bahasa Melayu for Malaysian audiences when you need it.",
        "You can save every comparison in History and reopen it anytime without re-running the AI."
    ];

    var currentFactIndex = 0;
    var currentCharIndex = 0;
    var isDeleting = false;
    var typingSpeed = 50;

    function typeFact() {
        var fact = sentimentFacts[currentFactIndex];
        if (!isDeleting && currentCharIndex < fact.length) {
            typingText.textContent = fact.substring(0, currentCharIndex + 1);
            currentCharIndex++;
            typingSpeed = 45;
        } else if (isDeleting && currentCharIndex > 0) {
            typingText.textContent = fact.substring(0, currentCharIndex - 1);
            currentCharIndex--;
            typingSpeed = 28;
        } else if (!isDeleting && currentCharIndex === fact.length) {
            typingSpeed = 2200;
            isDeleting = true;
        } else if (isDeleting && currentCharIndex === 0) {
            isDeleting = false;
            currentFactIndex = (currentFactIndex + 1) % sentimentFacts.length;
            typingSpeed = 450;
        }
        setTimeout(typeFact, typingSpeed);
    }
    typeFact();
});
</script>

<div id="sentimentExportModal" class="export-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div class="export-modal-content" style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 48px; color: #10b981;">📄</span>
        </div>
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Export PDF Report</h3>
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you ready to export this sentiment comparison as a PDF report?</p>
        <p id="sentimentExportTopic" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
        <p style="color: #10b981; margin-bottom: 24px; font-size: 14px; font-weight: 500;">The report will include all comparison details and NUJUM insights.</p>
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button type="button" onclick="closeSentimentExportModal()" style="padding: 12px 24px; background: transparent; color: #64748b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer;">Cancel</button>
            <button type="button" id="confirmSentimentExportBtn" style="padding: 12px 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">Export PDF</button>
        </div>
    </div>
</div>

<script>
var currentSentimentExportId = null;
window.confirmSentimentExportFromComparison = function () {
    var id = window.lastSentimentComparisonId;
    if (!id) {
        if (typeof showToast === 'function') {
            showToast('Comparison ID not available. Please wait for the analysis to complete.', 'error');
        } else {
            alert('Comparison ID not available.');
        }
        return;
    }
    currentSentimentExportId = id;
    var el = document.getElementById('sentimentExportTopic');
    if (el) {
        el.textContent = window.lastSentimentExportProfileLine || ('Sentiment comparison #' + id);
    }
    var modal = document.getElementById('sentimentExportModal');
    if (modal) {
        modal.style.display = 'flex';
    }
};
window.closeSentimentExportModal = function () {
    var modal = document.getElementById('sentimentExportModal');
    if (modal) {
        modal.style.display = 'none';
    }
    currentSentimentExportId = null;
};
window.exportSentimentComparisonPdf = function () {
    var id = currentSentimentExportId || window.lastSentimentComparisonId;
    if (!id) {
        if (typeof showToast === 'function') {
            showToast('Error: Comparison ID not found', 'error');
        }
        closeSentimentExportModal();
        return;
    }
    var n = parseInt(id, 10);
    if (isNaN(n) || n <= 0) {
        if (typeof showToast === 'function') {
            showToast('Error: Invalid comparison ID', 'error');
        }
        closeSentimentExportModal();
        return;
    }
    closeSentimentExportModal();
    if (typeof showToast === 'function') {
        showToast('Exporting PDF...', 'success');
    }
    setTimeout(function () {
        if (typeof showToast === 'function') {
            showToast('PDF exported successfully!', 'success');
        }
    }, 1000);
    window.location.href = '{{ url('/sentiment-analysis') }}/' + n + '/export';
};
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('confirmSentimentExportBtn');
    if (btn) {
        btn.onclick = exportSentimentComparisonPdf;
    }
    var modal = document.getElementById('sentimentExportModal');
    if (modal) {
        modal.onclick = function (e) {
            if (e.target === modal) {
                closeSentimentExportModal();
            }
        };
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            var m = document.getElementById('sentimentExportModal');
            if (m && m.style.display === 'flex') {
                closeSentimentExportModal();
            }
        }
    });
});
</script>

@include('sentiment-analysis.partials.sentiment-charts-init')
@endsection
