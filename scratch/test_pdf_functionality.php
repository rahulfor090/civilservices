<?php

use App\Models\Article;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

echo "--- Testing Article PDF Generation ---\n";

$article = Article::with(['sections', 'blocks', 'tags', 'author', 'contentType', 'section.translations', 'quiz.questions.options'])->first();

if (!$article) {
    echo "No article found in database!\n";
    exit(1);
}

echo "Found Article: [ID: {$article->id}] {$article->title}\n";
echo "Has uploaded PDF? " . ($article->has_uploaded_pdf ? 'YES' : 'NO') . "\n";

// Test PDF generation with watermark
try {
    $pdf = Pdf::loadView('public.articles.pdf', compact('article'));
    $pdf->setPaper('a4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'DejaVu Sans',
    ]);
    
    $output = $pdf->output();
    $pdfSize = strlen($output);
    echo "Generated PDF successfully! Size: {$pdfSize} bytes\n";
    
    // Save to scratch for verification
    $tempPath = storage_path('app/public/test_article_watermarked.pdf');
    file_put_contents($tempPath, $output);
    echo "Saved sample PDF to: {$tempPath}\n";
} catch (\Throwable $e) {
    echo "ERROR generating PDF: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

// Test URL route resolution
try {
    $pdfRoute = route('articles.pdf', ['slug' => $article->slug]);
    echo "PDF Route URL: {$pdfRoute}\n";
} catch (\Throwable $e) {
    echo "ERROR resolving route: " . $e->getMessage() . "\n";
}

echo "--- PDF Generation Test Completed Successfully! ---\n";
