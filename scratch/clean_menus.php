<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NavigationMenu;
use App\Models\NavigationItem;
use Illuminate\Support\Facades\Cache;

echo "=== NAVIGATION MENUS ===\n";
foreach (NavigationMenu::all() as $m) {
    echo "Menu #{$m->id}: code='{$m->code}', name='{$m->name}', lang_id={$m->language_id}\n";
    $parents = NavigationItem::where('menu_id', $m->id)->whereNull('parent_id')->get();
    echo "  Total parent columns: " . $parents->count() . "\n";
    foreach ($parents as $p) {
        $children = NavigationItem::where('parent_id', $p->id)->get();
        echo "    Parent #{$p->id}: '{$p->label}' (children: " . $children->count() . ")\n";
        foreach ($children as $c) {
            echo "      - Item #{$c->id}: '{$c->label}' -> '{$c->url}'\n";
        }
    }
}
