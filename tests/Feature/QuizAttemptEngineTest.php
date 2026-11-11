<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\QuizScoringService;
use Tests\TestCase;

class QuizAttemptEngineTest extends TestCase
{
    /**
     * Test that guests cannot start a quiz attempt and are redirected to login.
     */
    public function test_guest_is_redirected_to_login_when_starting_quiz(): void
    {
        $quiz = Quiz::published()->first();
        $this->assertNotNull($quiz, 'Published quiz must exist.');

        $response = $this->post("/student/quiz/{$quiz->id}/start");
        $response->assertRedirect('/login');
    }

    /**
     * Test that an authenticated student can start an attempt with server timer and question slots.
     */
    public function test_student_can_start_quiz_attempt(): void
    {
        $student = User::where('email', 'student@civilservicegurukul.com')->firstOrFail();
        $quiz = Quiz::published()->firstOrFail();

        $response = $this->actingAs($student)->post("/student/quiz/{$quiz->id}/start");
        $response->assertRedirect();

        $attempt = QuizAttempt::where('user_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($attempt);
        $this->assertEquals('in_progress', $attempt->status);
        $this->assertTrue($attempt->remaining_seconds > 0);

        // Verify attempt screen renders
        $attemptResponse = $this->actingAs($student)->get("/student/quiz/attempt/{$attempt->id}");
        $attemptResponse->assertStatus(200);
        $attemptResponse->assertSee($quiz->title);
        $attemptResponse->assertSee('Question Palette');
        $attemptResponse->assertSee('Submit Test');
    }

    /**
     * Test that autosave endpoint records answers and review markers asynchronously.
     */
    public function test_autosave_records_selected_option_and_review_state(): void
    {
        $student = User::where('email', 'student@civilservicegurukul.com')->firstOrFail();
        $quiz = Quiz::published()->with('questions.options')->firstOrFail();

        $scoringService = app(QuizScoringService::class);
        $attempt = $scoringService->startAttempt($student, $quiz);

        $question = $quiz->questions->first();
        $option = $question->options->first();

        $response = $this->actingAs($student)->postJson("/student/quiz/attempt/{$attempt->id}/autosave", [
            'question_id' => $question->id,
            'selected_option_ids' => [$option->id],
            'is_marked_for_review' => true,
            'time_spent_seconds' => 12,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_answered' => true,
            'is_marked_for_review' => true,
        ]);

        $this->assertDatabaseHas('quiz_attempt_answers', [
            'quiz_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'is_answered' => true,
            'is_marked_for_review' => true,
        ]);
    }

    /**
     * Test that submission accurately computes score, negative penalty, accuracy, and rank.
     */
    public function test_quiz_submission_calculates_scores_and_negative_marking(): void
    {
        $student = User::where('email', 'student@civilservicegurukul.com')->firstOrFail();
        $quiz = Quiz::published()->with('questions.options')->firstOrFail();

        $scoringService = app(QuizScoringService::class);
        $attempt = $scoringService->startAttempt($student, $quiz);

        $questions = $quiz->questions;
        $this->assertTrue($questions->count() >= 2);

        // Answer 1st question correctly (+2.0)
        $q1 = $questions[0];
        $correctOpt = $q1->options->where('is_correct', true)->first();
        $scoringService->autosaveAnswer($attempt, $q1->id, [$correctOpt->id], false, 20);

        // Answer 2nd question incorrectly (-0.66)
        $q2 = $questions[1];
        $wrongOpt = $q2->options->where('is_correct', false)->first();
        $scoringService->autosaveAnswer($attempt, $q2->id, [$wrongOpt->id], false, 25);

        // Submit attempt
        $response = $this->actingAs($student)->post("/student/quiz/attempt/{$attempt->id}/submit");
        $response->assertRedirect("/student/quiz/attempt/{$attempt->id}/result");

        $attempt->refresh();
        $this->assertEquals('completed', $attempt->status);
        $this->assertEquals(1, $attempt->correct_count);
        $this->assertEquals(1, $attempt->incorrect_count);
        $this->assertEquals(2.00, (float) $attempt->marks_positive);
        $this->assertEquals(0.66, (float) $attempt->marks_negative);
        $this->assertEquals(1.34, (float) $attempt->score); // 2.00 - 0.66 = 1.34
        $this->assertEquals(50.00, (float) $attempt->accuracy_pct); // 1 / 2 = 50%
        $this->assertNotNull($attempt->all_india_rank);

        // Result view renders
        $resultResponse = $this->actingAs($student)->get("/student/quiz/attempt/{$attempt->id}/result");
        $resultResponse->assertStatus(200);
        $resultResponse->assertSee('Scorecard');
        $resultResponse->assertSee('Question-by-Question Solution Review');
    }
}
