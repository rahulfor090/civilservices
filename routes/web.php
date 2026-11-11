<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\CentreController as AdminCentreController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CrmController as AdminCrmController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HomepageSlotController as AdminHomepageSlotController;
use App\Http\Controllers\Admin\MainsEvaluationController as AdminMainsEvaluationController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\NavigationController as AdminNavigationController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\CentreController;
use App\Http\Controllers\Public\CounsellingController;
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Public\DownloadController;
use App\Http\Controllers\Public\FeedController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\InterviewGuidanceController;
use App\Http\Controllers\Public\LeadController;
use App\Http\Controllers\Public\QuizController;
use App\Http\Controllers\Public\RecruitmentController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\MainsSubmissionController;
use App\Http\Controllers\Student\QuizAttemptController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (English Locale - Default)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// Lead Capture (AJAX & Form POST with Honeypot & Attribution)
Route::post('/leads/capture', [LeadController::class, 'store'])->name('leads.submit');

// RSS Feeds
Route::get('/feed/rss', [FeedController::class, 'rss'])->name('feed.rss');
Route::get('/feed/rss/{section}', [FeedController::class, 'rss'])->name('feed.rss.section');

// Downloads
Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
Route::get('/downloads/{category}', [DownloadController::class, 'category'])->name('downloads.category');
Route::get('/free-downloads', [DownloadController::class, 'index'])->name('downloads.free');
Route::get('/free-downloads/{category}', [DownloadController::class, 'category']);

