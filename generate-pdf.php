<?php
// Generate PDF directly from markdown - standalone script
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

$mdFile = __DIR__ . '/OFFBOARDING_DOCUMENTATION.md';
$outputDir = __DIR__ . '/public/docs';
$pdfFile = $outputDir . '/NUJUM_Offboarding_Documentation.pdf';

if (!file_exists($mdFile)) {
    die("ERROR: Markdown file not found: $mdFile\n");
}

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$markdown = file_get_contents($mdFile);

// Markdown to HTML conversion
function markdownToHtml($markdown) {
    $html = $markdown;
    $codeBlocks = [];
    
    // Preserve code blocks
    $html = preg_replace_callback('/```(\w+)?\n(.*?)```/s', function($m) use (&$codeBlocks) {
        $id = 'CODE_' . count($codeBlocks);
        $codeBlocks[$id] = '<pre class="ascii-diagram"><code>' . htmlspecialchars($m[2]) . '</code></pre>';
        return $id;
    }, $html);
    
    // Headers
    $html = preg_replace('/^###### (.*)$/m', '<h6>$1</h6>', $html);
    $html = preg_replace('/^##### (.*)$/m', '<h5>$1</h5>', $html);
    $html = preg_replace('/^#### (.*)$/m', '<h4>$1</h4>', $html);
    $html = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $html);
    
    // Horizontal rules
    $html = preg_replace('/^---\s*$/m', '<hr>', $html);
    
    // Inline code
    $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);
    
    // Bold
    $html = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $html);
    $html = preg_replace('/__(.*?)__/s', '<strong>$1</strong>', $html);
    
    // Italic
    $html = preg_replace('/(?<!<strong>)\*(?!\*)(.*?)(?<!\*)\*(?!\*)(?!<\/strong>)/', '<em>$1</em>', $html);
    
    // Links
    $html = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $html);
    
    // Lists
    $lines = explode("\n", $html);
    $processed = [];
    $inList = false;
    $listType = 'ul';
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        
        if (preg_match('/^[\*\-\+]\s+(.+)$/', $trimmed, $matches)) {
            if (!$inList) {
                $processed[] = '<ul>';
                $inList = true;
                $listType = 'ul';
            }
            $processed[] = '<li>' . $matches[1] . '</li>';
        } elseif (preg_match('/^\d+\.\s+(.+)$/', $trimmed, $matches)) {
            if (!$inList || $listType !== 'ol') {
                if ($inList) {
                    $processed[] = '</' . $listType . '>';
                }
                $processed[] = '<ol>';
                $inList = true;
                $listType = 'ol';
            }
            $processed[] = '<li>' . $matches[1] . '</li>';
        } else {
            if ($inList) {
                $processed[] = '</' . $listType . '>';
                $inList = false;
            }
            
            if (empty($trimmed)) {
                $processed[] = '';
            } elseif (preg_match('/^<[^>]+>/', $trimmed)) {
                $processed[] = $line;
            } else {
                $processed[] = '<p>' . $trimmed . '</p>';
            }
        }
    }
    
    if ($inList) {
        $processed[] = '</' . $listType . '>';
    }
    
    $html = implode("\n", $processed);
    
    // Restore code blocks
    foreach ($codeBlocks as $id => $code) {
        $html = str_replace($id, $code, $html);
    }
    
    $html = preg_replace('/<p>\s*<\/p>/', '', $html);
    $html = preg_replace('/\n{3,}/', "\n\n", $html);
    
    return $html;
}

$htmlContent = markdownToHtml($markdown);

// Create PDF view content
$pdfContent = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 1.5cm; size: A4; }
        body { font-family: Arial, sans-serif; line-height: 1.6; font-size: 10pt; }
        h1 { font-size: 18pt; font-weight: bold; margin-top: 25px; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        h2 { font-size: 14pt; font-weight: bold; margin-top: 20px; margin-bottom: 12px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        h3 { font-size: 12pt; font-weight: bold; margin-top: 15px; margin-bottom: 10px; }
        h4 { font-size: 11pt; font-weight: bold; margin-top: 12px; margin-bottom: 8px; }
        p { margin: 8px 0; text-align: justify; }
        ul, ol { margin: 10px 0; padding-left: 25px; }
        li { margin: 5px 0; }
        code { background-color: #f5f5f5; padding: 2px 4px; border-radius: 3px; font-family: Courier New, monospace; font-size: 9pt; }
        pre { background-color: #f5f5f5; border: 1px solid #ddd; padding: 10px; margin: 10px 0; font-family: Courier New, monospace; font-size: 9pt; }
        strong { font-weight: bold; }
        hr { border: none; border-top: 1px solid #ccc; margin: 20px 0; }
    </style>
</head>
<body>
    <div style="text-align: center; page-break-after: always; padding-top: 5cm;">
        <h1 style="font-size: 28pt; border: none; margin: 0;">NUJUM</h1>
        <div style="font-size: 14pt; color: #666; margin: 20px 0;">Software Developer Offboarding Documentation</div>
        <div style="font-size: 12pt; margin-top: 10px;">AI Prediction Analysis System</div>
        <div style="margin-top: 3cm; font-size: 11pt; color: #888;">
            <p>Version: 1.0</p>
            <p>Generated: ' . date('Y-m-d H:i:s') . '</p>
        </div>
    </div>
    ' . $htmlContent . '
</body>
</html>';

$pdf = Pdf::loadHTML($pdfContent);
$pdf->setPaper('A4', 'portrait');
$pdf->setOptions([
    'isHtml5ParserEnabled' => true,
    'isRemoteEnabled' => true,
    'defaultFont' => 'Arial',
    'dpi' => 150,
    'fontHeightRatio' => 0.9
]);

$pdf->save($pdfFile);

echo "PDF generated successfully!\n";
echo "Location: $pdfFile\n";
echo "Size: " . number_format(filesize($pdfFile) / 1024, 2) . " KB\n";

