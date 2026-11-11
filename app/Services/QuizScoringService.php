<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QuizScoringService
{
    /**
     * Start or resume a quiz attempt for a user
     */
    public function startAttempt(User $user, Quiz $quiz): QuizAttempt
    {
        // Check for active uncompleted attempt
        $existing = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        if ($existing) {
            // Check if expired
            if ($existing->isExpired()) {
                return $this->submitAttempt($existing);
            }
            return $existing;
        }

        // Create new attempt
        $expiresAt = Carbon::now()->addMinutes($quiz->duration_minutes);

        return DB::transaction(function () use ($user, $quiz, $expiresAt) {
            $attempt = QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'started_at' => now(),
                'expires_at' => $expiresAt,
                'status' => 'in_progress',
                'max_score' => $quiz->total_marks,
                'unanswered_count' => $quiz->questions()->count(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Initialize question answer slots for palette stability
            $questions = $quiz->questions()->get();
            foreach ($questions as $question) {
                QuizAttemptAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_option_ids' => null,
                    'is_marked_for_review' => false,
                    'is_answered' => false,
                ]);
            }

            return $attempt;
        });
    }

    /**
     * Autosave a single question response asynchronously
     */
    public function autosaveAnswer(
        QuizAttempt $attempt,
        int $questionId,
        array $selectedOptionIds,
        bool $markedForReview = false,
        int $timeSpentSeconds = 0
    ): QuizAttemptAnswer {
        if ($attempt->status !== 'in_progress') {
            return QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)
                ->where('question_id', $questionId)
                ->firstOrFail();
        }

        // If time expired, auto-submit attempt
        if ($attempt->isExpired()) {
            $this->submitAttempt($attempt);
            return QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)
                ->where('question_id', $questionId)
                ->firstOrFail();
        }

        $answer = QuizAttemptAnswer::firstOrNew([
            'quiz_attempt_id' => $attempt->id,
            'question_id' => $questionId,
        ]);

        $isAnswered = !empty($selectedOptionIds);

        $answer->selected_option_ids = $selectedOptionIds;
        $answer->is_answered = $isAnswered;
        $answer->is_marked_for_review = $markedForReview;
        $answer->time_spent_seconds += $timeSpentSeconds;
        $answer->save();

        // Increment total time on attempt
        $attempt->increment('time_spent_seconds', $timeSpentSeconds);

        return $answer;
    }

    /**
     * Submit and compute detailed exam results, negative marks, and analytics
     */
    public function submitAttempt(QuizAttempt $attempt): QuizAttempt
    {
        if ($attempt->status === 'completed') {
            return $attempt;
        }

        return DB::transaction(function () use ($attempt) {
            $quiz = $attempt->quiz;
            $answers = $attempt->answers()->with('question.options')->get();

            $correctCount = 0;
            $incorrectCount = 0;
            $unansweredCount = 0;
            $markedForReviewCount = 0;
            $marksPositive = 0.00;
            $marksNegative = 0.00;

            $subjectAnalytics = [];

            foreach ($answers as $answer) {
                $question = $answer->question;
                $subject = $question->subject_tag ?? 'General Studies';

                if (!isset($subjectAnalytics[$subject])) {
                    $subjectAnalytics[$subject] = [
                        'total' => 0,
                        'correct' => 0,
                        'incorrect' => 0,
                        'unattempted' => 0,
                        'marks' => 0.0,
                    ];
                }
                $subjectAnalytics[$subject]['total']++;

                if ($answer->is_marked_for_review) {
                    $markedForReviewCount++;
                }

                $selected = $answer->selected_option_ids ?? [];

                if (empty($selected)) {
                    $unansweredCount++;
                    $subjectAnalytics[$subject]['unattempted']++;
                    $answer->update([
                        'is_correct' => null,
                        'marks_awarded' => 0.00,
                    ]);
                    continue;
                }

                // Get correct option IDs
                $correctOptionIds = $question->options
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();

                sort($correctOptionIds);
                $selectedInts = array_map('intval', $selected);
                sort($selectedInts);

                $marksQ = (float) $quiz->marks_per_question;
                $negMarksQ = (float) $quiz->negative_marks_per_question;

                if ($selectedInts === $correctOptionIds) {
                    $correctCount++;
                    $marksPositive += $marksQ;
                    $subjectAnalytics[$subject]['correct']++;
                    $subjectAnalytics[$subject]['marks'] += $marksQ;

                    $answer->update([
                        'is_correct' => true,
                        'marks_awarded' => $marksQ,
                    ]);
                } else {
                    $incorrectCount++;
                    $marksNegative += $negMarksQ;
                    $subjectAnalytics[$subject]['incorrect']++;
                    $subjectAnalytics[$subject]['marks'] -= $negMarksQ;

                    $answer->update([
                        'is_correct' => false,
                        'marks_awarded' => -$negMarksQ,
                    ]);
                }
            }

            $netScore = $marksPositive - $marksNegative;
            $attemptedTotal = $correctCount + $incorrectCount;
            $accuracy = $attemptedTotal > 0 ? round(($correctCount / $attemptedTotal) * 100, 2) : 0.00;

            $attempt->update([
                'status' => 'completed',
                'submitted_at' => now(),
                'score' => $netScore,
                'marks_positive' => $marksPositive,
                'marks_negative' => $marksNegative,
                'correct_count' => $correctCount,
                'incorrect_count' => $incorrectCount,
                'unanswered_count' => $unansweredCount,
                'marked_for_review_count' => $markedForReviewCount,
                'accuracy_pct' => $accuracy,
                'section_analytics_json' => $subjectAnalytics,
            ]);

            // Compute All India Rank & Percentile dynamically
            $higherScoresCount = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('status', 'completed')
                ->where('score', '>', $netScore)
                ->count();

            $totalCompleted = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('status', 'completed')
                ->count();

            $rank = $higherScoresCount + 1;
            $percentile = $totalCompleted > 1 
                ? round((($totalCompleted - $rank) / $totalCompleted) * 100, 2) 
                : 100.00;

            $attempt->update([
                'all_india_rank' => $rank,
                'percentile' => max(0, min(100, $percentile)),
            ]);

            return $attempt->fresh();
        });
    }
}
