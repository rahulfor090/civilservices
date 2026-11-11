<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$a = App\Models\Article::with(['blocks', 'sections'])->where('slug', '18-sept-upi-update-case-system')->first();
if (!$a) {
    echo "Article 42 not found\n";
    exit(1);
}

echo "Article ID: " . $a->id . "\n";
echo "Title: " . $a->title . "\n";
echo "Slug: " . $a->slug . "\n";
echo "Excerpt: " . $a->excerpt . "\n";
echo "Sections count: " . $a->sections->count() . "\n";
echo "Blocks count: " . $a->blocks->count() . "\n";
foreach ($a->blocks as $b) {
    echo " - Block: {$b->type}\n";
    echo "   Payload: " . json_encode($b->payload_json) . "\n";
}
echo "Body HTML preview:\n" . substr($a->body_html ?? '', 0, 300) . "\n";

echo "\nColumns on articles table:\n";
$columns = Illuminate\Support\Facades\Schema::getColumnListing('articles');
echo implode(', ', $columns) . "\n";
