<?php
// Standalone PDF generator - creates PDF directly from markdown
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

$mdFile = __DIR__ . '/OFFBOARDING_DOCUMENTATION.md';
$outputDir = __DIR__ . '/public/docs';
$pdfFile = $outputDir . '/NUJUM_Offboarding_Documentation.pdf';

echo "Starting PDF generation...\n";

if (!file_exists($mdFile)) {
    die("ERROR: Markdown file not found: $mdFile\n");
}

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "Created directory: $outputDir\n";
}

$markdown = file_get_contents($mdFile);
echo "Read markdown file (" . number_format(strlen($markdown)) . " bytes)\n";

// Convert markdown to HTML
function convertMarkdown($md) {
    $html = $md;
    $codeBlocks = [];
    
    // Preserve code blocks
    $html = preg_replace_callback('/```(\w+)?\n(.*?)```/s', function($m) use (&$codeBlocks) {
        $id = 'CODEBLOCK' . count($codeBlocks);
        $codeBlocks[$id] = '<pre style="background:#f5f5f5;border:1px solid #ddd;padding:10px;font-family:monospace;font-size:9pt;"><code>' . htmlspecialchars($m[2]) . '</code></pre>';
        return $id;
    }, $html);
    
    // Headers
    $html = preg_replace('/^###### (.*)$/m', '<h6>$1</h6>', $html);
    $html = preg_replace('/^##### (.*)$/m', '<h5>$1</h5>', $html);
    $html = preg_replace('/^#### (.*)$/m', '<h4>$1</h4>', $html);
    $html = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $html);
    
    $html = preg_replace('/^---\s*$/m', '<hr>', $html);
    $html = preg_replace('/`([^`]+)`/', '<code style="background:#f5f5f5;padding:2px 4px;">$1</code>', $html);
    $html = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $html);
    $html = preg_replace('/__(.*?)__/s', '<strong>$1</strong>', $html);
    $html = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $html);
    
    // Process lists
    $lines = explode("\n", $html);
    $result = [];
    $inList = false;
    $listType = 'ul';
    
    foreach ($lines as $line) {
        $t = trim($line);
        if (preg_match('/^[\*\-\+]\s+(.+)$/', $t, $m)) {
            if (!$inList) { $result[] = '<ul>'; $inList = true; $listType = 'ul'; }
            $result[] = '<li>' . $m[1] . '</li>';
        } elseif (preg_match('/^\d+\.\s+(.+)$/', $t, $m)) {
            if (!$inList || $listType !== 'ol') {
                if ($inList) $result[] = '</' . $listType . '>';
                $result[] = '<ol>'; $inList = true; $listType = 'ol';
            }
            $result[] = '<li>' . $m[1] . '</li>';
        } else {
            if ($inList) { $result[] = '</' . $listType . '>'; $inList = false; }
            if (empty($t)) {
                $result[] = '';
            } elseif (preg_match('/^<[^>]+>/', $t)) {
                $result[] = $line;
            } else {
                $result[] = '<p>' . $t . '</p>';
            }
        }
    }
    if ($inList) $result[] = '</' . $listType . '>';
    
    $html = implode("\n", $result);
    foreach ($codeBlocks as $id => $code) {
        $html = str_replace($id, $code, $html);
    }
    $html = preg_replace('/<p>\s*<\/p>/', '', $html);
    return $html;
}

$html = convertMarkdown($markdown);
echo "Converted markdown to HTML\n";

// Full PDF HTML with styling
$fullHtml = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 1.5cm; size: A4; }
body { font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333; font-size: 10pt; }
h1 { font-size: 18pt; font-weight: bold; margin-top: 25px; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 8px; page-break-after: avoid; }
h2 { font-size: 14pt; font-weight: bold; margin-top: 20px; margin-bottom: 12px; border-bottom: 1px solid #ccc; padding-bottom: 5px; page-break-after: avoid; }
h3 { font-size: 12pt; font-weight: bold; margin-top: 15px; margin-bottom: 10px; page-break-after: avoid; }
h4 { font-size: 11pt; font-weight: bold; margin-top: 12px; margin-bottom: 8px; }
p { margin: 8px 0; text-align: justify; }
ul, ol { margin: 10px 0; padding-left: 25px; }
li { margin: 5px 0; page-break-inside: avoid; }
code { background-color: #f5f5f5; padding: 2px 4px; border-radius: 3px; font-family: "Courier New", monospace; font-size: 9pt; }
pre { background-color: #f5f5f5; border: 1px solid #ddd; padding: 10px; margin: 10px 0; font-family: "Courier New", monospace; font-size: 9pt; page-break-inside: avoid; }
strong { font-weight: bold; }
hr { border: none; border-top: 1px solid #ccc; margin: 20px 0; }
</style>
</head>
<body>
<div style="text-align: center; page-break-after: always; padding-top: 5cm;">
<h1 style="font-size: 28pt; border: none; margin: 0 0 20px 0;">NUJUM</h1>
<div style="font-size: 14pt; color: #666; margin-bottom: 30px;">Software Developer Offboarding Documentation</div>
<div style="font-size: 12pt; margin-top: 10px;">AI Prediction Analysis System</div>
<div style="margin-top: 3cm; font-size: 11pt; color: #888;">
<p>Version: 1.0</p>
<p>Generated: ' . date('Y-m-d H:i:s') . '</p>
</div>
</div>
' . $html . '
</body>
</html>';

echo "Creating PDF...\n";

try {
    $pdf = Pdf::loadHTML($fullHtml);
    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'Arial',
        'dpi' => 150,
        'fontHeightRatio' => 0.9
    ]);
    
    $pdf->save($pdfFile);
    
    $size = filesize($pdfFile);
    echo "\n";
    echo "========================================\n";
    echo "SUCCESS! PDF Generated\n";
    echo "========================================\n";
    echo "File: $pdfFile\n";
    echo "Size: " . number_format($size / 1024, 2) . " KB\n";
    echo "\n";
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}

