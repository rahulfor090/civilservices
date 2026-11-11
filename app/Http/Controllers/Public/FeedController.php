<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Language;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function rss(?string $sectionSlug = null): Response
    {
        $locale = app()->getLocale();
        $lang = Language::where('code', $locale)->firstOrFail();

        $query = Article::published()
            ->where('language_id', $lang->id)
            ->latest('published_at');

        if ($sectionSlug) {
            $query->whereHas('contentType', fn($q) => $q->where('code', $sectionSlug)->orWhere('url_prefix', $sectionSlug));
        }

        $articles = $query->take(25)->get();

        $channelTitle = $locale === 'hi' ? 'सिविल सेवा गुरुकुल - आरएसएस फ़ीड' : 'Civil Service Gurukul - Official RSS Feed';
        $channelDesc = $locale === 'hi' ? 'दैनिक समाचार विश्लेषण, संपादकीय और यूपीएससी सिविल सेवा परीक्षा अपडेट' : 'Daily News Analysis, Editorials, and UPSC CSE Preparation Updates';
        $channelLang = $locale === 'hi' ? 'hi-IN' : 'en-US';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . PHP_EOL;
        $xml .= '  <channel>' . PHP_EOL;
        $xml .= '    <title>' . htmlspecialchars($channelTitle) . '</title>' . PHP_EOL;
        $xml .= '    <link>' . url($locale === 'hi' ? '/hindi' : '/') . '</link>' . PHP_EOL;
        $xml .= '    <description>' . htmlspecialchars($channelDesc) . '</description>' . PHP_EOL;
        $xml .= '    <language>' . $channelLang . '</language>' . PHP_EOL;
        $xml .= '    <atom:link href="' . url()->current() . '" rel="self" type="application/rss+xml" />' . PHP_EOL;

        foreach ($articles as $article) {
            $xml .= '    <item>' . PHP_EOL;
            $xml .= '      <title>' . htmlspecialchars($article->title) . '</title>' . PHP_EOL;
            $xml .= '      <link>' . $article->url . '</link>' . PHP_EOL;
            $xml .= '      <guid isPermaLink="true">' . $article->url . '</guid>' . PHP_EOL;
            $xml .= '      <description>' . htmlspecialchars($article->excerpt ?? '') . '</description>' . PHP_EOL;
            $xml .= '      <pubDate>' . ($article->published_at ? $article->published_at->toRssString() : now()->toRssString()) . '</pubDate>' . PHP_EOL;
            $xml .= '    </item>' . PHP_EOL;
        }

        $xml .= '  </channel>' . PHP_EOL;
        $xml .= '</rss>';

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }
}
