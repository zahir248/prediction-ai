{{-- Grouped bars PDF: base64 SVG in img — larger canvas for PDF. --}}
@php
    use Illuminate\Support\Str;
    $xmlEsc = static function (string $s): string {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    };
    $W = 640;
    $H = 350;
    $pl = 76;
    $pr = 24;
    $pb = 72;
    $pt = 72;
    $cw = $W - $pl - $pr;
    $ch = $H - $pt - $pb;
    $base = $pt + $ch;
    $groupGap = 40;
    $groupW = ($cw - $groupGap) / 2;
    $barGap = 6;
    $tripletW = $groupW - 16;
    $tw = ($tripletW - 2 * $barGap) / 3;
    $sets = [
        [$posA ?? 0, $neuA ?? 0, $negA ?? 0],
        [$posB ?? 0, $neuB ?? 0, $negB ?? 0],
    ];
    $groupTitles = [$labelA ?? 'A', $labelB ?? 'B'];
    $innerColors = ['#22c55e', '#94a3b8', '#ef4444'];
    $legends = [$legendPos ?? 'Pos', $legendNeu ?? 'Neu', $legendNeg ?? 'Neg'];
    $legendStep = 175;
    $fsLeg = 12;
    $fsTick = 11;
    $fsAxis = 12;
    $fsGroup = 12;

    $svg = '<?xml version="1.0" encoding="UTF-8"?>';
    $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$W.' '.$H.'" width="'.$W.'" height="'.$H.'">';
    foreach ($legends as $li => $leg) {
        $lx = $pl + $li * $legendStep;
        $svg .= '<rect x="'.$lx.'" y="12" width="14" height="14" fill="'.$innerColors[$li].'" rx="3"/>';
        $svg .= '<text x="'.($lx + 20).'" y="23" font-size="'.$fsLeg.'" fill="#334155" font-family="DejaVu Sans, Arial, sans-serif">'.$xmlEsc(Str::limit($leg, 28)).'</text>';
    }
    $svg .= '<line x1="'.$pl.'" y1="'.$pt.'" x2="'.$pl.'" y2="'.$base.'" stroke="#e2e8f0" stroke-width="1.5"/>';
    $svg .= '<line x1="'.$pl.'" y1="'.$base.'" x2="'.($W - $pr).'" y2="'.$base.'" stroke="#94a3b8" stroke-width="1.5"/>';
    for ($yi = 0; $yi <= 4; $yi++) {
        $yv = $yi * 25;
        $yy = $base - ($ch * $yv / 100);
        $svg .= '<line x1="'.($pl - 4).'" y1="'.$yy.'" x2="'.$pl.'" y2="'.$yy.'" stroke="#e5e7eb"/>';
        $svg .= '<text x="'.($pl - 6).'" y="'.($yy + 4).'" text-anchor="end" font-size="'.$fsTick.'" fill="#64748b" font-family="DejaVu Sans, Arial, sans-serif">'.$yv.'</text>';
    }
    foreach ($sets as $gi => $vals) {
        $gx = $pl + $gi * ($groupW + $groupGap) + 8;
        $vals = array_map(static fn ($v) => max(0, min(100, (float) $v)), $vals);
        foreach ($vals as $bi => $v) {
            $barH = max(3, $ch * $v / 100);
            $bx = $gx + $bi * ($tw + $barGap);
            $by = $base - $barH;
            $svg .= '<rect x="'.$bx.'" y="'.$by.'" width="'.$tw.'" height="'.$barH.'" fill="'.$innerColors[$bi].'" rx="4"/>';
        }
        $gc = $gx + (3 * $tw + 2 * $barGap) / 2;
        $gt = Str::limit((string) ($groupTitles[$gi] ?? ''), 22);
        $svg .= '<text x="'.$gc.'" y="'.($H - 14).'" text-anchor="middle" font-size="'.$fsGroup.'" fill="#1e293b" font-weight="700" font-family="DejaVu Sans, Arial, sans-serif">'.$xmlEsc($gt).'</text>';
    }
    $svg .= '</svg>';
    $svgDataUri = 'data:image/svg+xml;base64,'.base64_encode($svg);
@endphp
<div class="sentiment-pdf-chart-svg" style="text-align: center; margin: 10px auto;">
    <img src="{{ $svgDataUri }}" alt="Sentiment mix chart" width="{{ $W }}" height="{{ $H }}" style="display: block; margin: 0 auto; max-width: 100%; width: {{ $W }}px; height: {{ $H }}px;"/>
</div>
