<?php
/**
 * DIRECT PDF GENERATOR
 * Run: php GENERATE_PDF.php
 * Output: public/docs/NUJUM_Offboarding_Documentation.pdf
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

$mdPath = base_path('OFFBOARDING_DOCUMENTATION.md');
$outDir = public_path('docs');
$pdfPath = $outDir . DIRECTORY_SEPARATOR . 'NUJUM_Offboarding_Documentation.pdf';

if (!is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

$md = file_get_contents($mdPath);

// Basic markdown to HTML
$html = $md;
$html = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $html);
$html = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $html);
$html = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $html);
$html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
$html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);
$html = preg_replace('/^\* (.*)$/m', '<li>$1</li>', $html);
$html = preg_replace('/(<li>.*<\/li>\n?)+/s', '<ul>$0</ul>', $html);

$lines = explode("\n", $html);
$html = '';
foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;
    if (!preg_match('/^<[^>]+>/', $line)) {
        $html .= '<p>' . $line . '</p>';
    } else {
        $html .= $line;
    }
    $html .= "\n";
}

$fullHtml = '<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>
@page{margin:1.5cm;size:A4}
body{font-family:Arial;font-size:10pt;line-height:1.6}
h1{font-size:18pt;font-weight:bold;border-bottom:2px solid #000;padding-bottom:8px;margin:25px 0 15px 0}
h2{font-size:14pt;font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:5px;margin:20px 0 12px 0}
h3{font-size:12pt;font-weight:bold;margin:15px 0 10px 0}
p{margin:8px 0}
ul,ol{margin:10px 0;padding-left:25px}
code{background:#f5f5f5;padding:2px 4px}
</style>
</head><body>
<div style="text-align:center;page-break-after:always;padding-top:5cm">
<h1 style="font-size:28pt;border:none;margin:0">NUJUM</h1>
<div style="font-size:14pt;color:#666;margin:20px 0">Software Developer Offboarding Documentation</div>
<div style="font-size:12pt">AI Prediction Analysis System</div>
<div style="margin-top:3cm;font-size:11pt;color:#888">
<p>Version: 1.0</p>
<p>Generated: ' . date('Y-m-d H:i:s') . '</p>
</div>
</div>
' . $html . '</body></html>';

$pdf = Pdf::loadHTML($fullHtml);
$pdf->setPaper('A4', 'portrait');
$pdf->save($pdfPath);

echo "PDF CREATED: $pdfPath\n";
echo "SIZE: " . number_format(filesize($pdfPath) / 1024, 2) . " KB\n";

