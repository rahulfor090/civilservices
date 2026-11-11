<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\AuditLog;
use App\Models\ContentType;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $totalArticles = Article::count();
        $publishedToday = Article::published()->whereDate('published_at', today())->count();
        $draftArticles = Article::where('status', 'draft')->count();
        $totalLeads = Lead::count();
        $newLeadsToday = Lead::whereDate('created_at', today())->count();
        $registeredStudents = User::count();

        // Translation coverage
        $enCount = Article::whereHas('language', fn($q) => $q->where('code', 'en'))->count();
        $hiCount = Article::whereHas('language', fn($q) => $q->where('code', 'hi'))->count();
        $translationCoverage = $enCount > 0 ? round(($hiCount / $enCount) * 100, 1) : 100;

        // Recent content
        $recentArticles = Article::with(['language', 'contentType', 'author'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        // Recent Audit logs
        $recentAudits = AuditLog::with('user')
            ->latest('id')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalArticles',
            'publishedToday',
            'draftArticles',
            'totalLeads',
            'newLeadsToday',
            'registeredStudents',
            'translationCoverage',
            'recentArticles',
            'recentAudits'
        ));
    }
}
