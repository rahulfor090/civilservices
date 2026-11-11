<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\User;
use Tests\TestCase;

class AdminQuizManagementTest extends TestCase
{
    protected User $admin;
    protected Language $language;
    protected QuizCategory $dailyCaCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'admin@civilservicegurukul.com')->first();
        $this->language = Language::where('code', 'en')->first();
        $this->dailyCaCategory = QuizCategory::where('slug', 'daily-current-affairs-quiz')->first()
            ?? QuizCategory::first();
    }

    public function test_admin_quizzes_index_loads_with_metrics_and_filters(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/quizzes');

        $response->assertStatus(200);
        $response->assertSee('Quizzes & Mock Tests Manager', false);
        $response->assertSee('Create Daily CA Quiz');
        $response->assertSee('Daily CA Quizzes');
    }

    public function test_admin_quiz_create_page_renders_fields(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/quizzes/create?category_slug=daily-current-affairs-quiz');

        $response->assertStatus(200);
        $response->assertSee('Create Daily Current Affairs Quiz');
        $response->assertSee('Duration (Minutes)');
        $response->assertSee('Marks / Question');
        $response->assertSee('Negative Marks');
    }

    public function test_admin_can_create_daily_current_affairs_quiz(): void
    {
        $uniqueSlug = 'daily-ca-quiz-test-' . time();

        $response = $this->actingAs($this->admin)->post('/admin/quizzes', [
            'quiz_category_id' => $this->dailyCaCategory->id,
            'language_id' => $this->language->id,
            'title' => 'Daily Current Affairs Quiz — Test ' . time(),
            'slug' => $uniqueSlug,
            'instructions' => 'Test instructions for candidates.',
            'duration_minutes' => 15,
            'marks_per_question' => 2.00,
            'negative_marks_per_question' => 0.66,
            'pass_percentage' => 40,
            'is_free' => 1,
            'allow_reattempt' => 1,
            'shuffle_questions' => 0,
            'status' => 'published',
        ]);

        $quiz = Quiz::where('slug', $uniqueSlug)->first();
        $this->assertNotNull($quiz);
        $response->assertRedirect(route('admin.quizzes.edit', $quiz));

        $this->assertEquals(15, $quiz->duration_minutes);
        $this->assertEquals(2.00, $quiz->marks_per_question);
        $this->assertEquals(0.66, $quiz->negative_marks_per_question);
    }

    public function test_admin_can_add_mcq_question_to_quiz_with_options_and_explanation(): void
    {
        $quiz = Quiz::first();
        $this->assertNotNull($quiz);

        $response = $this->actingAs($this->admin)->post("/admin/quizzes/{$quiz->id}/questions", [
            'stem' => 'Consider the following statements regarding the <strong>Election Commission of India</strong>:<br>1. Chief Election Commissioner has equal voting power as Election Commissioners.<br>2. CEC can be removed only like a Supreme Court Judge.<br>Which statement is correct?',
            'explanation' => 'Both statements are correct under Article 324 and the Election Commission Act.',
            'subject_tag' => 'Polity & Governance',
            'topic_tag' => 'Election Commission',
            'difficulty' => 'medium',
            'options' => [
                '1 only',
                '2 only',
                'Both 1 and 2',
                'Neither 1 nor 2',
            ],
            'correct_option' => 2, // Option C is correct
        ]);

        $response->assertRedirect(route('admin.quizzes.edit', $quiz));

        $latestQuestion = Question::latest('id')->first();
        $this->assertNotNull($latestQuestion);
        $this->assertEquals(4, $latestQuestion->options()->count());

        $correctOpt = $latestQuestion->options()->where('is_correct', true)->first();
        $this->assertNotNull($correctOpt);
        $this->assertEquals('Both 1 and 2', $correctOpt->option_text);

        // Verify quiz has attached the question
        $this->assertTrue($quiz->questions()->where('questions.id', $latestQuestion->id)->exists());
    }
}
