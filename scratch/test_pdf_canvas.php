<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);

$html = '
<!DOCTYPE html>
<html>
<head>
<style>
@page { margin: 25mm 15mm 20mm 15mm; }
body { font-family: DejaVu Sans; font-size: 11pt; color: #1e293b; margin: 0; }
header { position: fixed; top: -18mm; left: 0; right: 0; height: 12mm; border-bottom: 2px solid #d97706; }
footer { position: fixed; bottom: -12mm; left: 0; right: 0; height: 8mm; border-top: 1px solid #cbd5e1; font-size: 8pt; color: #64748b; }
.page-break { page-break-after: always; }
</style>
</head>
<body>
<header>
    <table style="width: 100%;">
        <tr>
            <td style="font-size: 12pt; font-weight: bold; color: #0f172a;">CIVIL SERVICES GURUKUL</td>
            <td style="text-align: right; font-size: 8pt; color: #b45309;">Daily News Analysis</td>
        </tr>
    </table>
</header>
<footer>
    <table style="width: 100%;">
        <tr>
            <td>Civil Services Gurukul &bull; All Rights Reserved</td>
            <td style="text-align: right;">Page <span class="page-num"></span></td>
        </tr>
    </table>
</footer>
<main>
    <h1>Page 1 Content</h1>
    <p>Testing watermark and page layout.</p>
    <div class="page-break"></div>
    <h1>Page 2 Content</h1>
    <p>Testing page 2 watermark and page layout.</p>
</main>

<script type="text/php">
if (isset($pdf)) {
    $font = $fontMetrics->getFont("DejaVu Sans", "bold");
    $normalFont = $fontMetrics->getFont("DejaVu Sans", "normal");
    
    // 1. Watermark in Center of every page
    $w = $pdf->get_width();
    $h = $pdf->get_height();
    
    // Diagonal angle -38 degrees
    $watermarkColor = array(0.92, 0.92, 0.94); // subtle grey/amber watermark
    $text = "CIVIL SERVICES GURUKUL";
    $sub = "www.civilservicegurukul.com";
    
    // Centered coordinates for rotation
    $pdf->page_text($w / 2 - 190, $h / 2 + 10, $text, $font, 32, $watermarkColor, 0, 0, -38);
    $pdf->page_text($w / 2 - 150, $h / 2 + 60, $sub, $normalFont, 16, $watermarkColor, 0, 0, -38);
    
    // 2. Page Number at Bottom Right
    $footerColor = array(0.4, 0.45, 0.5);
    $pageText = "Page {PAGE_NUM} of {PAGE_COUNT}";
    $pageWidth = $fontMetrics->getTextWidth($pageText, $normalFont, 8);
    $pdf->page_text($w - 15 * 2.83465 - $pageWidth, $h - 10 * 2.83465, $pageText, $normalFont, 8, $footerColor);
}
</script>
</body>
</html>
';

$dompdf->loadHtml($html);
$dompdf->setPaper('a4', 'portrait');
$dompdf->render();

$canvas = $dompdf->getCanvas();
echo "Canvas total pages: " . $canvas->get_page_count() . "\n";
file_put_contents(__DIR__ . '/test_canvas_output.pdf', $dompdf->output());
echo "Saved test_canvas_output.pdf (" . filesize(__DIR__ . '/test_canvas_output.pdf') . " bytes)\n";
