<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$html = view('components.mega-menu')->render();

// Check occurrence count of column headings
$prelimsCount = substr_count($html, 'Prelims');
$mainsCount = substr_count($html, 'Mains');
$gurukulSpecialsCount = substr_count($html, 'Gurukul Specials');
$examprepCount = substr_count($html, 'ExamPrep IAS Specials');

echo "ExamPrep IAS Specials occurrences: {$examprepCount} (must be 0)\n";
echo "Gurukul Specials occurrences in English menu: {$gurukulSpecialsCount} (must be 1)\n";

assert($examprepCount === 0, "ExamPrep IAS Specials should not appear in mega menu");
assert($gurukulSpecialsCount === 1, "Gurukul Specials should appear exactly once in mega menu");

echo "\nMega menu verification passed with flying colors!\n";
