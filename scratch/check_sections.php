<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$sections = App\Models\Section::with(['translations', 'contentType'])->get();
foreach ($sections as $s) {
    $enName = $s->translations->firstWhere('language_id', 1)?->name ?? $s->translations->first()?->name ?? 'N/A';
    echo "ID: {$s->id} | ContentType: " . ($s->contentType?->code ?? 'null') . " | Name: {$enName}\n";
}
