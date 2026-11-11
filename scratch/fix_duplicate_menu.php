<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NavigationMenu;
use App\Models\NavigationItem;
use Illuminate\Support\Facades\Cache;

echo "Cleaning up duplicate navigation items...\n";

// Delete old duplicate items with IDs 1 to 20
$deletedCount = NavigationItem::whereIn('id', range(1, 20))->delete();
echo "Deleted {$deletedCount} old duplicate items (IDs 1-20).\n";

// Clear mega menu cache
Cache::forget('mega_menu_en');
Cache::forget('mega_menu_hi');
\Illuminate\Support\Facades\Artisan::call('cache:clear');
\Illuminate\Support\Facades\Artisan::call('view:clear');

echo "Cache cleared!\n";

echo "\nRemaining Parent Columns for English Mega Menu:\n";
$menuEn = NavigationMenu::where('code', 'mega_menu')->where('language_id', 1)->first();
if ($menuEn) {
    $parents = NavigationItem::where('menu_id', $menuEn->id)->whereNull('parent_id')->get();
    echo "Total Parent Columns: " . $parents->count() . "\n";
    foreach ($parents as $p) {
        $children = NavigationItem::where('parent_id', $p->id)->get();
        echo " - [{$p->id}] {$p->label} ({$children->count()} links)\n";
        foreach ($children as $c) {
            echo "     * {$c->label} -> {$c->url}\n";
        }
    }
}
