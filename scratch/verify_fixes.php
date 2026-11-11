<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Article;
use App\Models\ContentType;
use App\Models\Language;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\User;

echo "=== 1. VERIFYING SECTIONS & CREATE ARTICLE VIEW ===\n";
$languages = Language::where('is_active', true)->get();
$contentTypes = ContentType::where('is_active', true)->get();
$sections = Section::with('translations')->where('is_active', true)->get();
$quizzes = Quiz::with('language')->orderByDesc('id')->get();
$errors = new \Illuminate\Support\ViewErrorBag();

$user = User::where('email', 'admin@civilservicegurukul.com')->first();
auth()->login($user);

$createHtml = view('admin.articles.create', compact('languages', 'contentTypes', 'sections', 'quizzes', 'errors'))->render();

assert(str_contains($createHtml, 'GS-1 - General Studies I'), 'Create view must contain GS-1 - General Studies I');
assert(str_contains($createHtml, 'GS-2 - General Studies II'), 'Create view must contain GS-2 - General Studies II');
assert(str_contains($createHtml, 'GS-3 - General Studies III'), 'Create view must contain GS-3 - General Studies III');
assert(str_contains($createHtml, 'GS-4 - General Studies IV'), 'Create view must contain GS-4 - General Studies IV');
assert(!str_contains($createHtml, 'value="1"> - </option>') && !str_contains($createHtml, 'value="1">  -  </option>'), 'Create view must NOT contain blank "-" options');

echo "✓ Create article section dropdown rendered correctly with full GS Paper labels!\n";

echo "\n=== 2. VERIFYING EDIT ARTICLE VIEW ===\n";
$article = Article::first();
$editHtml = view('admin.articles.edit', compact('article', 'languages', 'contentTypes', 'sections', 'quizzes', 'errors'))->render();

assert(str_contains($editHtml, 'GS-1 - General Studies I'), 'Edit view must contain GS-1 - General Studies I');
assert(str_contains($editHtml, 'name="section_id"'), 'Edit view must contain section_id select');
echo "✓ Edit article section dropdown rendered correctly!\n";

echo "\n=== 3. VERIFYING LOGIN VIEW (CLEAN FOR PRODUCTION) ===\n";
$loginHtml = view('auth.login', ['errors' => $errors])->render();

assert(!str_contains($loginHtml, 'Password123!'), 'Login view must NOT contain Password123!');
assert(!str_contains($loginHtml, 'Demo System Accounts'), 'Login view must NOT contain Demo System Accounts');
assert(!str_contains($loginHtml, 'value="admin@civilservicegurukul.com"'), 'Login view must NOT prefill admin email');
assert(str_contains($loginHtml, 'placeholder="••••••••"'), 'Login view must contain password placeholder');
echo "✓ Login view is completely clean of demo credentials and auto-fill passwords!\n";

echo "\n ALL VERIFICATIONS PASSED SUCCESSFULLY!\n";
