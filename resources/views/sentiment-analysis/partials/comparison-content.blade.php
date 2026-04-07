@php
    $r = $comparison->ai_result ?? [];
    $a = $r['profile_a_sentiment'] ?? [];
    $b = $r['profile_b_sentiment'] ?? [];
    $ms = (($r['report_language'] ?? $comparison->report_language ?? 'en') === 'ms');
    $__ui = fn (string $en, string $my): string => $ms ? $my : $en;
    $cid = (int) $comparison->id;
    $nameA = $r['profile_a_username'] ?? $comparison->socialMediaAnalysisA->username ?? 'Profile A';
    $nameB = $r['profile_b_username'] ?? $comparison->socialMediaAnalysisB->username ?? 'Profile B';
    $labelA = $comparison->socialMediaAnalysisA
        ? $comparison->socialMediaAnalysisA->profileDisplayLabel($nameA)
        : $nameA.' (—)';
    $labelB = $comparison->socialMediaAnalysisB
        ? $comparison->socialMediaAnalysisB->profileDisplayLabel($nameB)
        : $nameB.' (—)';

    $clamp = static function ($v, int $d = 50): int {
        if ($v === null || $v === '' || ! is_numeric($v)) {
            return $d;
        }

        return max(0, min(100, (int) round((float) $v)));
    };

    $os = is_array($r['overall_sentiment'] ?? null) ? $r['overall_sentiment'] : [];
    $polA = $clamp(data_get($os, 'polarity_index.profile_a', data_get($a, 'sentiment_score_0_100', 55)));
    $polB = $clamp(data_get($os, 'polarity_index.profile_b', data_get($b, 'sentiment_score_0_100', 55)));

    $posA = $clamp(data_get($os, 'positive_ratio.profile_a', 40));
    $posB = $clamp(data_get($os, 'positive_ratio.profile_b', 40));
    $neuA = $clamp(data_get($os, 'neutral_ratio.profile_a', 35));
    $neuB = $clamp(data_get($os, 'neutral_ratio.profile_b', 35));
    $negA = $clamp(data_get($os, 'negative_ratio.profile_a', 25));
    $negB = $clamp(data_get($os, 'negative_ratio.profile_b', 25));

    $mv = is_array($r['mentions_volume'] ?? null) ? $r['mentions_volume'] : [];
    $volA = $clamp($mv['relative_volume_a'] ?? 50);
    $volB = $clamp($mv['relative_volume_b'] ?? 50);

    $trendDims = [];
    if (! empty($r['trend_analysis']['dimensions']) && is_array($r['trend_analysis']['dimensions'])) {
        foreach ($r['trend_analysis']['dimensions'] as $row) {
            if (is_array($row) && isset($row['label'])) {
                $trendDims[] = [
                    'label' => (string) $row['label'],
                    'a' => $clamp($row['profile_a'] ?? null),
                    'b' => $clamp($row['profile_b'] ?? null),
                ];
            }
        }
    }

    $topics = [];
    if (! empty($r['key_topics']) && is_array($r['key_topics'])) {
        foreach ($r['key_topics'] as $row) {
            if (is_array($row) && ! empty($row['topic'])) {
                $topics[] = [
                    'topic' => (string) $row['topic'],
                    'a' => $clamp($row['salience_a'] ?? null),
                    'b' => $clamp($row['salience_b'] ?? null),
                ];
            }
        }
    }

    $drivers = [];
    if (! empty($r['sentiment_drivers']) && is_array($r['sentiment_drivers'])) {
        foreach ($r['sentiment_drivers'] as $row) {
            if (is_array($row) && ! empty($row['driver'])) {
                $drivers[] = [
                    'driver' => (string) $row['driver'],
                    'a' => $clamp($row['influence_a'] ?? null),
                    'b' => $clamp($row['influence_b'] ?? null),
                    'note' => (string) ($row['note'] ?? ''),
                ];
            }
        }
    }

    $chartPolarity = [
        'type' => 'barPolarity',
        'labels' => [$labelA, $labelB],
        'values' => [$polA, $polB],
    ];
    $chartVolume = [
        'type' => 'barVolume',
        'labels' => [$labelA, $labelB],
        'values' => [$volA, $volB],
    ];
    $chartValence = [
        'type' => 'groupedValence',
        'labels' => [$labelA, $labelB],
        'positive' => [$posA, $posB],
        'neutral' => [$neuA, $neuB],
        'negative' => [$negA, $negB],
        'labelPos' => $__ui('Positive (est.)', 'Positif (angg.)'),
        'labelNeu' => $__ui('Neutral (est.)', 'Neutral (angg.)'),
        'labelNeg' => $__ui('Negative (est.)', 'Negatif (angg.)'),
    ];
    $chartTrend = [
        'type' => 'barTrend',
        'rows' => $trendDims,
        'labelA' => $labelA,
        'labelB' => $labelB,
    ];
    $chartTopics = [
        'type' => 'horizontalTopics',
        'rows' => array_slice($topics, 0, 8),
        'labelA' => $labelA,
        'labelB' => $labelB,
    ];
    $chartDrivers = [
        'type' => 'horizontalDrivers',
        'rows' => array_slice($drivers, 0, 8),
        'labelA' => $labelA,
        'labelB' => $labelB,
    ];

    $scopeText = $r['scope_and_methodology'] ?? $r['methodology'] ?? '';
    $overallNarrative = $os['narrative'] ?? ($r['executive_summary'] ?? '');
    $trendNarrative = $r['trend_analysis']['narrative'] ?? '';
    $mentionsNarrative = $mv['narrative'] ?? '';
    $compBlock = is_array($r['comparison'] ?? null) ? $r['comparison'] : [];
    $compNarrative = $compBlock['narrative'] ?? ($r['side_by_side_summary'] ?? '');
    $matrixRows = [];
    if (! empty($compBlock['matrix']) && is_array($compBlock['matrix'])) {
        $matrixRows = $compBlock['matrix'];
    }
    $plat = is_array($r['platform_analysis'] ?? null) ? $r['platform_analysis'] : [];
    $risk = is_array($r['risk_alert'] ?? null) ? $r['risk_alert'] : [];
    $recs = $r['recommendations'] ?? [];
    if (! is_array($recs)) {
        $recs = [];
    }
    $riskLevel = strtoupper((string) ($risk['level'] ?? 'Low'));
    $riskBorder = match (true) {
        str_contains($riskLevel, 'HIGH') => '#ef4444',
        str_contains($riskLevel, 'MEDIUM') => '#f59e0b',
        default => '#22c55e',
    };
    $saFont = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
    $pdfFont = "'Times New Roman', Times, serif";
    $isPdfExport = ! empty($isPdfExport);
