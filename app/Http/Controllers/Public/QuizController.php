<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Quiz;
use App\Models\QuizCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $lang = Language::where('code', $locale)->first() ?? Language::where('code', 'en')->first();

        $query = Quiz::published()
            ->where('language_id', $lang->id)
            ->with(['category', 'questions'])
            ->latest('published_at');

        if ($categorySlug = $request->query('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
        }

        $quizzes = $query->paginate(12)->withQueryString();
        $categories = QuizCategory::where('language_id', $lang->id)->where('is_active', true)->get();

        return view('public.quizzes.index', compact('quizzes', 'categories', 'locale'));
    }

    public function show(Request $request, string $slug): View
    {
        $locale = app()->getLocale();
        $lang = Language::where('code', $locale)->first() ?? Language::where('code', 'en')->first();

        $quiz = Quiz::where('slug', $slug)
            ->where('language_id', $lang->id)
            ->with(['category', 'questions.options'])
            ->firstOrFail();

        // Check if user already completed this quiz
        $userAttempt = auth()->check() 
            ? $quiz->attempts()->where('user_id', auth()->id())->latest('id')->first() 
            : null;

        return view('public.quizzes.show', compact('quiz', 'userAttempt', 'locale'));
    }
}
