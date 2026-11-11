<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$articles = App\Models\Article::where('title', 'like', '%Sport%')
    ->orWhere('title', 'like', '%Bindra%')
    ->orWhere('body_html', 'like', '%Bindra%')
    ->get();

if ($articles->isEmpty()) {
    echo "No matching article found. Latest 5 articles:\n";
    foreach (App\Models\Article::latest()->take(5)->get() as $a) {
        echo $a->id . ': ' . $a->title . "\n";
    }
    exit;
}

foreach ($articles as $article) {
    echo "FOUND: " . $article->id . ': ' . $article->title . "\n";
}
$article = $articles->first();

echo "ID: " . $article->id . "\n";
echo "Title: " . $article->title . "\n";
echo "Sections count: " . $article->sections->count() . "\n";
echo "--- FIRST 800 CHARS OF BODY_HTML ---\n";
echo substr($article->body_html ?? $article->content ?? '', 0, 800) . "\n";
echo "--- LAST 500 CHARS OF BODY_HTML ---\n";
echo substr($article->body_html ?? $article->content ?? '', -500) . "\n";
