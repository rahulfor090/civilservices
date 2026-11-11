<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\User;
use Tests\TestCase;

class StudentLmsPortalTest extends TestCase
{
    /**
     * Test that student dashboard loads with enrolled courses and scorecards.
     */
    public function test_student_dashboard_displays_enrolled_courses(): void
    {
        $student = User::where('email', 'student@civilservicegurukul.com')->firstOrFail();

        $response = $this->actingAs($student)->get('/student/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Student Learning Portal');
        $response->assertSee('My Enrolled Courses');
        $response->assertSee('Recent Test Scorecards');
    }

    /**
     * Test that course player view loads with lesson curriculum and video container.
     */
    public function test_course_player_displays_curriculum_and_video(): void
    {
        $student = User::where('email', 'student@civilservicegurukul.com')->firstOrFail();
        $course = Course::where('slug', 'upsc-cse-foundation-pre-cum-mains-2027')->firstOrFail();

        $response = $this->actingAs($student)->get("/student/courses/{$course->slug}");

        $response->assertStatus(200);
        $response->assertSee('Course Curriculum');
        $response->assertSee('Lecture 01');
    }

    /**
     * Test that student lesson progress is persisted via progress endpoint.
     */
    public function test_lesson_watch_progress_is_recorded(): void
    {
        $student = User::where('email', 'student@civilservicegurukul.com')->firstOrFail();
        $lesson = CourseLesson::firstOrFail();

        $response = $this->actingAs($student)->postJson('/student/courses/progress', [
            'lesson_id' => $lesson->id,
            'watched_seconds' => 1200,
            'is_completed' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $student->id,
            'course_lesson_id' => $lesson->id,
            'watched_seconds' => 1200,
            'is_completed' => true,
        ]);
    }
}
