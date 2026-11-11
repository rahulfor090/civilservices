<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

// Test article
$article = App\Models\Article::first();

// Test render view
$html = view('public.articles.pdf', compact('article'))->render();
echo "Rendered HTML length: " . strlen($html) . "\n";

$pdf = Pdf::loadHTML($html);
$pdf->setPaper('a4', 'portrait');
$pdf->setOptions([
    'isHtml5ParserEnabled' => true,
    'isRemoteEnabled' => true,
    'isPhpEnabled' => true,
    'defaultFont' => 'DejaVu Sans',
    'dpi' => 150,
]);

$outputPath = __DIR__ . '/test_output.pdf';
$pdf->save($outputPath);
echo "Saved PDF to " . $outputPath . " (size: " . filesize($outputPath) . " bytes)\n";
