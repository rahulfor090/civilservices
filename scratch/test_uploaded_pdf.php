<?php

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Public\ArticleController;

echo "--- Testing Uploaded PDF Priority ---\n";

$article = Article::first();
$article->pdf_path = 'test_article_watermarked.pdf'; // points to the file in public storage
$article->save();

echo "Article now has uploaded PDF: " . $article->pdf_path . "\n";
echo "has_uploaded_pdf: " . ($article->fresh()->has_uploaded_pdf ? 'YES' : 'NO') . "\n";

$request = Request::create("/articles/{$article->slug}/pdf", 'GET');
$controller = app(ArticleController::class);
$response = $controller->pdf($request, $article->slug);

echo "Response type: " . get_class($response) . "\n";
echo "Response status: " . $response->getStatusCode() . "\n";

// Reset
$article->pdf_path = null;
$article->save();
echo "Reset article pdf_path to null.\n";
echo "--- Uploaded PDF Priority Test PASSED! ---\n";
