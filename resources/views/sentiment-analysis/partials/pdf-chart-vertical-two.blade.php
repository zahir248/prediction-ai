{{-- DomPDF: base64 SVG in <img>. Dimensions scaled for readable PDF size. --}}
@php
    use Illuminate\Support\Str;
    $xmlEsc = static function ($s): string {
        return htmlspecialchars((string) $s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    };
    $labels = $chartLabels ?? [];
    $values = array_values(array_map(static fn ($v) => max(0, min(100, (float) $v)), $chartValues ?? []));
    $colors = $barColors ?? ['#667eea', '#764ba2'];
    // Larger canvas (~1.48× previous) for PDF legibility
    $W = 560;
    $H = 310;
    $pl = 64;
    $pr = 18;
    $pb = 58;
    $pt = 22;
    $cw = $W - $pl - $pr;
    $ch = $H - $pt - $pb;
    $n = max(1, count($labels));
    $gap = 27;
    $bw = min(112, ($cw - $gap * ($n + 1)) / $n);
    $base = $pt + $ch;
    $fsTick = 11;
    $fsText = 12;

    $svg = '<?xml version="1.0" encoding="UTF-8"?>';
    $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$W.' '.$H.'" width="'.$W.'" height="'.$H.'">';
    $svg .= '<line x1="'.$pl.'" y1="'.$pt.'" x2="'.$pl.'" y2="'.$base.'" stroke="#e2e8f0" stroke-width="1.5"/>';
    $svg .= '<line x1="'.$pl.'" y1="'.$base.'" x2="'.($W - $pr).'" y2="'.$base.'" stroke="#94a3b8" stroke-width="1.5"/>';
    for ($yi = 0; $yi <= 4; $yi++) {
        $yv = $yi * 25;
        $yy = $base - ($ch * $yv / 100);
        $svg .= '<line x1="'.($pl - 4).'" y1="'.$yy.'" x2="'.$pl.'" y2="'.$yy.'" stroke="#e5e7eb" stroke-width="1"/>';
        $svg .= '<text x="'.($pl - 6).'" y="'.($yy + 4).'" text-anchor="end" font-size="'.$fsTick.'" fill="#64748b" font-family="DejaVu Sans, Arial, sans-serif">'.$yv.'</text>';
    }
    foreach ($labels as $i => $lab) {
        $v = $values[$i] ?? 0;
        $barH = max(3, $ch * $v / 100);
        $cx = $pl + $gap + $i * ($bw + $gap) + $bw / 2;
        $bx = $cx - $bw / 2;
        $by = $base - $barH;
        $fill = $colors[$i % max(1, count($colors))] ?? '#667eea';
        $textLab = Str::limit((string) $lab, 22);
        $svg .= '<rect x="'.$bx.'" y="'.$by.'" width="'.$bw.'" height="'.$barH.'" fill="'.$fill.'" rx="5"/>';
        $svg .= '<text x="'.$cx.'" y="'.($by - 7).'" text-anchor="middle" font-size="'.$fsText.'" fill="#1e293b" font-family="DejaVu Sans, Arial, sans-serif">'.round($v).'</text>';
        $svg .= '<text x="'.$cx.'" y="'.($H - 10).'" text-anchor="middle" font-size="'.$fsText.'" fill="#475569" font-family="DejaVu Sans, Arial, sans-serif">'.$xmlEsc($textLab).'</text>';
    }
    $svg .= '</svg>';
    $svgDataUri = 'data:image/svg+xml;base64,'.base64_encode($svg);
@endphp
<div class="sentiment-pdf-chart-svg" style="text-align: center; margin: 10px auto;">
    <img src="{{ $svgDataUri }}" alt="Bar chart" width="{{ $W }}" height="{{ $H }}" style="display: block; margin: 0 auto; max-width: 100%; width: {{ $W }}px; height: {{ $H }}px;"/>
</div>
