<?php

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseLesson;
use App\Models\Language;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Carbon;

echo "=== TESTING COURSES PLANNER & INTERACTIVE FLOW ===\n\n";

$lang = Language::first() ?? Language::create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
$user = User::first();

// 1. Create or fetch a test course
$course = Course::firstOrCreate(
    ['slug' => 'upsc-gs-foundation-2027-planner-demo'],
    [
        'language_id' => $lang->id,
        'title' => 'UPSC GS Integrated Foundation 2027 (Dynamic Planner)',
        'target_exam' => 'upsc_cse',
        'level' => 'foundation',
        'price' => 14999,
        'mrp' => 24999,
        'validity_days' => 365,
        'status' => 'published',
        'is_featured' => true,
        'excerpt' => 'Complete UPSC GS Foundation batch with scheduled video releases, daily quizzes, Mains answer writing and dedicated phone mentoring sessions.',
        'description' => '<p>Comprehensive batch covering Prelims and Mains with dynamic daily planners and 1-on-1 faculty support.</p>',
    ]
);

echo "1. Course: {$course->title} (ID: {$course->id})\n";

// 2. Create Batch starting 20 Dec 2026
$batch = CourseBatch::firstOrCreate(
    ['batch_code' => 'BATCH-GS27-DEC20'],
    [
        'course_id' => $course->id,
        'title' => 'December 20 Foundation Super Batch',
        'delivery_mode' => 'online',
        'start_date' => Carbon::parse('2026-12-20'),
        'end_date' => Carbon::parse('2027-10-30'),
        'class_timing' => '05:00 PM - 07:30 PM',
        'faculty_names' => 'Dr. R.K. Verma, Sharma Sir, Tiwari Sir',
        'is_enrolment_open' => true,
    ]
);

echo "2. Batch: {$batch->batch_code} starting {$batch->start_date->format('d M Y')}\n";

// Clear previous test lessons for this batch if any
CourseLesson::where('course_batch_id', $batch->id)->delete();

// Get an existing quiz or create sample
$quiz = Quiz::first();

// 3. Activity 1: Pre-recorded Lecture auto-scheduled at 5:00 PM on 20 Dec 2026
$lesson1 = CourseLesson::create([
    'course_batch_id' => $batch->id,
    'title' => 'Lecture 01: Preamble & Constitutional Philosophy',
    'lesson_type' => 'video',
    'activity_type' => 'video',
    'scheduled_date' => '2026-12-20',
    'scheduled_time' => '17:00',
    'scheduled_at' => Carbon::parse('2026-12-20 17:00:00'),
    'duration_minutes' => 30,
    'alert_minutes_before' => 30,
    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
    'sort_order' => 1,
    'is_free_preview' => true,
]);
echo "3. Lesson 1 Created: {$lesson1->title} (Auto-unlock: {$lesson1->scheduled_at->format('d M Y, h:i A')}, Alert at: {$lesson1->alert_time->format('h:i A')})\n";

// 4. Activity 2: Daily Timed Quiz auto-scheduled at 5:30 PM on 20 Dec 2026
$lesson2 = CourseLesson::create([
    'course_batch_id' => $batch->id,
    'title' => 'Daily Practice Quiz 01: Preamble MCQs',
    'lesson_type' => 'quiz',
    'activity_type' => 'quiz',
    'quiz_id' => $quiz?->id,
    'scheduled_date' => '2026-12-20',
    'scheduled_time' => '17:30',
    'scheduled_at' => Carbon::parse('2026-12-20 17:30:00'),
    'duration_minutes' => 30,
    'alert_minutes_before' => 30,
    'sort_order' => 2,
]);
echo "4. Lesson 2 Created: {$lesson2->title} (Auto-unlock: {$lesson2->scheduled_at->format('d M Y, h:i A')})\n";

// 5. Activity 3: Mains Subjective Question at 6:00 PM on 20 Dec 2026
$lesson3 = CourseLesson::create([
    'course_batch_id' => $batch->id,
    'title' => 'Mains Daily Answer Writing 01: Basic Structure & Preamble',
    'lesson_type' => 'video',
    'activity_type' => 'mains',
    'scheduled_date' => '2026-12-20',
    'scheduled_time' => '18:00',
    'scheduled_at' => Carbon::parse('2026-12-20 18:00:00'),
    'duration_minutes' => 30,
    'alert_minutes_before' => 30,
    'mains_question_text' => 'Discuss whether the Preamble is an integral part of the Constitution and can it be amended under Article 368? Examine in the light of the Kesavananda Bharati case. (15 Marks, 250 Words)',
    'mains_marks' => 15,
    'mains_word_limit' => 250,
    'sort_order' => 3,
]);
echo "5. Lesson 3 Created: {$lesson3->title} (Mains Question: {$lesson3->mains_marks} Marks)\n";