// Content Types Top-level Routes (English)
Route::get('/mind-map', [ArticleController::class, 'index'])->defaults('typePrefix', 'mind-map')->name('articles.mindmap');
Route::get('/mind-map/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'mind-map');

Route::get('/state-pcs-current-affairs', [ArticleController::class, 'index'])->defaults('typePrefix', 'state-pcs-current-affairs')->name('articles.statepcs');
Route::get('/state-pcs-current-affairs/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'state-pcs-current-affairs');

Route::get('/daily-news-editorials', [ArticleController::class, 'index'])->defaults('typePrefix', 'daily-news-editorials')->name('articles.editorials');
Route::get('/daily-news-editorials/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'daily-news-editorials');

Route::get('/pib-current-affairs', [ArticleController::class, 'index'])->defaults('typePrefix', 'pib-current-affairs')->name('articles.pib');
Route::get('/pib-current-affairs/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'pib-current-affairs');

Route::get('/loksabha-rajyasabha-discussions', [ArticleController::class, 'index'])->defaults('typePrefix', 'loksabha-rajyasabha-discussions')->name('articles.sansadtv');
Route::get('/loksabha-rajyasabha-discussions/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'loksabha-rajyasabha-discussions');

Route::get('/summary-of-important-reports', [ArticleController::class, 'index'])->defaults('typePrefix', 'summary-of-important-reports')->name('articles.reports');
Route::get('/summary-of-important-reports/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'summary-of-important-reports');

Route::get('/to-the-points', [ArticleController::class, 'index'])->defaults('typePrefix', 'to-the-points')->name('articles.tothepoint');
Route::get('/to-the-points/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'to-the-points');

Route::get('/ethics/case-studies', [ArticleController::class, 'index'])->defaults('typePrefix', 'ethics/case-studies')->name('articles.ethics');
Route::get('/ethics/case-studies/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'ethics/case-studies');

// Article Detail & PDF Download
Route::get('/daily-updates/{type}/{slug}/pdf', [ArticleController::class, 'pdf'])
    ->where('type', '[a-zA-Z0-9\-_]+')
    ->where('slug', '[a-zA-Z0-9\-_]+')
    ->name('articles.type.pdf');

Route::get('/articles/{slug}/pdf', [ArticleController::class, 'pdf'])
    ->where('slug', '[a-zA-Z0-9\-_]+')
    ->name('articles.pdf');

Route::get('/article/{slug}/pdf', [ArticleController::class, 'pdf'])
    ->where('slug', '[a-zA-Z0-9\-_]+')
    ->name('articles.direct.pdf');

Route::get('/daily-updates/{type}/{slug}', [ArticleController::class, 'show'])
    ->where('type', '[a-zA-Z0-9\-_]+')
    ->where('slug', '[a-zA-Z0-9\-_]+')
    ->name('articles.show');

// Article Listings (Daily News Analysis, Editorials, Prelims, Mains, etc.)
Route::get('/daily-updates/{type?}', [ArticleController::class, 'index'])
    ->where('type', '[a-zA-Z0-9\-_]+')
    ->name('articles.index');

// Quizzes & Mock Tests (English)
Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
Route::get('/quizzes/{slug}', [QuizController::class, 'show'])->name('quizzes.show');

// Academic Courses & Classroom Batches (English)
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [PublicCourseController::class, 'show'])->name('courses.public.show');
Route::get('/courses/{course}/planner.ics', [PublicCourseController::class, 'downloadCalendar'])->name('courses.calendar.ics');
Route::post('/courses/{course}/alert-reminder', [PublicCourseController::class, 'subscribeAlerts'])->name('courses.alerts.subscribe');
Route::post('/courses/{course}/enroll', [PublicCourseController::class, 'enroll'])->name('courses.enroll');

// 11 National Centres (English)
Route::get('/centres', [CentreController::class, 'index'])->name('centres.index');
Route::get('/centres/{slug}', [CentreController::class, 'show'])->name('centres.show');

// Admissions & Counselling (English)
Route::get('/book-counselling', [CounsellingController::class, 'create'])->name('counselling.create');
Route::post('/book-counselling', [CounsellingController::class, 'store'])->name('counselling.store');

// Interview Guidance Programme (IGP & DAF Mock)
Route::get('/interview-guidance', [InterviewGuidanceController::class, 'create'])->name('interview.create');
Route::post('/interview-guidance', [InterviewGuidanceController::class, 'store'])->name('interview.store');

// Talent Recruitment (Faculty & Content Writers)
Route::get('/teacher-registration', [RecruitmentController::class, 'teacher'])->name('recruitment.teacher');
Route::post('/teacher-registration', [RecruitmentController::class, 'storeTeacher'])->name('recruitment.teacher.store');
Route::get('/content-writer-registration', [RecruitmentController::class, 'writer'])->name('recruitment.writer');
Route::post('/content-writer-registration', [RecruitmentController::class, 'storeWriter'])->name('recruitment.writer.store');

// Direct article slug lookup (fallback)
Route::get('/article/{slug}', [ArticleController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-_]+')
    ->name('articles.direct');

/*
|--------------------------------------------------------------------------
| Public Routes (Hindi Locale - /hindi prefix)
|--------------------------------------------------------------------------
*/
Route::prefix('hindi')->name('hindi.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // RSS Feeds (Hindi)
    Route::get('/feed/rss', [FeedController::class, 'rss'])->name('feed.rss');
    Route::get('/feed/rss/{section}', [FeedController::class, 'rss'])->name('feed.rss.section');

    // Downloads (Hindi)
    Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
    Route::get('/downloads/{category}', [DownloadController::class, 'category'])->name('downloads.category');
    Route::get('/free-downloads', [DownloadController::class, 'index'])->name('downloads.free');
    Route::get('/free-downloads/{category}', [DownloadController::class, 'category']);

    // Content Types Top-level Routes (Hindi)
    Route::get('/mind-map', [ArticleController::class, 'index'])->defaults('typePrefix', 'mind-map')->name('articles.mindmap');
    Route::get('/mind-map/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'mind-map');

    Route::get('/state-pcs-current-affairs', [ArticleController::class, 'index'])->defaults('typePrefix', 'state-pcs-current-affairs')->name('articles.statepcs');
    Route::get('/state-pcs-current-affairs/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'state-pcs-current-affairs');

    Route::get('/daily-news-editorials', [ArticleController::class, 'index'])->defaults('typePrefix', 'daily-news-editorials')->name('articles.editorials');
    Route::get('/daily-news-editorials/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'daily-news-editorials');

    Route::get('/pib-current-affairs', [ArticleController::class, 'index'])->defaults('typePrefix', 'pib-current-affairs')->name('articles.pib');
    Route::get('/pib-current-affairs/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'pib-current-affairs');

    Route::get('/loksabha-rajyasabha-discussions', [ArticleController::class, 'index'])->defaults('typePrefix', 'loksabha-rajyasabha-discussions')->name('articles.sansadtv');
    Route::get('/loksabha-rajyasabha-discussions/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'loksabha-rajyasabha-discussions');

    Route::get('/summary-of-important-reports', [ArticleController::class, 'index'])->defaults('typePrefix', 'summary-of-important-reports')->name('articles.reports');
    Route::get('/summary-of-important-reports/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'summary-of-important-reports');

    Route::get('/to-the-points', [ArticleController::class, 'index'])->defaults('typePrefix', 'to-the-points')->name('articles.tothepoint');
    Route::get('/to-the-points/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'to-the-points');

    Route::get('/ethics/case-studies', [ArticleController::class, 'index'])->defaults('typePrefix', 'ethics/case-studies')->name('articles.ethics');
    Route::get('/ethics/case-studies/{slug}', [ArticleController::class, 'show'])->defaults('param1', 'ethics/case-studies');

    // Quizzes & Mock Tests (Hindi)
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/{slug}', [QuizController::class, 'show'])->name('quizzes.show');

    // Academic Courses & Classroom Batches (Hindi)
    Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{slug}', [PublicCourseController::class, 'show'])->name('courses.public.show');

    // Article Detail (Hindi)
    Route::get('/daily-updates/{type}/{slug}', [ArticleController::class, 'show'])
        ->where('type', '[a-zA-Z0-9\-_]+')
        ->where('slug', '[a-zA-Z0-9\-_]+')
        ->name('articles.show');

    // Article Listings (Hindi)
    Route::get('/daily-updates/{type?}', [ArticleController::class, 'index'])
        ->where('type', '[a-zA-Z0-9\-_]+')
        ->name('articles.index');

    // 11 National Centres (Hindi)
    Route::get('/centres', [CentreController::class, 'index'])->name('centres.index');
    Route::get('/centres/{slug}', [CentreController::class, 'show'])->name('centres.show');

    // Admissions & Counselling (Hindi)
    Route::get('/book-counselling', [CounsellingController::class, 'create'])->name('counselling.create');
    Route::post('/book-counselling', [CounsellingController::class, 'store'])->name('counselling.store');

    // Interview Guidance (Hindi)
    Route::get('/interview-guidance', [InterviewGuidanceController::class, 'create'])->name('interview.create');
    Route::post('/interview-guidance', [InterviewGuidanceController::class, 'store'])->name('interview.store');

    // Direct article slug lookup (Hindi)
    Route::get('/article/{slug}', [ArticleController::class, 'show'])
        ->where('slug', '[a-zA-Z0-9\-_]+')
        ->name('articles.direct');

    // Article PDF (Hindi)
    Route::get('/daily-updates/{type}/{slug}/pdf', [ArticleController::class, 'pdf'])
        ->where('type', '[a-zA-Z0-9\-_]+')
        ->where('slug', '[a-zA-Z0-9\-_]+')
        ->name('articles.type.pdf');

    Route::get('/articles/{slug}/pdf', [ArticleController::class, 'pdf'])
        ->where('slug', '[a-zA-Z0-9\-_]+')
        ->name('articles.pdf');

    Route::get('/article/{slug}/pdf', [ArticleController::class, 'pdf'])
        ->where('slug', '[a-zA-Z0-9\-_]+')
        ->name('articles.direct.pdf');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Student LMS Portal & Timed Test Attempt Engine (Auth Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses/{slug}', [StudentDashboardController::class, 'course'])->name('courses.show');
    Route::post('/courses/progress', [StudentDashboardController::class, 'trackProgress'])->name('courses.progress');

    // Interactive Quiz / Prelims Engine
    Route::post('/quiz/{quiz}/start', [QuizAttemptController::class, 'start'])->name('quiz.start');
    Route::get('/quiz/attempt/{attempt}', [QuizAttemptController::class, 'attempt'])->name('quiz.attempt');
    Route::post('/quiz/attempt/{attempt}/autosave', [QuizAttemptController::class, 'autosave'])->name('quiz.autosave');
    Route::post('/quiz/attempt/{attempt}/submit', [QuizAttemptController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/attempt/{attempt}/result', [QuizAttemptController::class, 'result'])->name('quiz.result');

    // Mains Subjective Answer Copy Evaluation
    Route::get('/mains', [MainsSubmissionController::class, 'index'])->name('mains.index');
    Route::get('/mains/{mainsTest}', [MainsSubmissionController::class, 'show'])->name('mains.show');
    Route::post('/mains/{mainsTest}/submit', [MainsSubmissionController::class, 'store'])->name('mains.submit');
    Route::get('/mains/evaluated/{submission}', [MainsSubmissionController::class, 'evaluated'])->name('mains.evaluated');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Staff & Editors RBAC Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Content CMS (Articles)
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
    Route::post('/articles/bulk-action', [AdminArticleController::class, 'bulkAction'])->name('articles.bulk');

    // Media Manager (Master of Files)
    Route::get('/media', [AdminMediaController::class, 'index'])->name('media.index');
    Route::post('/media', [AdminMediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{media}', [AdminMediaController::class, 'destroy'])->name('media.destroy');
    Route::get('/media/picker', [AdminMediaController::class, 'picker'])->name('media.picker');

    // Courses & LMS Management
    Route::resource('courses', AdminCourseController::class);
    Route::post('/courses/{course}/batches', [AdminCourseController::class, 'storeBatch'])->name('courses.batches.store');
    Route::delete('/batches/{batch}', [AdminCourseController::class, 'destroyBatch'])->name('courses.batches.destroy');
    Route::post('/batches/{batch}/lessons', [AdminCourseController::class, 'storeLesson'])->name('courses.batches.lessons.store');
    Route::put('/lessons/{lesson}', [AdminCourseController::class, 'updateLesson'])->name('courses.batches.lessons.update');
    Route::delete('/lessons/{lesson}', [AdminCourseController::class, 'destroyLesson'])->name('courses.batches.lessons.destroy');

    // Quizzes & Daily Current Affairs Tests Management
    Route::get('/quizzes/template/download', [AdminQuizController::class, 'downloadTemplate'])->name('quizzes.template.download');
    Route::post('/quizzes/{quiz}/import-questions', [AdminQuizController::class, 'importQuestions'])->name('quizzes.questions.import');
    Route::resource('quizzes', AdminQuizController::class);
    Route::post('/quizzes/{quiz}/questions', [AdminQuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
    Route::delete('/quizzes/{quiz}/questions/{question}', [AdminQuizController::class, 'detachQuestion'])->name('quizzes.questions.detach');

    // Mains Answer Evaluation Queue, Evaluator Management & Grading
    Route::get('/mains', [AdminMainsEvaluationController::class, 'index'])->name('mains.index');
    Route::get('/mains/{submission}/evaluate', [AdminMainsEvaluationController::class, 'show'])->name('mains.evaluate');
    Route::post('/mains/{submission}/evaluate', [AdminMainsEvaluationController::class, 'evaluate'])->name('mains.evaluate.store');
    Route::post('/mains/{submission}/assign', [AdminMainsEvaluationController::class, 'assign'])->name('mains.assign');
    Route::post('/mains/evaluators', [AdminMainsEvaluationController::class, 'storeEvaluator'])->name('mains.evaluators.store');
    Route::delete('/mains/evaluators/{user}', [AdminMainsEvaluationController::class, 'destroyEvaluator'])->name('mains.evaluators.destroy');

    // CRM Lead Pipeline & Assignment
    Route::get('/crm', [AdminCrmController::class, 'index'])->name('crm.index');
    Route::post('/crm/{lead}/stage', [AdminCrmController::class, 'updateStage'])->name('crm.stage');
    Route::post('/crm/{lead}/assign', [AdminCrmController::class, 'assign'])->name('crm.assign');

    // Analytics & Report Exports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/leads', [AdminReportController::class, 'exportLeads'])->name('reports.export.leads');
    Route::get('/reports/export/quizzes', [AdminReportController::class, 'exportQuizPerformance'])->name('reports.export.quizzes');

    // Navigation & Mega-Menu Management
    Route::get('/navigation', [AdminNavigationController::class, 'index'])->name('navigation.index');
    Route::post('/navigation/items', [AdminNavigationController::class, 'storeItem'])->name('navigation.items.store');
    Route::put('/navigation/items/{item}', [AdminNavigationController::class, 'updateItem'])->name('navigation.items.update');
    Route::delete('/navigation/items/{item}', [AdminNavigationController::class, 'destroyItem'])->name('navigation.items.destroy');

    // Homepage Slots & Announcement Tickers
    Route::get('/homepage-slots', [AdminHomepageSlotController::class, 'index'])->name('slots.index');
    Route::post('/homepage-slots/items', [AdminHomepageSlotController::class, 'storeItem'])->name('slots.items.store');
    Route::put('/homepage-slots/items/{item}', [AdminHomepageSlotController::class, 'updateItem'])->name('slots.items.update');
    Route::delete('/homepage-slots/items/{item}', [AdminHomepageSlotController::class, 'destroyItem'])->name('slots.items.destroy');
    Route::post('/homepage-slots/banner', [AdminHomepageSlotController::class, 'updateBanner'])->name('slots.banner.update');

    // National Centres & Addresses Management
    Route::resource('centres', AdminCentreController::class)->except(['show']);

    // Audit Logs
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
});
