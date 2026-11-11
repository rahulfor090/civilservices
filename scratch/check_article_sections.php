<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Article;
use App\Models\Section;
use App\Models\SectionTranslation;

echo "Articles using section_id:\n";
foreach (Article::whereNotNull('section_id')->get(['id', 'title', 'section_id']) as $a) {
    echo "Article #{$a->id} -> Section #{$a->section_id}\n";
}
