@php
    // Helper function to convert markdown **text** to HTML <strong>text</strong>
    function convertMarkdownBold($text) {
        if (!is_string($text)) {
            return $text;
        }
        // Escape HTML first for security
        $escaped = e($text);
        // Convert **text** to <strong>text</strong>
        $converted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $escaped);
        return $converted;
    }

    $__reportLangMs = (($prediction->input_data['report_language'] ?? 'en') === 'ms');
    $__ui = function (string $en, string $ms) use ($__reportLangMs): string {
        return $__reportLangMs ? $ms : $en;
    };
    $__statusLabel = function (string $status) use ($__reportLangMs): string {
        if (!$__reportLangMs) {
            return ucfirst($status);
        }
        return match ($status) {
            'completed' => 'Selesai',
            'completed_with_warnings' => 'Selesai (dengan amaran)',
            'processing' => 'Memproses',
            'failed' => 'Gagal',
            'pending' => 'Menunggu',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($status),
        };
    };
    $__horizonLabel = function (?string $horizon) use ($__reportLangMs): string {
        if ($horizon === null || $horizon === '') {
            return '';
        }
        if (!$__reportLangMs) {
            return ucwords(str_replace('_', ' ', $horizon));
        }
        $map = [
            'next_two_days' => 'Dua Hari Akan Datang',
            'next_two_weeks' => 'Dua Minggu Akan Datang',
            'next_month' => 'Bulan Akan Datang',
            'three_months' => '3 Bulan',
            'six_months' => '6 Bulan',
            'twelve_months' => '12 Bulan',
            'two_years' => '2 Tahun',
        ];
        return $map[$horizon] ?? ucwords(str_replace('_', ' ', $horizon));
    };
    $__jsonKeyLabel = function (string $key) use ($__reportLangMs): string {
        if (!$__reportLangMs) {
            return ucfirst(str_replace('_', ' ', $key));
        }
        $map = [
            'point' => 'Perkara',
            'explanation' => 'Penjelasan',
            'risk' => 'Risiko',
            'level' => 'Tahap',
            'probability' => 'Kebarangkalian',
            'mitigation' => 'Strategi Pengurangan',
        ];
        return $map[$key] ?? ucfirst(str_replace('_', ' ', $key));
    };
    $__riskScaleLabel = function ($value) use ($__reportLangMs): string {
        $raw = trim(is_string($value) ? $value : (string) $value);
        if (!$__reportLangMs || $raw === '') {
            return $raw;
        }
        $norm = strtolower(preg_replace('/[\s_\-]+/u', ' ', $raw));
        $norm = trim($norm);
        static $map = [
            'critical' => 'Kritikal',
            'severe' => 'Teruk',
            'extreme' => 'Melampau',
            'catastrophic' => 'Katastrofik',
            'high' => 'Tinggi',
            'very high' => 'Sangat tinggi',
            'medium' => 'Sederhana',
            'moderate' => 'Sederhana',
            'low' => 'Rendah',
            'very low' => 'Sangat rendah',
            'minimal' => 'Minimum',
            'negligible' => 'Boleh diabaikan',
            'marginal' => 'Marginal',
            'significant' => 'Ketara',
            'substantial' => 'Bermakna',
            'almost certain' => 'Hampir pasti',
            'very likely' => 'Sangat berkemungkinan',
            'likely' => 'Berkemungkinan',
            'probable' => 'Berkemungkinan',
            'possible' => 'Mungkin',
            'unlikely' => 'Tidak berkemungkinan',
            'very unlikely' => 'Sangat tidak berkemungkinan',
            'rare' => 'Jarang',
            'remote' => 'Jauh (remeh)',
            'improbable' => 'Mustahil',
            'certain' => 'Pasti',
            'uncertain' => 'Tidak pasti',
            'frequent' => 'Kerap',
            'occasional' => 'Kadangkala',
            'seldom' => 'Jarang berlaku',
            'imminent' => 'Hampir berlaku',
            'high risk' => 'Risiko tinggi',
            'medium risk' => 'Risiko sederhana',
            'low risk' => 'Risiko rendah',
        ];
        return $map[$norm] ?? $raw;
    };
    $__localizeScaleInText = function (string $text) use ($__reportLangMs): string {
        if (!$__reportLangMs) {
            return $text;
        }
        static $ordered = null;
        if ($ordered === null) {
            $pairs = [
                'very high' => 'Sangat tinggi',
                'very low' => 'Sangat rendah',
                'very likely' => 'Sangat berkemungkinan',
                'very unlikely' => 'Sangat tidak berkemungkinan',
                'almost certain' => 'Hampir pasti',
                'high risk' => 'Risiko tinggi',
                'medium risk' => 'Risiko sederhana',
                'low risk' => 'Risiko rendah',
                'catastrophic' => 'Katastrofik',
                'substantial' => 'Bermakna',
                'significant' => 'Ketara',
                'negligible' => 'Boleh diabaikan',
                'improbable' => 'Mustahil',
                'moderate' => 'Sederhana',
                'critical' => 'Kritikal',
                'extreme' => 'Melampau',
                'probable' => 'Berkemungkinan',
                'unlikely' => 'Tidak berkemungkinan',
                'certain' => 'Pasti',
                'uncertain' => 'Tidak pasti',
                'minimal' => 'Minimum',
                'severe' => 'Teruk',
                'likely' => 'Berkemungkinan',
                'medium' => 'Sederhana',
                'possible' => 'Mungkin',
                'remote' => 'Jauh (remeh)',
                'rare' => 'Jarang',
                'high' => 'Tinggi',
                'low' => 'Rendah',
                'frequent' => 'Kerap',
                'occasional' => 'Kadangkala',
                'seldom' => 'Jarang berlaku',
                'imminent' => 'Hampir berlaku',
                'marginal' => 'Marginal',
            ];
            uksort($pairs, fn ($a, $b) => strlen($b) <=> strlen($a));
            $ordered = $pairs;
        }
        $out = $text;
        foreach ($ordered as $en => $ms) {
            $out = preg_replace('/\b' . preg_quote($en, '/') . '\b/iu', $ms, $out);
        }
        return $out;
    };
@endphp

<div class="prediction-content-wrapper" style="max-width: 100%; margin: 0; padding: 0;">
    <!-- Main Content (No Card) -->
    <div class="prediction-main-card" style="background: transparent; padding: 0;">
        <!-- Header -->
        <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
                <div style="flex: 1; min-width: 0;">
                    <h1 class="prediction-topic" style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0; word-wrap: break-word; overflow-wrap: break-word;">{{ $prediction->topic }}</h1>
                    @if($prediction->target)
                        <p class="prediction-target" style="color: #166534; font-size: 13px; margin: 8px 0; font-weight: 500; background: #f0fdf4; padding: 8px 12px; border-radius: 6px; border: 1px solid #bbf7d0; display: inline-block; max-width: 100%; word-wrap: break-word; overflow-wrap: break-word;">
                            <strong>{{ $__ui('Target:', 'Sasaran:') }}</strong> {{ $prediction->target }}
                        </p>
                    @endif
                    <p style="color: #64748b; font-size: 13px; margin: 8px 0 0 0;">
                        {{ $__ui('Created on', 'Dicipta pada') }} {{ $prediction->created_at->format('M d, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>

            @include('predictions.partials.report_sections', compact('prediction'))
        </div>
    </div>
</div>

