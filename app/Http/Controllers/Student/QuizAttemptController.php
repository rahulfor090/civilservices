<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizAttemptController extends Controller
{
    protected QuizScoringService $scoringService;

    public function __construct(QuizScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Start or resume a quiz attempt
     */
    public function start(Quiz $quiz): RedirectResponse
    {
        $attempt = $this->scoringService->startAttempt(auth()->user(), $quiz);

        if ($attempt->status === 'completed') {
            return redirect()->route('student.quiz.result', $attempt);
        }

        return redirect()->route('student.quiz.attempt', $attempt);
    }

    /**
     * Render the active interactive test attempt screen
     */
    public function attempt(QuizAttempt $attempt): View|RedirectResponse
    {
        // Security: only owner can take attempt
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this attempt.');
        }

        // If expired, auto-submit
        if ($attempt->isExpired() && $attempt->status === 'in_progress') {
            $this->scoringService->submitAttempt($attempt);
            return redirect()->route('student.quiz.result', $attempt)->with('info', 'Time expired! Your test was auto-submitted.');
        }

        if ($attempt->status === 'completed') {
            return redirect()->route('student.quiz.result', $attempt);
        }

        $attempt->load(['quiz.questions.options', 'answers']);

        // Group questions by section
        $questions = $attempt->quiz->questions;
        $answers = $attempt->answers->keyBy('question_id');

        return view('student.quizzes.attempt', compact('attempt', 'questions', 'answers'));
    }

    /**
     * AJAX Autosave endpoint
     */
    public function autosave(Request $request, QuizAttempt $attempt): JsonResponse
    {
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'selected_option_ids' => 'nullable|array',
            'selected_option_ids.*' => 'integer|exists:question_options,id',
            'is_marked_for_review' => 'boolean',
            'time_spent_seconds' => 'nullable|integer|min:0',
        ]);

        $answer = $this->scoringService->autosaveAnswer(
            $attempt,
            $validated['question_id'],
            $validated['selected_option_ids'] ?? [],
            $validated['is_marked_for_review'] ?? false,
            $validated['time_spent_seconds'] ?? 0
        );

        return response()->json([
            'success' => true,
            'is_answered' => $answer->is_answered,
            'is_marked_for_review' => $answer->is_marked_for_review,
            'time_remaining_seconds' => $attempt->remaining_seconds,
        ]);
    }

    /**
     * Final submission of attempt
     */
    public function submit(Request $request, QuizAttempt $attempt): RedirectResponse
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $this->scoringService->submitAttempt($attempt);

        return redirect()->route('student.quiz.result', $attempt)->with('success', 'Test submitted successfully!');
    }

    /**
     * Scorecard, analytics, and solution review screen
     */
    public function result(QuizAttempt $attempt): View
    {
        if ($attempt->user_id !== auth()->id() && !auth()->user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized');
        }

        $attempt->load(['quiz.category', 'answers.question.options']);

        return view('student.quizzes.result', compact('attempt'));
    }
}
