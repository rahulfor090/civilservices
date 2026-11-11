<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Language;
use App\Models\User;
use Tests\TestCase;

class AdminCourseManagementTest extends TestCase
{
    protected User $admin;
    protected Language $language;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'admin@civilservicegurukul.com')->first();
        $this->language = Language::where('code', 'en')->first();
    }

    public function test_admin_courses_index_loads_with_metrics_and_table(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/courses');

        $response->assertStatus(200);
        $response->assertSee('Courses & LMS Management', false);
        $response->assertSee('Create New Course');
        $response->assertSee('Total Courses');
        $response->assertSee('Active Batches');
    }

    public function test_admin_course_create_page_renders_fields_and_rich_editor(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/courses/create');

        $response->assertStatus(200);
        $response->assertSee('Create New Course');
        $response->assertSee('Course Title');
        $response->assertSee('Selling Fee');
        $response->assertSee('Target Exam');
        $response->assertSee('quill-editor', false);
        $response->assertSee('Initial Batch Setup', false);
    }

    public function test_admin_can_create_course_with_initial_batch(): void
    {
        $uniqueSlug = 'upsc-test-course-' . time();

        $response = $this->actingAs($this->admin)->post('/admin/courses', [
            'language_id' => $this->language->id,
            'title' => 'UPSC CSE Foundation 2026 Test Course',
            'slug' => $uniqueSlug,
            'target_exam' => 'upsc_cse',
            'level' => 'foundation',
            'price' => 52000.00,
            'mrp' => 75000.00,
            'validity_days' => 365,
            'status' => 'published',
            'is_featured' => 1,
            'excerpt' => 'Test course synopsis for UPSC 2026 foundation batch',
            'description' => '<h2>Course Highlights</h2><p>Includes daily answer writing and prelims test series.</p>',
            'create_initial_batch' => 1,
            'batch_code' => 'BATCH-TEST-' . time(),
            'batch_title' => 'Morning Hybrid Batch',
            'delivery_mode' => 'hybrid',
        ]);

        $course = Course::where('slug', $uniqueSlug)->first();
        $this->assertNotNull($course);
        $response->assertRedirect(route('admin.courses.edit', $course));

        $this->assertEquals(52000.00, $course->price);
        $this->assertEquals('upsc_cse', $course->target_exam);
        $this->assertEquals(1, $course->batches()->count());

        $batch = $course->batches()->first();
        $this->assertEquals('Morning Hybrid Batch', $batch->title);
        $this->assertEquals('hybrid', $batch->delivery_mode);
    }

    public function test_admin_can_add_batch_and_lesson_to_course(): void
    {
        $course = Course::first();
        $this->assertNotNull($course);

        // 1. Add Batch
        $batchCode = 'BATCH-ADD-' . time();
        $batchResponse = $this->actingAs($this->admin)->post("/admin/courses/{$course->id}/batches", [
            'batch_code' => $batchCode,
            'title' => 'Special Weekend Batch',
            'delivery_mode' => 'online',
            'start_date' => now()->addDays(14)->toDateString(),
            'class_timing' => '10:00 AM - 01:00 PM',
            'faculty_names' => 'Senior Faculty Panel',
            'is_enrolment_open' => 1,
        ]);

        $batchResponse->assertRedirect(route('admin.courses.edit', $course));
        $batch = CourseBatch::where('batch_code', $batchCode)->first();
        $this->assertNotNull($batch);

        // 2. Add Lesson to Batch
        $lessonResponse = $this->actingAs($this->admin)->post("/admin/batches/{$batch->id}/lessons", [
            'title' => 'Orientation & UPSC Syllabus Micro-Analysis',
            'lesson_type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=sample-orientation',
            'duration_minutes' => 75,
            'sort_order' => 1,
            'is_free_preview' => 1,
        ]);

        $lessonResponse->assertRedirect(route('admin.courses.edit', $course));
        $this->assertEquals(1, $batch->lessons()->count());
        $lesson = $batch->lessons()->first();
        $this->assertEquals('Orientation & UPSC Syllabus Micro-Analysis', $lesson->title);
        $this->assertTrue($lesson->is_free_preview);
    }
}
