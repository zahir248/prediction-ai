@php
    $analysis = $analysis ?? [];
    $__reportLangMs = (($analysis['report_language'] ?? 'en') === 'ms');
    $__fieldKeyUi = function (string $key) use ($__reportLangMs): string {
        if (!$__reportLangMs) {
            return ucwords(str_replace('_', ' ', $key));
        }
        $map = [
            'recommendations' => 'Cadangan',
            'evidence' => 'Bukti',
            'concerns' => 'Kebimbangan',
            'strengths' => 'Kekuatan',
            'indicators' => 'Petunjuk',
            'notable_patterns' => 'Corak Ketara',
            'key_characteristics' => 'Ciri Utama',
            'summary' => 'Ringkasan',
            'overview' => 'Gambaran Keseluruhan',
            'description' => 'Penerangan',
            'current_focus' => 'Tumpuan semasa',
            'expertise_areas' => 'Bidang kepakaran',
            'industry_positioning' => 'Kedudukan dalam industri',
            'career_goals' => 'Matlamat kerjaya',
            'growth_potential' => 'Potensi pertumbuhan',
            'market_value' => 'Nilai pasaran',
            'current_political_focus' => 'Tumpuan politik semasa',
            'political_expertise_areas' => 'Bidang kepakaran politik',
            'political_positioning' => 'Kedudukan politik',
            'political_goals' => 'Matlamat politik',
            'political_growth_potential' => 'Potensi pertumbuhan politik',
            'political_market_value' => 'Nilai pengaruh politik',
            'online_presence' => 'Kehadiran dalam talian',
            'content_quality' => 'Kualiti kandungan',
            'brand_consistency' => 'Konsisten jenama',
            'platform_utilization' => 'Penggunaan platform',
            'audience_engagement' => 'Penglibatan audiens',
            'professionalism_score' => 'Skor profesionalisme',
            'confidence' => 'Keyakinan',
            'political_affiliation_score' => 'Skor afiliasi politik',
            'political_leanings' => 'Kecondongan politik',
            'political_engagement' => 'Penglibatan politik',
            'political_content' => 'Kandungan politik',
            'political_network' => 'Rangkaian politik',
            'political_consistency' => 'Kekonsistenan politik',
            'political_influence' => 'Pengaruh politik',
            'political_controversies' => 'Kontroversi politik',
            'political_communication_style' => 'Gaya komunikasi politik',
            'political_credibility' => 'Kredibiliti politik',
            'political_brand_consistency' => 'Konsisten jenama politik',
            'political_platform_utilization' => 'Penggunaan platform (politik)',
            'political_audience_engagement' => 'Penglibatan audiens (politik)',
        ];
        return $map[$key] ?? ucwords(str_replace('_', ' ', $key));
    };
@endphp
<div style="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
    <h3 style="font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 16px; letter-spacing: -0.02em; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">{{ $title }}</h3>
    
    @foreach($data as $key => $value)
        @if($key === 'recommendations' || $key === 'evidence' || $key === 'concerns' || $key === 'strengths' || $key === 'indicators' || $key === 'notable_patterns' || $key === 'key_characteristics')
            @if(is_array($value) && count($value) > 0)
                <div style="margin-bottom: 12px;">
                    <strong style="color: #374151;">{{ $__fieldKeyUi($key) }}:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        @foreach($value as $item)
                            <li style="margin-bottom: 4px; color: #64748b; line-height: 1.6;">
                                @if(is_string($item))
                                    {{ $item }}
                                @elseif(is_array($item))
                                    {{ json_encode($item, JSON_PRETTY_PRINT) }}
                                @else
                                    {{ $item ?? '' }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @elseif(is_string($value) && trim($value) !== '')
            <div style="margin-bottom: 12px;">
                <strong style="color: #374151;">{{ $__fieldKeyUi($key) }}:</strong> 
                <span style="color: #64748b; line-height: 1.6;">{{ $value }}</span>
            </div>
        @elseif(is_numeric($value))
            <div style="margin-bottom: 12px;">
                <strong style="color: #374151;">{{ $__fieldKeyUi($key) }}:</strong> 
                <span style="color: #64748b; line-height: 1.6;">{{ $value }}</span>
            </div>
        @endif
    @endforeach
</div>
