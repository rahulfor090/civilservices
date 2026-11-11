<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Centre;
use App\Models\HomepageSlot;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $lang = Language::where('code', $locale)->first() ?? Language::where('code', 'en')->first();

        // Top Promo Banner
        $topBanner = Banner::where('placement', 'top_strip')
            ->where('language_id', $lang->id)
            ->where('is_active', true)
            ->first();

        // Announcement Ticker items (matches top of RSS)
        $tickerSlot = HomepageSlot::where('key', 'announcement_ticker')
            ->where('language_id', $lang->id)
            ->where('is_active', true)
            ->with('items')
            ->first();

        $tickerItems = $tickerSlot ? $tickerSlot->items : collect();

        // Recent Updates for English and Hindi tabs
        $recentUpdatesEn = Article::published()
            ->forLocale('en')
            ->latest('published_at')
            ->take(8)
            ->get();

        $recentUpdatesHi = Article::published()
            ->forLocale('hi')
            ->latest('published_at')
            ->take(8)
            ->get();

        // Featured Hero / Daily Editorials
        $featuredArticles = Article::published()
            ->where('language_id', $lang->id)
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        $hqCentre = Centre::headquarters();
        $activeCentresCount = Centre::activeOrdered()->count();

        return view('public.home', compact(
            'locale',
            'topBanner',
            'tickerItems',
            'recentUpdatesEn',
            'recentUpdatesHi',
            'featuredArticles',
            'hqCentre',
            'activeCentresCount'
        ));
    }
}
