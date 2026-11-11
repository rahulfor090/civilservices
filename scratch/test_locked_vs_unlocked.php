<?php

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseLesson;
use App\Models\User;
use Illuminate\Support\Carbon;

$course = Course::where('slug', 'upsc-gs-foundation-2027-planner-demo')->first();
$batch = $course->batches->first();
$user = User::first();

// Test 1: Future Scheduled Lesson (Locked state)
$lessonFuture = CourseLesson::where('course_batch_id', $batch->id)
    ->where('activity_type', 'phone_session')
    ->first();

$htmlLocked = view('student.courses.show', [
    'course' => $course,
    'batch' => $batch,
    'lessons' => $batch->lessons,
    'groupedPlanner' => $batch->lessons->groupBy('scheduled_date'),
    'activeLesson' => $lessonFuture,
    'isEnrolled' => true,
    'quizAttempt' => null,
    'mainsSubmission' => null,
])->render();

echo "Future lesson is_unlocked: " . ($lessonFuture->is_unlocked ? 'true' : 'false') . "\n";
if (str_contains($htmlLocked, 'Scheduled Session') && str_contains($htmlLocked, 'Pre-Session Alert Notification Scheduled')) {
    echo "SUCCESS: Future scheduled lesson correctly renders locked banner with 30m advance alert info!\n";
} else {
    echo "FAILED: Future lesson rendering unexpected.\n";
}

// Test 2: Unlocked / Today's Lesson
$lessonToday = CourseLesson::create([
    'course_batch_id' => $batch->id,
    'title' => 'Today\'s 1-on-1 Faculty Phone Mentoring Window',
    'lesson_type' => 'video',
    'activity_type' => 'phone_session',
    'scheduled_date' => now()->toDateString(),
    'scheduled_time' => '10:00',
    'scheduled_at' => now()->subHour(), // Unlocked 1 hour ago
    'duration_minutes' => 60,
    'alert_minutes_before' => 30,
    'mentor_name' => 'Dr. R.K. Verma (Senior Faculty, Polity)',
    'mentor_phone' => '+91 9911020626',
    'call_window' => '10:00 AM - 11:00 AM (Direct Call Window)',
    'session_instructions' => 'Call directly to discuss doubts.',
    'sort_order' => 10,
]);

echo "Today lesson is_unlocked: " . ($lessonToday->is_unlocked ? 'true' : 'false') . "\n";

$htmlUnlocked = view('student.courses.show', [
    'course' => $course,
    'batch' => $batch,
    'lessons' => $batch->lessons,
    'groupedPlanner' => $batch->lessons->groupBy('scheduled_date'),
    'activeLesson' => $lessonToday,
    'isEnrolled' => true,
    'quizAttempt' => null,
    'mainsSubmission' => null,
])->render();

if (str_contains($htmlUnlocked, 'Phone Interactive Session') &&
    str_contains($htmlUnlocked, '+91 9911020626') &&
    str_contains($htmlUnlocked, 'Call Mentor Directly') &&
    str_contains($htmlUnlocked, 'Dr. R.K. Verma')) {
    echo "SUCCESS: Unlocked phone session renders full interactive card, phone button, mentor name and call window!\n";
} else {
    echo "FAILED: Unlocked phone session missing key details.\n";
}

// Clean up test lesson
$lessonToday->delete();