@endphp

<div class="sentiment-comparison-inner{{ $isPdfExport ? ' sentiment-pdf-mode' : '' }}" data-sentiment-comparison-id="{{ $cid }}" data-export-profile-line="{{ e($labelA.' vs '.$labelB) }}" style="width: 100%; max-width: 100%; margin: 0; padding: 0; box-sizing: border-box; font-family: {{ $isPdfExport ? $pdfFont : $saFont }};">
    @if (! $isPdfExport)
        {{-- Web only: in PDF the export wrapper already provides the report header (same pattern as social-media export-pdf). --}}
        <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px;">
            {{ $r['title'] ?? $__ui('Sentiment comparison', 'Perbandingan sentimen') }}
            <span style="display: block; font-size: 14px; font-weight: 400; color: #64748b; margin-top: 10px; line-height: 1.5;">
                {{ $labelA }} <span style="color: #94a3b8;">vs</span> {{ $labelB }}
                · {{ $comparison->created_at->format('M j, Y g:i A') }}
            </span>
        </h2>
    @endif

    @if (($r['status'] ?? '') === 'error')
        <div class="{{ $isPdfExport ? 'sentiment-pdf-alert-error' : '' }}" style="background: #fee2e2; border-radius: 10px; padding: 16px; color: #991b1b; margin-bottom: 32px; border: 1px solid #fecaca; font-size: 16px; line-height: 1.6;">
            {{ $__ui('This comparison did not complete successfully. Try running a new comparison.', 'Perbandingan ini tidak berjaya. Cuba jalankan perbandingan baharu.') }}
        </div>
    @endif

    {{-- Executive Summary (web: gradient card; PDF: styled like social-media .section / .content-box) --}}
    <div class="{{ $isPdfExport ? 'sentiment-pdf-exec-summary' : '' }}" style="margin-bottom: 32px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white;">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px; color: white;">{{ $__ui('Executive Summary', 'Ringkasan eksekutif') }}</h3>
        <p style="color: rgba(255,255,255,0.95); line-height: 1.8; margin: 0; font-size: 16px; word-wrap: break-word; overflow-wrap: break-word;">{{ $r['executive_summary'] ?? '—' }}</p>
    </div>

    {{-- Scope & Methodology --}}
    <div style="margin-bottom: 32px; padding: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -0.01em;">{{ $__ui('Scope & Methodology', 'Skop & metodologi') }}</h2>
        <p style="margin: 0; color: #64748b; line-height: 1.8; font-size: 16px;">{{ $scopeText !== '' ? $scopeText : '—' }}</p>
        @if (!empty($r['limitations']))
            <p style="margin: 14px 0 0; color: #64748b; font-size: 16px; line-height: 1.6;"><strong style="color: #374151;">{{ $__ui('Limitations', 'Had') }}</strong> {{ $r['limitations'] }}</p>
        @endif
    </div>

    {{-- Overall Sentiment --}}
    <div style="margin-bottom: 32px; padding: 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -0.01em;">{{ $__ui('Overall Sentiment', 'Sentimen keseluruhan') }}</h2>
        <p style="margin: 0 0 20px; color: #64748b; line-height: 1.8; font-size: 16px;">{{ $overallNarrative !== '' ? $overallNarrative : '—' }}</p>
        @if (!empty($os['note']))
            <p style="margin: 0 0 16px; font-size: 14px; color: #94a3b8; line-height: 1.5;">{{ $os['note'] }}</p>
        @endif

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; margin-bottom: 20px;">
            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #fafafa;">
                <div style="font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">{{ $__ui('Polarity index (0–100)', 'Indeks polariti (0–100)') }}</div>
                @if ($isPdfExport)
                    @include('sentiment-analysis.partials.pdf-chart-vertical-two', [
                        'chartLabels' => $chartPolarity['labels'] ?? [$labelA, $labelB],
                        'chartValues' => $chartPolarity['values'] ?? [$polA, $polB],
                        'barColors' => ['#667eea', '#764ba2'],
                    ])
                @else
                    <canvas class="sentiment-chart-canvas" data-config='@json($chartPolarity)' style="max-height: 220px; width: 100% !important; height: 220px !important;"></canvas>
                @endif
            </div>
            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #fafafa;">
                <div style="font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">{{ $__ui('Relative activity / visibility (est.)', 'Aktiviti / keterlihatan relatif (angg.)') }}</div>
                @if ($isPdfExport)
                    @include('sentiment-analysis.partials.pdf-chart-vertical-two', [
                        'chartLabels' => $chartVolume['labels'] ?? [$labelA, $labelB],
                        'chartValues' => $chartVolume['values'] ?? [$volA, $volB],
                        'barColors' => ['#667eea', '#764ba2'],
                    ])
                @else
                    <canvas class="sentiment-chart-canvas" data-config='@json($chartVolume)' style="max-height: 220px; width: 100% !important; height: 220px !important;"></canvas>
                @endif
            </div>
        </div>

        <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #fafafa; margin-bottom: 20px;">
            <div style="font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">{{ $__ui('Estimated sentiment mix by profile', 'Anggaran campuran sentimen mengikut profil') }}</div>
            @if ($isPdfExport)
                @include('sentiment-analysis.partials.pdf-chart-valence', [
                    'labelA' => $labelA,
                    'labelB' => $labelB,
                    'posA' => $posA,
                    'posB' => $posB,
                    'neuA' => $neuA,
                    'neuB' => $neuB,
                    'negA' => $negA,
                    'negB' => $negB,
                    'legendPos' => $chartValence['labelPos'] ?? 'Positive',
                    'legendNeu' => $chartValence['labelNeu'] ?? 'Neutral',
                    'legendNeg' => $chartValence['labelNeg'] ?? 'Negative',
                ])
            @else
                <canvas class="sentiment-chart-canvas" data-config='@json($chartValence)' style="max-height: 280px; width: 100% !important; height: 280px !important;"></canvas>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; background: white;">
                <h3 style="margin: 0 0 12px; font-size: 18px; font-weight: 600; color: #667eea; letter-spacing: -0.01em;">{{ $labelA }}</h3>
                <dl style="margin: 0; font-size: 16px; color: #64748b; line-height: 1.6;">
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Sentiment score', 'Skor sentimen') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ $clamp($a['sentiment_score_0_100'] ?? null, $polA) }}/100</dd>
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Dominant tone', 'Nada dominan') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ $a['dominant_tone'] ?? '—' }}</dd>
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Positivity', 'Positiviti') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ $a['positivity_assessment'] ?? '—' }}</dd>
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Themes', 'Tema') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ isset($a['emotional_themes']) && is_array($a['emotional_themes']) ? implode(', ', $a['emotional_themes']) : '—' }}</dd>
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Controversy', 'Kontroversi') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ $a['controversy_or_conflict_level'] ?? '—' }}</dd>
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Audience mood', 'Suasana penonton') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ $a['audience_facing_mood'] ?? '—' }}</dd>
                </dl>
            </div>
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; background: white;">
                <h3 style="margin: 0 0 12px; font-size: 18px; font-weight: 600; color: #764ba2; letter-spacing: -0.01em;">{{ $labelB }}</h3>
                <dl style="margin: 0; font-size: 16px; color: #64748b; line-height: 1.6;">
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Sentiment score', 'Skor sentimen') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ $clamp($b['sentiment_score_0_100'] ?? null, $polB) }}/100</dd>
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Dominant tone', 'Nada dominan') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ $b['dominant_tone'] ?? '—' }}</dd>
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Positivity', 'Positiviti') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ $b['positivity_assessment'] ?? '—' }}</dd>
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Themes', 'Tema') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ isset($b['emotional_themes']) && is_array($b['emotional_themes']) ? implode(', ', $b['emotional_themes']) : '—' }}</dd>
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Controversy', 'Kontroversi') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ $b['controversy_or_conflict_level'] ?? '—' }}</dd>
                    <dt style="font-weight: 600; margin-top: 8px; color: #374151;">{{ $__ui('Audience mood', 'Suasana penonton') }}</dt>
                    <dd style="margin: 4px 0 0;">{{ $b['audience_facing_mood'] ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Trend Analysis --}}
    <div style="margin-bottom: 32px; padding: 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -0.01em;">{{ $__ui('Trend Analysis', 'Analisis trend') }}</h2>
        <p style="margin: 0 0 18px; color: #64748b; line-height: 1.8; font-size: 16px;">{{ $trendNarrative !== '' ? $trendNarrative : '—' }}</p>
        @if (count($trendDims) > 0)
            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #fafafa;">
                @if ($isPdfExport)
                    @include('sentiment-analysis.partials.pdf-chart-trend', [
                        'trendRows' => $trendDims,
                        'labelA' => $labelA,
                        'labelB' => $labelB,
                    ])
                @else
                    <canvas class="sentiment-chart-canvas" data-config='@json($chartTrend)' style="max-height: 320px; width: 100% !important; height: 320px !important;"></canvas>
                @endif
            </div>
        @endif
    </div>

    {{-- Key Topics --}}
    <div style="margin-bottom: 32px; padding: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -0.01em;">{{ $__ui('Key Topics', 'Topik utama') }}</h2>
        @if (count($topics) > 0)
            <ul style="margin: 0 0 16px; padding-left: 20px; color: #64748b; line-height: 1.8; font-size: 16px;">
                @foreach ($topics as $t)
                    <li style="margin-bottom: 6px;"><strong>{{ $t['topic'] }}</strong> — {{ $labelA }}: {{ $t['a'] }}, {{ $labelB }}: {{ $t['b'] }}</li>
                @endforeach
            </ul>
            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #ffffff;">
                @if ($isPdfExport)
                    @include('sentiment-analysis.partials.pdf-chart-horizontal-rows', [
                        'horizontalRows' => $topics,
                        'rowLabelKey' => 'topic',
                        'labelA' => $labelA,
                        'labelB' => $labelB,
                    ])
                @else
                    <canvas class="sentiment-chart-canvas" data-config='@json($chartTopics)' style="max-height: 360px; width: 100% !important; height: 360px !important;"></canvas>
                @endif
            </div>
        @else
            <p style="margin: 0; color: #64748b;">—</p>
        @endif
    </div>

    {{-- Sentiment Drivers --}}
    <div style="margin-bottom: 32px; padding: 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -0.01em;">{{ $__ui('Sentiment Drivers', 'Pemandu sentimen') }}</h2>
        @if (count($drivers) > 0)
            <div style="margin-bottom: 16px;">
                @foreach ($drivers as $d)
                    <div style="border-left: 3px solid #0ea5e9; padding: 10px 14px; margin-bottom: 10px; background: #f8fafc;">
                        <div style="font-weight: 600; color: #1e293b; font-size: 16px;">{{ $d['driver'] }}</div>
                        <div style="color: #64748b; font-size: 16px; line-height: 1.6; margin-top: 4px;">{{ $labelA }}: {{ $d['a'] }} · {{ $labelB }}: {{ $d['b'] }}@if($d['note'] !== '') — {{ $d['note'] }}@endif</div>
                    </div>
                @endforeach
            </div>
            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #fafafa;">
                @if ($isPdfExport)
                    @include('sentiment-analysis.partials.pdf-chart-horizontal-rows', [
                        'horizontalRows' => $drivers,
                        'rowLabelKey' => 'driver',
                        'labelA' => $labelA,
                        'labelB' => $labelB,
                    ])
                @else
                    <canvas class="sentiment-chart-canvas" data-config='@json($chartDrivers)' style="max-height: 360px; width: 100% !important; height: 360px !important;"></canvas>
                @endif
            </div>
        @else
            <p style="margin: 0; color: #64748b;">—</p>
        @endif
    </div>

    {{-- Mentions Volume --}}
    <div style="margin-bottom: 32px; padding: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -0.01em;">{{ $__ui('Mentions Volume', 'Jumlah sebutan / volum') }}</h2>
        <p style="margin: 0; color: #64748b; line-height: 1.8; font-size: 16px;">{{ $mentionsNarrative !== '' ? $mentionsNarrative : '—' }}</p>
    </div>

    {{-- Comparison --}}
    <div style="margin-bottom: 32px; padding: 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -0.01em;">{{ $__ui('Comparison', 'Perbandingan') }}</h2>
        <p style="margin: 0 0 18px; color: #64748b; line-height: 1.8; font-size: 16px;">{{ $compNarrative !== '' ? $compNarrative : '—' }}</p>
        @php
            $tableRows = $matrixRows;
            if (empty($tableRows) && !empty($r['key_differences']) && is_array($r['key_differences'])) {
                $tableRows = $r['key_differences'];
            }
        @endphp
        @if (!empty($tableRows))
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 16px;">
                    <thead>
                        <tr style="background: #f1f5f9;">
                            <th style="text-align: left; padding: 10px 12px; border: 1px solid #e2e8f0;">{{ $__ui('Dimension', 'Dimensi') }}</th>
                            <th style="text-align: left; padding: 10px 12px; border: 1px solid #e2e8f0;">{{ $labelA }}</th>
                            <th style="text-align: left; padding: 10px 12px; border: 1px solid #e2e8f0;">{{ $labelB }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tableRows as $row)
                            @if (is_array($row))
                                <tr>
                                    <td style="padding: 10px 12px; border: 1px solid #e2e8f0; font-weight: 600; color: #334155;">{{ $row['dimension'] ?? $row['aspect'] ?? '—' }}</td>
                                    <td style="padding: 10px 12px; border: 1px solid #e2e8f0; color: #475569;">{{ $row['profile_a'] ?? '—' }}</td>
                                    <td style="padding: 10px 12px; border: 1px solid #e2e8f0; color: #475569;">{{ $row['profile_b'] ?? '—' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Platform Analysis --}}
    <div style="margin-bottom: 32px; padding: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -0.01em;">{{ $__ui('Platform Analysis', 'Analisis platform') }}</h2>
        <p style="margin: 0 0 16px; color: #64748b; line-height: 1.8; font-size: 16px;">{{ $plat['narrative'] ?? '—' }}</p>
        @if (!empty($plat['platforms']) && is_array($plat['platforms']))
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach ($plat['platforms'] as $p)
                    @if (is_array($p))
                        <div style="padding: 12px 14px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 6px; font-size: 16px;">{{ $p['platform'] ?? 'Platform' }}</div>
                            <div style="font-size: 16px; line-height: 1.6; color: #64748b;"><strong style="color: #374151;">{{ $labelA }}:</strong> {{ $p['profile_a_note'] ?? '—' }}</div>
                            <div style="font-size: 16px; line-height: 1.6; color: #64748b; margin-top: 4px;"><strong style="color: #374151;">{{ $labelB }}:</strong> {{ $p['profile_b_note'] ?? '—' }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Risk Alert --}}
    <div class="{{ $isPdfExport ? 'sentiment-pdf-keep-border' : '' }}" style="margin-bottom: 32px; padding: 24px; background: #fff; border-radius: 12px; border: 2px solid {{ $riskBorder }};">
        <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -0.01em;">{{ $__ui('Risk Alert', 'Amaran risiko') }}</h2>
        <p style="margin: 0 0 8px; font-size: 14px; font-weight: 700; color: {{ $riskBorder }};">{{ $__ui('Level', 'Tahap') }}: {{ $risk['level'] ?? '—' }}</p>
        <p style="margin: 0 0 14px; color: #64748b; line-height: 1.8; font-size: 16px;">{{ $risk['summary'] ?? '—' }}</p>
        @if (!empty($risk['alerts']) && is_array($risk['alerts']))
            <ul style="margin: 0; padding-left: 20px; color: #64748b; font-size: 16px; line-height: 1.7;">
                @foreach ($risk['alerts'] as $al)
                    <li style="margin-bottom: 6px;">{{ $al }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Recommendation --}}
    <div style="margin-bottom: 32px; padding: 24px; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1px solid #86efac; border-radius: 12px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -0.01em;">{{ $__ui('Recommendation', 'Cadangan') }}</h2>
        @if (count($recs) > 0)
            <ul style="margin: 0; padding-left: 20px; color: #166534; line-height: 1.8; font-size: 16px;">
                @foreach ($recs as $rec)
                    <li style="margin-bottom: 8px;">{{ $rec }}</li>
                @endforeach
            </ul>
        @else
            <p style="margin: 0; color: #64748b;">—</p>
        @endif
    </div>

    @if (!empty($r['similarities']) && is_array($r['similarities']))
        <div style="margin-bottom: 24px; padding: 24px; background: #fafafa; border: 1px solid #e5e7eb; border-radius: 12px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 12px; letter-spacing: -0.01em;">{{ $__ui('Similarities', 'Persamaan') }}</h3>
            <ul style="margin: 0; padding-left: 20px; color: #64748b; font-size: 16px; line-height: 1.8;">
                @foreach ($r['similarities'] as $item)
                    <li style="margin-bottom: 4px;">{{ is_string($item) ? $item : json_encode($item) }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (!empty($r['comparative_insights']) && is_array($r['comparative_insights']))
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 12px; font-weight: 700; color: #64748b; margin: 0 0 12px; text-transform: uppercase; letter-spacing: 0.04em;">{{ $__ui('Additional insights', 'Wawasan tambahan') }}</h3>
            @foreach ($r['comparative_insights'] as $ins)
                @if (is_array($ins))
                    <div style="border-left: 3px solid #667eea; padding: 10px 14px; margin-bottom: 10px; background: #f8fafc;">
                        <div style="font-weight: 600; color: #1e293b; font-size: 16px;">{{ $ins['point'] ?? '' }}</div>
                        <div style="color: #64748b; font-size: 16px; line-height: 1.8; margin-top: 6px;">{{ $ins['explanation'] ?? '' }}</div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    @if (!empty($r['note']))
        <p style="font-size: 14px; color: #94a3b8; line-height: 1.6; margin-bottom: 20px;"><strong>{{ $__ui('Note', 'Nota') }}</strong> {{ $r['note'] }}</p>
    @endif
</div>
