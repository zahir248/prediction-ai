{{-- Trend dimensions bar chart PDF — wider/taller for readability. --}}
@php
    use Illuminate\Support\Str;
    $xmlEsc = static function (string $s): string {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    };
    $rows = $trendRows ?? [];
    $n = max(1, count($rows));
    $W = (int) min(780, 80 + $n * 76);
    $H = 300;
    $pl = 68;
    $pr = 16;
    $pb = 72;
    $pt = 20;
    $cw = $W - $pl - $pr;
    $ch = $H - $pt - $pb;
    $base = $pt + $ch;
    $slotW = $cw / $n;
    $pairGap = 6;
    $barW = min(26, ($slotW - 10 - $pairGap) / 2);
    $colorA = '#667eea';
    $colorB = '#764ba2';
    $la = Str::limit((string) ($labelA ?? 'A'), 18);
    $lb = Str::limit((string) ($labelB ?? 'B'), 18);
    $fsTick = 11;
    $fsLeg = 11;
    $fsDim = 10;

    $svg = '<?xml version="1.0" encoding="UTF-8"?>';
    $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$W.' '.$H.'" width="'.$W.'" height="'.$H.'">';
    $svg .= '<line x1="'.$pl.'" y1="'.$pt.'" x2="'.$pl.'" y2="'.$base.'" stroke="#e2e8f0" stroke-width="1.5"/>';
    $svg .= '<line x1="'.$pl.'" y1="'.$base.'" x2="'.($W - $pr).'" y2="'.$base.'" stroke="#94a3b8" stroke-width="1.5"/>';
    for ($yi = 0; $yi <= 4; $yi++) {
        $yv = $yi * 25;
        $yy = $base - ($ch * $yv / 100);
        $svg .= '<line x1="'.($pl - 4).'" y1="'.$yy.'" x2="'.$pl.'" y2="'.$yy.'" stroke="#e5e7eb"/>';
        $svg .= '<text x="'.($pl - 6).'" y="'.($yy + 4).'" text-anchor="end" font-size="'.$fsTick.'" fill="#64748b" font-family="DejaVu Sans, Arial, sans-serif">'.$yv.'</text>';
    }
    $svg .= '<rect x="'.($pl + 10).'" y="8" width="12" height="12" fill="'.$colorA.'" rx="2"/>';
    $svg .= '<text x="'.($pl + 28).'" y="18" font-size="'.$fsLeg.'" fill="#334155" font-family="DejaVu Sans, Arial, sans-serif">'.$xmlEsc($la).'</text>';
    $svg .= '<rect x="'.($pl + 168).'" y="8" width="12" height="12" fill="'.$colorB.'" rx="2"/>';
    $svg .= '<text x="'.($pl + 186).'" y="18" font-size="'.$fsLeg.'" fill="#334155" font-family="DejaVu Sans, Arial, sans-serif">'.$xmlEsc($lb).'</text>';
    foreach ($rows as $ri => $row) {
        $va = max(0, min(100, (float) ($row['a'] ?? 0)));
        $vb = max(0, min(100, (float) ($row['b'] ?? 0)));
        $slotX = $pl + $ri * $slotW + ($slotW - (2 * $barW + $pairGap)) / 2;
        $ha = max(3, $ch * $va / 100);
        $hb = max(3, $ch * $vb / 100);
        $lab = Str::limit((string) ($row['label'] ?? ''), 12);
        $svg .= '<rect x="'.$slotX.'" y="'.($base - $ha).'" width="'.$barW.'" height="'.$ha.'" fill="'.$colorA.'" rx="4"/>';
        $svg .= '<rect x="'.($slotX + $barW + $pairGap).'" y="'.($base - $hb).'" width="'.$barW.'" height="'.$hb.'" fill="'.$colorB.'" rx="4"/>';
        $svg .= '<text x="'.($pl + $ri * $slotW + $slotW / 2).'" y="'.($H - 10).'" text-anchor="middle" font-size="'.$fsDim.'" fill="#475569" font-family="DejaVu Sans, Arial, sans-serif">'.$xmlEsc($lab).'</text>';
    }
    $svg .= '</svg>';
    $svgDataUri = 'data:image/svg+xml;base64,'.base64_encode($svg);
@endphp
<div class="sentiment-pdf-chart-svg" style="text-align: center; margin: 10px auto;">
    <img src="{{ $svgDataUri }}" alt="Trend chart" width="{{ $W }}" height="{{ $H }}" style="display: block; margin: 0 auto; max-width: 100%; width: {{ $W }}px; height: {{ $H }}px;"/>
</div>
