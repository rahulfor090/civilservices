<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$a = App\Models\Article::where('slug', '18-sept-upi-update-case-system')->first();
echo "ARTICLE:\n";
if ($a) {
    echo "ID: {$a->id}\n";
    echo "Title: {$a->title}\n";
    echo "Slug: {$a->slug}\n";
    echo "Status: {$a->status}\n";
    echo "Language: {$a->language_id}\n";
    echo "Content Type: {$a->content_type_id}\n";
} else {
    echo "NOT FOUND\n";
    // Check similar
    $similar = App\Models\Article::where('slug', 'like', '%upi%')->orWhere('title', 'like', '%upi%')->get();
    echo "Similar articles count: " . $similar->count() . "\n";
    foreach ($similar as $s) {
        echo " - ID {$s->id}: {$s->slug} | {$s->title}\n";
    }
}

echo "\nALL QUIZZES:\n";
foreach (App\Models\Quiz::all() as $q) {
    echo "Quiz #{$q->id}: {$q->title} (slug: {$q->slug}, questions: {$q->questions()->count()})\n";
}
