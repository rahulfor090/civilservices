<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "MENUS:\n";
foreach (\App\Models\NavigationMenu::all() as $m) {
    echo "ID: {$m->id}, Code: {$m->code}, Name: {$m->name}, Lang: {$m->language_id}\n";
    $items = \App\Models\NavigationItem::where('menu_id', $m->id)->get();
    echo "  Total items for menu: " . $items->count() . "\n";
    foreach ($items->whereNull('parent_id') as $parent) {
        echo "   - Parent ID {$parent->id}: '{$parent->label}', sort: {$parent->sort_order}, children: " . $items->where('parent_id', $parent->id)->count() . "\n";
    }
}