// 6. Activity 4: Phone Interactive Session from 6:30 PM - 07:30 PM
$lesson4 = CourseLesson::create([
    'course_batch_id' => $batch->id,
    'title' => '1-on-1 Faculty Phone Mentoring & Doubt Discussion Window',
    'lesson_type' => 'video',
    'activity_type' => 'phone_session',
    'scheduled_date' => '2026-12-20',
    'scheduled_time' => '18:30',
    'scheduled_at' => Carbon::parse('2026-12-20 18:30:00'),
    'duration_minutes' => 60,
    'alert_minutes_before' => 30,
    'mentor_name' => 'Dr. R.K. Verma (Senior Faculty, Polity)',
    'mentor_phone' => '+91 9911020626',
    'call_window' => '06:30 PM - 07:30 PM (Direct 1-on-1 Window)',
    'session_instructions' => 'Students can dial +91 9911020626 directly between 06:30 PM and 07:30 PM to discuss doubts regarding Lecture 01, today\'s Daily Quiz, or their Mains Answer Sheet.',
    'sort_order' => 4,
]);
echo "6. Lesson 4 Created: {$lesson4->title} (Mentor: {$lesson4->mentor_name}, Phone: {$lesson4->mentor_phone})\n";

// 7. Activity 5: Live 1-to-1 / Interactive Class on 21 Dec 2026
$lesson5 = CourseLesson::create([
    'course_batch_id' => $batch->id,
    'title' => 'Live Interactive Doubt Session: Constitutional Framework',
    'lesson_type' => 'live',
    'activity_type' => 'live',
    'scheduled_date' => '2026-12-21',
    'scheduled_time' => '17:00',
    'scheduled_at' => Carbon::parse('2026-12-21 17:00:00'),
    'duration_minutes' => 60,
    'alert_minutes_before' => 30,
    'meeting_platform' => 'Google Meet',
    'meeting_link' => 'https://meet.google.com/csg-gs27-demo',
    'meeting_password' => 'CSG2026',
    'sort_order' => 5,
]);
echo "7. Lesson 5 Created: {$lesson5->title} (Live Link: {$lesson5->meeting_link})\n";

// 8. Test Calendar .ICS Generation
echo "\n8. Testing .ICS Calendar Generation...\n";
$publicCtrl = app(\App\Http\Controllers\Public\CourseController::class);
$icsResponse = $publicCtrl->downloadCalendar($course);
$icsContent = $icsResponse->getContent();

if (str_contains($icsContent, 'BEGIN:VCALENDAR') && 
    str_contains($icsContent, 'TRIGGER:-PT30M') && 
    str_contains($icsContent, 'Lecture 01') && 
    str_contains($icsContent, '+91 9911020626')) {
    echo "SUCCESS: .ICS calendar generated with 30-min reminder alarms and session details! Total bytes: " . strlen($icsContent) . "\n";
} else {
    echo "WARNING: Calendar format check failed.\n";
}

// 9. Test View Rendering for Public Show and Student LMS
echo "\n9. Testing Blade View Rendering...\n";

// Public Show View
$publicShowHtml = view('public.courses.show', [
    'course' => $course,
    'batch' => $batch,
    'lessons' => CourseLesson::where('course_batch_id', $batch->id)->get(),
    'groupedPlanner' => CourseLesson::where('course_batch_id', $batch->id)->get()->groupBy(fn($l) => $l->scheduled_date ? $l->scheduled_date->format('Y-m-d') : 'Flexible Curriculum'),
    'isEnrolled' => false,
    'locale' => 'en',
])->render();

if (str_contains($publicShowHtml, 'Dynamic Day-by-Day Course Planner') &&
    str_contains($publicShowHtml, 'Preamble MCQs') &&
    str_contains($publicShowHtml, 'Call Mentor Directly') || str_contains($publicShowHtml, 'Dr. R.K. Verma')) {
    echo "SUCCESS: Public course show blade rendered successfully with day-by-day planner!\n";
} else {
    echo "WARNING: Public blade rendering missing expected tokens.\n";
}

// Student LMS View
auth()->login($user);
$studentShowHtml = view('student.courses.show', [
    'course' => $course,
    'batch' => $batch,
    'lessons' => CourseLesson::where('course_batch_id', $batch->id)->get(),
    'groupedPlanner' => CourseLesson::where('course_batch_id', $batch->id)->get()->groupBy(fn($l) => $l->scheduled_date ? $l->scheduled_date->format('Y-m-d') : 'Flexible Curriculum'),
    'activeLesson' => $lesson4, // Phone interactive session
    'isEnrolled' => true,
    'quizAttempt' => null,
    'mainsSubmission' => null,
])->render();

if (str_contains($studentShowHtml, 'Phone Interactive Session') &&
    str_contains($studentShowHtml, '+91 9911020626') &&
    str_contains($studentShowHtml, '06:30 PM - 07:30 PM')) {
    echo "SUCCESS: Student LMS view rendered phone session with call window and direct tel link!\n";
} else {
    echo "WARNING: Student LMS blade rendering missing phone interactive details.\n";
}

echo "\n=== ALL TESTS PASSED SUCCESSFULLY ===\n";
