{{-- Horizontal bars PDF — taller rows and wider bar track. --}}
@php
    use Illuminate\Support\Str;
    $xmlEsc = static function (string $s): string {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    };
    $rows = $horizontalRows ?? [];
    $rowLabelKey = $rowLabelKey ?? 'label';
    $la = Str::limit((string) ($labelA ?? 'A'), 20);
    $lb = Str::limit((string) ($labelB ?? 'B'), 20);
    $colorA = '#667eea';
    $colorB = '#764ba2';
    $rowH = 40;
    $labelW = 148;
    $barMax = 300;
    $left = 12;
    $top = 48;
    $n = count($rows);
    $W = $left + $labelW + 12 + $barMax + 52;
    $H = $top + max(1, $n) * $rowH + 36;
    $fsLeg = 12;
    $fsLab = 11;
    $fsVal = 10;
    $barStroke = 14;

    $svg = '<?xml version="1.0" encoding="UTF-8"?>';
    $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$W.' '.$H.'" width="'.$W.'" height="'.$H.'">';
    $svg .= '<rect x="'.$left.'" y="10" width="12" height="12" fill="'.$colorA.'" rx="2"/>';
    $svg .= '<text x="'.($left + 18).'" y="21" font-size="'.$fsLeg.'" fill="#334155" font-family="DejaVu Sans, Arial, sans-serif">'.$xmlEsc($la).'</text>';
    $svg .= '<rect x="'.($left + 168).'" y="10" width="12" height="12" fill="'.$colorB.'" rx="2"/>';
    $svg .= '<text x="'.($left + 186).'" y="21" font-size="'.$fsLeg.'" fill="#334155" font-family="DejaVu Sans, Arial, sans-serif">'.$xmlEsc($lb).'</text>';
    foreach ($rows as $ri => $row) {
        $lab = Str::limit((string) ($row[$rowLabelKey] ?? ''), 26);
        $va = max(0, min(100, (float) ($row['a'] ?? 0)));
        $vb = max(0, min(100, (float) ($row['b'] ?? 0)));
        $y = $top + $ri * $rowH;
        $bwA = max(3, $barMax * $va / 100);
        $bwB = max(3, $barMax * $vb / 100);
        $svg .= '<text x="'.($left + $labelW).'" y="'.($y + 16).'" text-anchor="end" font-size="'.$fsLab.'" fill="#1e293b" font-family="DejaVu Sans, Arial, sans-serif">'.$xmlEsc($lab).'</text>';
        $svg .= '<rect x="'.($left + $labelW + 12).'" y="'.$y.'" width="'.$bwA.'" height="'.$barStroke.'" fill="'.$colorA.'" rx="3"/>';
        $svg .= '<text x="'.($left + $labelW + 12 + $bwA + 5).'" y="'.($y + 12).'" font-size="'.$fsVal.'" fill="#64748b" font-family="DejaVu Sans, Arial, sans-serif">'.round($va).'</text>';
        $svg .= '<rect x="'.($left + $labelW + 12).'" y="'.($y + 20).'" width="'.$bwB.'" height="'.$barStroke.'" fill="'.$colorB.'" rx="3"/>';
        $svg .= '<text x="'.($left + $labelW + 12 + $bwB + 5).'" y="'.($y + 32).'" font-size="'.$fsVal.'" fill="#64748b" font-family="DejaVu Sans, Arial, sans-serif">'.round($vb).'</text>';
    }
    $svg .= '</svg>';
    $svgDataUri = 'data:image/svg+xml;base64,'.base64_encode($svg);
@endphp
<div class="sentiment-pdf-chart-svg" style="text-align: center; margin: 10px auto;">
    <img src="{{ $svgDataUri }}" alt="Horizontal comparison chart" width="{{ $W }}" height="{{ $H }}" style="display: block; margin: 0 auto; max-width: 100%; width: {{ $W }}px; height: {{ $H }}px;"/>
</div>
