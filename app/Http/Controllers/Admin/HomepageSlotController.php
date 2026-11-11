<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Banner;
use App\Models\HomepageSlot;
use App\Models\HomepageSlotItem;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomepageSlotController extends Controller
{
    /**
     * Display homepage announcement tickers, promo banners, and slots
     */
    public function index(Request $request): View
    {
        $languages = Language::where('is_active', true)->get();
        $selectedLangId = $request->query('language_id', $languages->first()?->id);

        $tickerSlot = HomepageSlot::firstOrCreate(
            ['language_id' => $selectedLangId, 'key' => 'announcement_ticker'],
            ['title' => 'This Just In Ticker', 'slot_type' => 'ticker', 'sort_order' => 1, 'is_active' => true]
        );

        $tickerItems = HomepageSlotItem::where('slot_id', $tickerSlot->id)
            ->orderBy('sort_order')
            ->get();

        $promoBanner = Banner::where('language_id', $selectedLangId)
            ->where('placement', 'top_strip')
            ->first();

        return view('admin.slots.index', compact('languages', 'selectedLangId', 'tickerSlot', 'tickerItems', 'promoBanner'));
    }

    /**
     * Store a new headline item in the ticker slot
     */
    public function storeItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slot_id' => 'required|exists:homepage_slots,id',
            'headline' => 'required|string|max:255',
            'cta_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $item = HomepageSlotItem::create([
            'slot_id' => $validated['slot_id'],
            'item_type' => 'custom',
            'headline' => $validated['headline'],
            'cta_url' => $validated['cta_url'] ?? '#',
            'sort_order' => $validated['sort_order'] ?? 1,
            'is_active' => $request->boolean('is_active', true),
        ]);

        Cache::forget('announcement_ticker_en');
        Cache::forget('announcement_ticker_hi');

        AuditLog::record(auth()->user(), 'homepage_slots', 'create_item', $item, null, $item->toArray());

        return back()->with('success', 'Announcement headline added to ticker.');
    }

    /**
     * Update ticker headline item
     */
    public function updateItem(Request $request, HomepageSlotItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'headline' => 'required|string|max:255',
            'cta_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $before = $item->toArray();

        $item->update([
            'headline' => $validated['headline'],
            'cta_url' => $validated['cta_url'] ?? '#',
            'sort_order' => $validated['sort_order'] ?? $item->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        Cache::forget('announcement_ticker_en');
        Cache::forget('announcement_ticker_hi');

        AuditLog::record(auth()->user(), 'homepage_slots', 'update_item', $item, $before, $item->toArray());

        return back()->with('success', 'Announcement item updated.');
    }

    /**
     * Delete ticker headline item
     */
    public function destroyItem(HomepageSlotItem $item): RedirectResponse
    {
        $before = $item->toArray();
        $item->delete();

        Cache::forget('announcement_ticker_en');
        Cache::forget('announcement_ticker_hi');

        AuditLog::record(auth()->user(), 'homepage_slots', 'delete_item', null, $before, null);

        return back()->with('success', 'Announcement headline deleted.');
    }

    /**
     * Update promotional top strip banner
     */
    public function updateBanner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'headline' => 'required|string|max:255',
            'subheadline' => 'nullable|string|max:255',
            'cta_label' => 'nullable|string|max:100',
            'url' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $banner = Banner::updateOrCreate(
            ['language_id' => $validated['language_id'], 'placement' => 'top_strip'],
            [
                'headline' => $validated['headline'],
                'subheadline' => $validated['subheadline'] ?? '',
                'cta_label' => $validated['cta_label'] ?? 'Learn More',
                'url' => $validated['url'] ?? '#',
                'is_active' => $request->boolean('is_active', true),
            ]
        );

        Cache::forget('top_strip_banner_en');
        Cache::forget('top_strip_banner_hi');

        AuditLog::record(auth()->user(), 'homepage_slots', 'update_banner', $banner, null, $banner->toArray());

        return back()->with('success', 'Top promotional banner updated successfully.');
    }
}
