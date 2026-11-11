<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Article;
use App\Models\Section;
use App\Models\SectionTranslation;

echo "=== Deduplicating Sections ===\n";
// Keep the first 4 sections (1, 2, 3, 4) for GS-1, GS-2, GS-3, GS-4
// Remap any articles pointing to duplicate sections
// 5, 9 -> 1
// 6, 10 -> 2
// 7, 11 -> 3
// 8, 12 -> 4

$remap = [
    5 => 1, 9 => 1,
    6 => 2, 10 => 2,
    7 => 3, 11 => 3,
    8 => 4, 12 => 4,
];

foreach ($remap as $oldId => $newId) {
    Article::where('section_id', $oldId)->update(['section_id' => $newId]);
}

// Delete sections > 4 if they exist
$deleted = Section::where('id', '>', 4)->delete();
echo "Sections deleted: $deleted\n";

$remaining = Section::with('translations')->get();
echo "Remaining sections count: " . $remaining->count() . "\n";
foreach ($remaining as $s) {
    echo "ID: {$s->id} | Code: {$s->code} | Name: {$s->name}\n";
}
