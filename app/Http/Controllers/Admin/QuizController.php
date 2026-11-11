<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Language;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Services\QuizExcelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuizController extends Controller
{
    public function index(Request $request): View
    {
        $query = Quiz::with(['category', 'language'])
            ->withCount(['questions', 'attempts']);

        if ($request->filled('category_id')) {
            $query->where('quiz_category_id', $request->category_id);
        }

        if ($request->filled('category_slug')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category_slug));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('slug', 'like', $search);
            });
        }

        $quizzes = $query->latest('id')->paginate(15)->withQueryString();

        $dailyCaCategory = QuizCategory::where('slug', 'daily-current-affairs-quiz')->first();

        $stats = [
            'total_quizzes' => Quiz::count(),
            'daily_ca_quizzes' => $dailyCaCategory ? Quiz::where('quiz_category_id', $dailyCaCategory->id)->count() : 0,
            'published_quizzes' => Quiz::where('status', 'published')->count(),
            'total_attempts' => \App\Models\QuizAttempt::count(),
        ];

        $categories = QuizCategory::where('is_active', true)->get();
        $languages = Language::where('is_active', true)->get();

        return view('admin.quizzes.index', compact('quizzes', 'stats', 'categories', 'languages'));
    }

    public function create(Request $request): View
    {
        $categories = QuizCategory::where('is_active', true)->get();
        $languages = Language::where('is_active', true)->get();

        $defaultCategory = null;
        if ($request->filled('category_slug')) {
            $defaultCategory = QuizCategory::where('slug', $request->category_slug)->first();
        }

        return view('admin.quizzes.create', compact('categories', 'languages', 'defaultCategory'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quiz_category_id' => 'required|exists:quiz_categories,id',
            'language_id' => 'required|exists:languages,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:191|unique:quizzes,slug',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'marks_per_question' => 'required|numeric|min:0.5',
            'negative_marks_per_question' => 'required|numeric|min:0',
            'pass_percentage' => 'required|numeric|min:1|max:100',
            'is_free' => 'boolean',
            'allow_reattempt' => 'boolean',
            'shuffle_questions' => 'boolean',
            'status' => 'required|in:draft,scheduled,published,archived',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        if (Quiz::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $quiz = Quiz::create([
            'quiz_category_id' => $validated['quiz_category_id'],
            'language_id' => $validated['language_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'instructions' => $validated['instructions'] ?? 'Each question carries 2 marks. There is a penalty of 0.66 marks for each incorrect answer.',
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => 0.00,
            'marks_per_question' => $validated['marks_per_question'],
            'negative_marks_per_question' => $validated['negative_marks_per_question'],
            'pass_percentage' => $validated['pass_percentage'],
            'is_free' => $request->boolean('is_free'),
            'allow_reattempt' => $request->boolean('allow_reattempt'),
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' ? now() : null,
            'created_by' => auth()->id(),
        ]);

        AuditLog::record(auth()->user(), 'quiz', 'create_quiz', $quiz, null, $quiz->toArray());

        return redirect()->route('admin.quizzes.edit', $quiz)->with('success', 'Quiz created successfully. Now add questions below.');
    }

    public function edit(Quiz $quiz): View
    {
        $quiz->load(['category', 'language', 'questions.options']);
        $categories = QuizCategory::where('is_active', true)->get();
        $languages = Language::where('is_active', true)->get();

        return view('admin.quizzes.edit', compact('quiz', 'categories', 'languages'));
    }

    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        $before = $quiz->toArray();

        $validated = $request->validate([
            'quiz_category_id' => 'required|exists:quiz_categories,id',
            'language_id' => 'required|exists:languages,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:191|unique:quizzes,slug,' . $quiz->id,
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'marks_per_question' => 'required|numeric|min:0.5',
            'negative_marks_per_question' => 'required|numeric|min:0',
            'pass_percentage' => 'required|numeric|min:1|max:100',
            'status' => 'required|in:draft,scheduled,published,archived',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_free'] = $request->boolean('is_free');
        $validated['allow_reattempt'] = $request->boolean('allow_reattempt');
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');

        if ($validated['status'] === 'published' && !$quiz->published_at) {
            $validated['published_at'] = now();
        }

        $quiz->update($validated);
        $this->recalculateTotalMarks($quiz);

        AuditLog::record(auth()->user(), 'quiz', 'update_quiz', $quiz, $before, $quiz->toArray());

        return redirect()->route('admin.quizzes.edit', $quiz)->with('success', 'Quiz settings updated successfully.');
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $before = $quiz->toArray();
        $quiz->delete();

        AuditLog::record(auth()->user(), 'quiz', 'delete_quiz', null, $before, null);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted successfully.');
    }

    public function storeQuestion(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'stem' => 'required|string',
            'explanation' => 'nullable|string',
            'subject_tag' => 'nullable|string|max:100',
            'topic_tag' => 'nullable|string|max:100',
            'difficulty' => 'required|in:easy,medium,hard',
            'options' => 'required|array|min:2|max:6',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer|min:0|max:5',
        ]);

        $question = Question::create([
            'language_id' => $quiz->language_id,
            'stem' => $validated['stem'],
            'explanation' => $validated['explanation'] ?? null,
            'subject_tag' => $validated['subject_tag'] ?? 'Current Affairs',
            'topic_tag' => $validated['topic_tag'] ?? null,
            'difficulty' => $validated['difficulty'],
            'default_marks' => $quiz->marks_per_question,
            'default_negative_marks' => $quiz->negative_marks_per_question,
            'is_active' => true,
        ]);

        foreach ($validated['options'] as $index => $optText) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $optText,
                'is_correct' => ($index == $validated['correct_option']),
                'sort_order' => $index + 1,
            ]);
        }

        $nextSortOrder = $quiz->questions()->count() + 1;
        $quiz->questions()->attach($question->id, [
            'section_name' => $validated['subject_tag'] ?? 'General Studies',
            'sort_order' => $nextSortOrder,
        ]);

        $this->recalculateTotalMarks($quiz);

        AuditLog::record(auth()->user(), 'quiz', 'add_question', $quiz, null, ['question_id' => $question->id]);

        return redirect()->route('admin.quizzes.edit', $quiz)->with('success', "Question #{$nextSortOrder} added to quiz successfully.");
    }

    public function detachQuestion(Quiz $quiz, Question $question): RedirectResponse
    {
        $quiz->questions()->detach($question->id);
        $this->recalculateTotalMarks($quiz);

        AuditLog::record(auth()->user(), 'quiz', 'detach_question', $quiz, null, ['question_id' => $question->id]);

        return redirect()->route('admin.quizzes.edit', $quiz)->with('success', 'Question removed from this quiz.');
    }

    protected function recalculateTotalMarks(Quiz $quiz): void
    {
        $count = $quiz->questions()->count();
        $quiz->update([
            'total_marks' => $count * $quiz->marks_per_question,
        ]);
    }

    /**
     * Download Excel/CSV Questions Template
     */
    public function downloadTemplate(Request $request, QuizExcelService $excelService): StreamedResponse
    {
        return $excelService->downloadTemplate($request->query('format', 'xlsx'));
    }

    /**
     * Import Questions in bulk from uploaded Excel/CSV
     */
    public function importQuestions(Request $request, Quiz $quiz, QuizExcelService $excelService): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|max:10240',
        ]);

        $file = $request->file('excel_file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
            return back()->withErrors(['excel_file' => 'Please upload a valid Excel (.xlsx, .xls) or CSV file.']);
        }

        $result = $excelService->importQuestions($file, $quiz);

        AuditLog::record(auth()->user(), 'quiz', 'import_questions', $quiz, null, [
            'imported_count' => $result['imported'],
            'errors_count' => count($result['errors']),
        ]);

        $message = "Successfully imported {$result['imported']} questions into this quiz!";
        if (!empty($result['errors'])) {
            return redirect()->route('admin.quizzes.edit', $quiz)
                ->with('success', $message)
                ->with('warning', implode('<br>', array_slice($result['errors'], 0, 5)));
        }

        return redirect()->route('admin.quizzes.edit', $quiz)->with('success', $message);
    }
}
