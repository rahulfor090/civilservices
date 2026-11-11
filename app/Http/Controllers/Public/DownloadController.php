<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\DownloadCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DownloadController extends Controller
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $lang = Language::where('code', $locale)->firstOrFail();

        $categories = DownloadCategory::where('language_id', $lang->id)
            ->where('is_active', true)
            ->with(['downloads' => fn($q) => $q->where('is_active', true)->latest()])
            ->get();

        $recentDownloads = Download::where('language_id', $lang->id)
            ->where('is_active', true)
            ->latest()
            ->take(12)
            ->get();

        return view('public.downloads.index', compact('categories', 'recentDownloads', 'locale'));
    }

    public function category(Request $request, string $categorySlug): View
    {
        $locale = app()->getLocale();
        $lang = Language::where('code', $locale)->firstOrFail();

        $category = DownloadCategory::where('slug', $categorySlug)
            ->where('language_id', $lang->id)
            ->with(['downloads' => fn($q) => $q->where('is_active', true)->latest()])
            ->firstOrFail();

        return view('public.downloads.category', compact('category', 'locale'));
    }
}
