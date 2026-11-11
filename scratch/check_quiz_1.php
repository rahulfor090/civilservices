<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$q = App\Models\Quiz::with('questions.options')->find(1);
echo "Quiz: {$q->title}\n";
echo "Total questions: " . $q->questions->count() . "\n";
foreach ($q->questions->take(3) as $question) {
    echo "\nQ{$question->id}: {$question->stem}\n";
    foreach ($question->options as $opt) {
        echo "  [" . ($opt->is_correct ? 'X' : ' ') . "] {$opt->option_text}\n";
    }
    echo "  Explanation: " . substr($question->explanation ?? '', 0, 100) . "...\n";
}
