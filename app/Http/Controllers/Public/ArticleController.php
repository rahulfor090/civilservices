<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ContentType;
use App\Models\Language;
use App\Services\SeoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleController extends Controller
{
    /**
     * Display a paginated listing of articles for a given content type prefix
     */
    public function index(Request $request, string $typePrefix = 'daily-updates/daily-news-analysis'): View
    {
        $locale = app()->getLocale();
        $lang = Language::where('code', $locale)->firstOrFail();

        // Match content type by url_prefix or code
        $cleanPrefix = trim($typePrefix, '/');
        $contentType = ContentType::where('url_prefix', $cleanPrefix)
            ->orWhere('code', $cleanPrefix)
            ->first();

        $query = Article::published()
            ->where('language_id', $lang->id)
            ->with(['section.translations', 'tags', 'author']);

        if ($contentType) {
            $query->where('content_type_id', $contentType->id);
        }

        // Year & Month archive filter
        $selectedYear = $request->query('year');
        $selectedMonth = $request->query('month');
        $selectedDate = $request->query('date');

        if ($selectedYear) {
            $query->whereYear('published_at', (int) $selectedYear);
        }
        if ($selectedMonth) {
            $query->whereMonth('published_at', (int) $selectedMonth);
        }
        if ($selectedDate) {
            $query->whereDate('published_at', $selectedDate);
        }

        // Sorting
        $sortField = $request->query('sort', 'published_at');
        $sortDirection = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (in_array($sortField, ['published_at', 'title', 'view_count', 'sort_order'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest('published_at');
        }

        $articles = $query->paginate(20)->withQueryString();

        // Generate calendar matrix for current/selected month with real article count links
        $calendarDate = Carbon::create(
            (int) ($selectedYear ?: now()->year),
            (int) ($selectedMonth ?: now()->month),
            1
        );

        $daysInMonth = $calendarDate->daysInMonth;
        $startDayOfWeek = $calendarDate->copy()->startOfMonth()->dayOfWeek; // 0 (Sun) to 6 (Sat)

        // Count articles per day in this month
        $dailyCounts = Article::published()
            ->where('language_id', $lang->id)
            ->when($contentType, fn($q) => $q->where('content_type_id', $contentType->id))
            ->whereYear('published_at', $calendarDate->year)
            ->whereMonth('published_at', $calendarDate->month)
            ->selectRaw('DAY(published_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        return view('public.articles.index', compact(
            'articles',
            'contentType',
            'cleanPrefix',
            'locale',
            'selectedYear',
            'selectedMonth',
            'selectedDate',
            'calendarDate',
            'daysInMonth',
            'startDayOfWeek',
            'dailyCounts'
        ));
    }

    /**
     * Display a specific article detail page
     */
    public function show(Request $request, ?string $param1 = null, ?string $param2 = null): View|RedirectResponse
    {
        $locale = app()->getLocale();
        $lang = Language::where('code', $locale)->firstOrFail();

        $slug = $request->route('slug') ?? $param2 ?? $param1;

        $article = Article::where('slug', $slug)
            ->where('language_id', $lang->id)
            ->with(['sections', 'blocks', 'tags', 'author', 'contentType', 'section.translations', 'quiz.questions.options', 'quiz.category'])
            ->first();

        if (!$article) {
            // Check if article exists under another language or locale and redirect gracefully
            $altArticle = Article::where('slug', $slug)->first();
            if ($altArticle && $altArticle->url) {
                return redirect($altArticle->url, 301);
            }

            // Check if there is an active 301 redirect rule
            $path = '/' . trim($request->path(), '/');
            $redirect = \App\Models\Redirect::where('from_path', $path)->where('is_active', true)->first();
            if ($redirect) {
                $redirect->increment('hits');
                return redirect($redirect->to_path, $redirect->status_code);
            }

            abort(404, 'Article not found');
        }

        // Preview protection for unpublished content
        if ($article->status !== 'published') {
            $token = $request->query('preview_token');
            if (!$token || $token !== md5($article->id . 'CIVIL_SERVICE_PREVIEW_SALT')) {
                abort(404);
            }
        }

        // Safe increment of views
        $article->increment('view_count');

        // Check if student has attempted the associated quiz
        $studentQuizAttempt = null;
        if (auth()->check() && $article->quiz) {
            $studentQuizAttempt = $article->quiz->attempts()
                ->where('user_id', auth()->id())
                ->latest('id')
                ->first();
        }

        // Bilingual pair (reciprocal translation twin)
        $twin = $article->translationTwin();

        // Previous and Next articles in the same content type & language
        $prevArticle = Article::published()
            ->where('content_type_id', $article->content_type_id)
            ->where('language_id', $article->language_id)
            ->where('published_at', '<', $article->published_at)
            ->latest('published_at')
            ->first();

        $nextArticle = Article::published()
            ->where('content_type_id', $article->content_type_id)
            ->where('language_id', $article->language_id)
            ->where('published_at', '>', $article->published_at)
            ->oldest('published_at')
            ->first();

        // Related articles by section or tag
        $relatedArticles = Article::published()
            ->where('language_id', $article->language_id)
            ->where('content_type_id', $article->content_type_id)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(4)
            ->get();

        // Structured Data Schema
        $jsonLd = SeoService::getArticleSchema($article);

        // Share links
        $encodedUrl = urlencode($article->url);
        $encodedTitle = urlencode($article->title);
        $shareLinks = [
            'whatsapp' => "https://api.whatsapp.com/send?text={$encodedTitle}%20{$encodedUrl}",
            'telegram' => "https://t.me/share/url?url={$encodedUrl}&text={$encodedTitle}",
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
            'twitter' => "https://twitter.com/intent/tweet?text={$encodedTitle}&url={$encodedUrl}",
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}",
        ];

        return view('public.articles.show', compact(
            'article',
            'twin',
            'prevArticle',
            'nextArticle',
            'relatedArticles',
            'jsonLd',
            'shareLinks',
            'locale',
            'studentQuizAttempt'
        ));
    }

    /**
     * Download or print/stream article PDF with Civil Services Gurukul watermark
     */
    public function pdf(Request $request, ?string $param1 = null, ?string $param2 = null)
    {
        $slug = $request->route('slug') ?? $param2 ?? $param1;

        $article = Article::where('slug', $slug)
            ->with(['sections', 'blocks', 'tags', 'author', 'contentType', 'section.translations', 'quiz.questions.options', 'pdfMedia'])
            ->first();

        if (!$article) {
            abort(404, 'Article not found');
        }

        // 1. If an official PDF was uploaded from admin, prioritize and serve that PDF
        if (!$request->boolean('force_generate')) {
            if ($article->pdfMedia && Storage::disk($article->pdfMedia->disk)->exists($article->pdfMedia->path)) {
                $filePath = Storage::disk($article->pdfMedia->disk)->path($article->pdfMedia->path);
                $downloadName = Str::slug($article->title) . '-civilservicegurukul.pdf';
                return response()->download($filePath, $downloadName);
            }

            if ($article->pdf_path) {
                // If stored in public storage directory
                if (Storage::disk('public')->exists($article->pdf_path)) {
                    $filePath = Storage::disk('public')->path($article->pdf_path);
                    return response()->download($filePath, Str::slug($article->title) . '-civilservicegurukul.pdf');
                }

                // If stored as relative media or public asset
                $publicPath = public_path(ltrim($article->pdf_path, '/'));
                if (file_exists($publicPath)) {
                    return response()->download($publicPath, Str::slug($article->title) . '-civilservicegurukul.pdf');
                }

                // If URL
                if (preg_match('#^https?://#i', $article->pdf_path)) {
                    return redirect($article->pdf_path);
                }
            }

            if ($article->uploaded_pdf_url) {
                return redirect($article->uploaded_pdf_url);
            }
        }

        // 2. Otherwise: Dynamically generate PDF with the Civil Services Gurukul watermark
        $pdf = Pdf::loadView('public.articles.pdf', compact('article'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 150,
        ]);

        $fileName = Str::slug($article->title) . '-civilservicegurukul.pdf';

        if ($request->query('action') === 'stream' || $request->query('action') === 'print' || $request->boolean('preview')) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }
}
