<?php

namespace Tests\Feature;

use App\Models\MainsSubmission;
use App\Models\MainsTest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MainsAnswerEvaluationTest extends TestCase
{
    public function test_guest_is_redirected_from_student_mains_portal(): void
    {
        $response = $this->get('/student/mains');
        $response->assertRedirect('/login');
    }

    public function test_student_can_view_mains_test_and_submit_answer_copy(): void
    {
        Storage::fake('public');

        $student = User::where('email', 'student@civilservicegurukul.com')->firstOrFail();
        $test = MainsTest::where('slug', 'upsc-mains-gs2-mock-2026')->firstOrFail();

        // 1. View Test Paper
        $response = $this->actingAs($student)->get("/student/mains/{$test->id}");
        $response->assertStatus(200);
        $response->assertSee($test->title);
        $response->assertSee('Submit Answer Copy');

        // 2. Submit Fake PDF
        $file = UploadedFile::fake()->create('my_gs2_copy.pdf', 1024, 'application/pdf');

        $submitResponse = $this->actingAs($student)->post("/student/mains/{$test->id}/submit", [
            'answer_copy_pdf' => $file,
            'candidate_roll_no' => 'UPSC-998877',
        ]);

        $submitResponse->assertRedirect(route('student.mains.index'));

        $this->assertDatabaseHas('mains_submissions', [
            'user_id' => $student->id,
            'mains_test_id' => $test->id,
            'candidate_roll_no' => 'UPSC-998877',
            'status' => 'submitted',
        ]);
    }

    public function test_admin_or_evaluator_can_grade_submission_with_rubrics(): void
    {
        $admin = User::where('email', 'admin@civilservicegurukul.com')->firstOrFail();
        $test = MainsTest::where('slug', 'upsc-mains-gs2-mock-2026')->firstOrFail();
        $student = User::where('email', 'student@civilservicegurukul.com')->firstOrFail();

        $submission = MainsSubmission::where('user_id', $student->id)
            ->where('mains_test_id', $test->id)
            ->firstOrFail();

        // Admin grades the submission
        $q = $test->questions->first();

        $gradeResponse = $this->actingAs($admin)->post("/admin/mains/{$submission->id}/evaluate", [
            'rubrics' => [
                [
                    'question_id' => $q->id,
                    'intro_score' => 2.0,
                    'content_score' => 5.0,
                    'structure_score' => 2.0,
                    'conclusion_score' => 1.0,
                    'comments' => 'Clear formulation of constitutional arguments.',
                ]
            ],
            'general_remarks' => 'Outstanding structure and crisp analytical presentation.',
        ]);

        $gradeResponse->assertRedirect(route('admin.mains.index'));

        $submission->refresh();
        $this->assertEquals('evaluated', $submission->status);
        $this->assertEquals(10.0, $submission->total_score);

        // Student can view evaluated report
        $studentReportResponse = $this->actingAs($student)->get("/student/mains/evaluated/{$submission->id}");
        $studentReportResponse->assertStatus(200);
        $studentReportResponse->assertSee('Evaluated Scorecard');
        $studentReportResponse->assertSee('10');
        $studentReportResponse->assertSee('Outstanding structure');
    }

    public function test_admin_can_view_mains_evaluation_queue(): void
    {
        $admin = User::where('email', 'admin@civilservicegurukul.com')->firstOrFail();

        $response = $this->actingAs($admin)->get('/admin/mains');
        $response->assertStatus(200);
        $response->assertSee('Mains Evaluation');
        $response->assertSee('Evaluator Faculty');
    }

    public function test_admin_can_view_and_manage_evaluator_faculty_roster(): void
    {
        $admin = User::where('email', 'admin@civilservicegurukul.com')->firstOrFail();

        // 1. View Evaluators tab
        $response = $this->actingAs($admin)->get('/admin/mains?tab=evaluators');
        $response->assertStatus(200);
        $response->assertSee('Active Faculty Evaluators Roster');
        $response->assertSee('Register New Evaluator Faculty Member');

        // 2. Register New Evaluator
        $evalEmail = 'new.evaluator.' . time() . '@civilservicegurukul.com';
        $createResponse = $this->actingAs($admin)->post('/admin/mains/evaluators', [
            'name' => 'Prof. S. N. Mukherjee (GS Paper 1 & History)',
            'email' => $evalEmail,
            'mobile' => '9811020626',
            'password' => 'FacultyPassword123!',
        ]);

        $createResponse->assertRedirect(route('admin.mains.index', ['tab' => 'evaluators']));
        $newEvaluator = User::where('email', $evalEmail)->first();
        $this->assertNotNull($newEvaluator);
        $this->assertTrue($newEvaluator->hasRole('evaluator'));

        // 3. Revoke Evaluator Role
        $deleteResponse = $this->actingAs($admin)->delete("/admin/mains/evaluators/{$newEvaluator->id}");
        $deleteResponse->assertRedirect(route('admin.mains.index', ['tab' => 'evaluators']));
        $newEvaluator->refresh();
        $this->assertFalse($newEvaluator->hasRole('evaluator'));
    }
}
