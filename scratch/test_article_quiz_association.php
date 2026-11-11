<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Article;
use App\Models\Quiz;
use App\Models\User;

echo "=== 1. VERIFYING ARTICLE & QUIZ ASSOCIATION ===\n";
$article = Article::with('quiz.questions.options')->where('slug', '18-sept-upi-update-case-system')->first();
assert($article !== null, 'Article 18-sept-upi-update-case-system must exist');
assert($article->quiz !== null, 'Article must have an associated quiz');
echo "Article ID: {$article->id} - {$article->title}\n";
echo "Associated Quiz ID: {$article->quiz->id} - {$article->quiz->title}\n";
echo "Total Questions: " . $article->quiz->questions->count() . "\n";
assert($article->quiz->questions->count() >= 5, 'Quiz must have at least 5 questions');

echo "\n=== 2. VERIFYING PUBLIC ARTICLE DETAIL VIEW RENDERING ===\n";
$lang = App\Models\Language::find($article->language_id);
$twin = $article->translationTwin();
$prevArticle = null;
$nextArticle = null;
$relatedArticles = collect();
$jsonLd = App\Services\SeoService::getArticleSchema($article);
$shareLinks = ['whatsapp' => '#', 'telegram' => '#', 'facebook' => '#', 'twitter' => '#', 'linkedin' => '#'];
$locale = 'en';
$studentQuizAttempt = null;

$viewHtml = view('public.articles.show', compact(
    'article',
    'twin',
    'prevArticle',
    'nextArticle',
    'relatedArticles',
    'jsonLd',
    'shareLinks',
    'locale',
    'studentQuizAttempt'
))->render();

assert(str_contains($viewHtml, 'Associated Editorial Prelims Test'), 'View must contain Associated Editorial Prelims Test banner');
assert(str_contains($viewHtml, e($article->quiz->title)), 'View must display quiz title');
assert(str_contains($viewHtml, 'Instant Practice Preview (Sample Questions)'), 'View must contain practice questions preview');
assert(str_contains($viewHtml, 'With reference to the Unified Payments Interface (UPI) in India'), 'View must show UPI question');
echo "Public view rendered successfully with all quiz components! (" . strlen($viewHtml) . " bytes)\n";

echo "\n=== 3. VERIFYING STUDENT QUIZ ATTEMPT INITIATION ===\n";
$student = User::first();
echo "Attempting with User: {$student->name} (ID: {$student->id})\n";
$scoringService = app(App\Services\QuizScoringService::class);
$attempt = $scoringService->startAttempt($student, $article->quiz);
echo "Attempt ID: {$attempt->id}, Status: {$attempt->status}, User: {$student->name} ({$student->email})\n";
assert($attempt->quiz_id === $article->quiz->id, 'Attempt must match article quiz ID');

echo "\n=== 4. VERIFYING ADMIN ARTICLE EDIT VIEW WITH QUIZZES ===\n";
$languages = App\Models\Language::all();
$contentTypes = App\Models\ContentType::all();
$sections = App\Models\Section::all();
$quizzes = Quiz::all();

auth()->login($student);
$errors = new \Illuminate\Support\ViewErrorBag();
$adminEditHtml = view('admin.articles.edit', compact('article', 'languages', 'contentTypes', 'sections', 'quizzes', 'errors'))->render();
assert(str_contains($adminEditHtml, 'Associated Practice Quiz / Prelims Test for Students'), 'Admin edit must include quiz selector');
assert(str_contains($adminEditHtml, 'name="quiz_id"'), 'Admin edit must have quiz_id select element');
echo "Admin edit view rendered successfully! (" . strlen($adminEditHtml) . " bytes)\n";

echo "\n ALL TESTS PASSED! Article quiz association is fully functional and robust.\n";
