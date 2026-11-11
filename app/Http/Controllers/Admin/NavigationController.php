<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Language;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class NavigationController extends Controller
{
    /**
     * Display navigation menus and items for mega menu and top navigation
     */
    public function index(Request $request): View
    {
        $languages = Language::where('is_active', true)->get();
        $selectedLangId = $request->query('language_id', $languages->first()?->id);

        // Fetch or create mega_menu for selected language
        $menu = NavigationMenu::firstOrCreate(
            ['code' => 'mega_menu', 'language_id' => $selectedLangId],
            ['name' => 'Mega Menu (' . ($languages->firstWhere('id', $selectedLangId)?->name ?? 'Default') . ')', 'is_active' => true]
        );

        $columns = NavigationItem::where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->with(['children' => fn($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('admin.navigation.index', compact('menu', 'languages', 'selectedLangId', 'columns'));
    }

    /**
     * Store a new navigation column or link item
     */
    public function storeItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:navigation_menus,id',
            'parent_id' => 'nullable|exists:navigation_items,id',
            'label' => 'required|string|max:190',
            'url' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $item = NavigationItem::create([
            'menu_id' => $validated['menu_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'label' => $validated['label'],
            'url' => $validated['url'],
            'link_type' => empty($validated['parent_id']) ? 'category' : 'internal',
            'sort_order' => $validated['sort_order'] ?? 1,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ]);

        Cache::forget('mega_menu_en');
        Cache::forget('mega_menu_hi');
        Cache::forget('top_nav_en');
        Cache::forget('top_nav_hi');

        AuditLog::record(auth()->user(), 'navigation', 'create_item', $item, null, $item->toArray());

        return back()->with('success', 'Navigation item "' . $item->label . '" created successfully.');
    }

    /**
     * Update an existing navigation item
     */
    public function updateItem(Request $request, NavigationItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:190',
            'url' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $before = $item->toArray();

        $item->update([
            'label' => $validated['label'],
            'url' => $validated['url'],
            'sort_order' => $validated['sort_order'] ?? $item->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        Cache::forget('mega_menu_en');
        Cache::forget('mega_menu_hi');
        Cache::forget('top_nav_en');
        Cache::forget('top_nav_hi');

        AuditLog::record(auth()->user(), 'navigation', 'update_item', $item, $before, $item->toArray());

        return back()->with('success', 'Navigation item "' . $item->label . '" updated.');
    }

    /**
     * Delete a navigation item and its children
     */
    public function destroyItem(NavigationItem $item): RedirectResponse
    {
        $label = $item->label;
        $before = $item->toArray();

        // Delete children if this is a parent column
        $item->children()->delete();
        $item->delete();

        Cache::forget('mega_menu_en');
        Cache::forget('mega_menu_hi');
        Cache::forget('top_nav_en');
        Cache::forget('top_nav_hi');

        AuditLog::record(auth()->user(), 'navigation', 'delete_item', null, $before, null);

        return back()->with('success', 'Navigation item "' . $label . '" and associated sub-items deleted.');
    }
}
