<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseLesson;
use App\Models\Language;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::with(['language', 'batches.lessons'])
            ->withCount(['batches', 'enrollments']);

        if ($request->filled('target_exam')) {
            $query->where('target_exam', $request->target_exam);
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
                  ->orWhere('slug', 'like', $search)
                  ->orWhere('excerpt', 'like', $search);
            });
        }

        $courses = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_courses' => Course::count(),
            'published_courses' => Course::where('status', 'published')->count(),
            'total_batches' => CourseBatch::count(),
            'total_enrollments' => \App\Models\Enrollment::count(),
        ];

        $languages = Language::where('is_active', true)->get();

        return view('admin.courses.index', compact('courses', 'stats', 'languages'));
    }

    public function create(): View
    {
        $languages = Language::where('is_active', true)->get();
        $centres = Centre::where('is_active', true)->get();

        return view('admin.courses.create', compact('languages', 'centres'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:191|unique:courses,slug',
            'target_exam' => 'required|string|max:50',
            'level' => 'required|in:foundation,prelims,mains,interview,optional',
            'price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'validity_days' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'thumbnail' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string',
            'description' => 'nullable|string',
            // Optional initial batch
            'create_initial_batch' => 'nullable|boolean',
            'batch_code' => 'nullable|string|max:50',
            'batch_title' => 'nullable|string|max:255',
            'delivery_mode' => 'nullable|in:online,offline,hybrid',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        // Ensure uniqueness
        if (Course::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $course = Course::create([
            'language_id' => $validated['language_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'target_exam' => $validated['target_exam'],
            'level' => $validated['level'],
            'price' => $validated['price'],
            'mrp' => $validated['mrp'] ?? $validated['price'],
            'validity_days' => $validated['validity_days'],
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
            'thumbnail' => $validated['thumbnail'] ?? null,
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['description'] ?? ''), 160),
            'description' => $validated['description'] ?? '',
        ]);

        // Auto-create initial batch if requested
        if ($request->boolean('create_initial_batch') || !empty($validated['batch_title'])) {
            $rawCode = !empty($validated['batch_code']) 
                ? strtoupper(Str::slug($validated['batch_code'])) 
                : 'BATCH-' . strtoupper(Str::slug($course->target_exam)) . '-' . date('y') . '-' . strtoupper(Str::random(4));
            
            $batchCode = $rawCode;
            $counter = 1;
            while (CourseBatch::where('batch_code', $batchCode)->exists()) {
                $batchCode = $rawCode . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
                $counter++;
            }

            CourseBatch::create([
                'course_id' => $course->id,
                'batch_code' => $batchCode,
                'title' => $validated['batch_title'] ?? ($course->title . ' - Regular Batch'),
                'delivery_mode' => $validated['delivery_mode'] ?? 'online',
                'start_date' => now()->addDays(7),
                'class_timing' => '09:00 AM - 11:30 AM',
                'is_enrolment_open' => true,
            ]);
        }

        AuditLog::record(auth()->user(), 'course', 'create_course', $course, null, $course->toArray());

        return redirect()->route('admin.courses.edit', $course)->with('success', 'Course created successfully. You can now manage batches and curriculum lessons.');
    }

    public function edit(Course $course): View
    {
        $course->load(['language', 'batches.lessons.quiz.language', 'batches.lessons.mainsTest', 'batches.centre']);
        $languages = Language::where('is_active', true)->get();
        $centres = Centre::where('is_active', true)->get();
        $quizzes = Quiz::with('language')->latest('id')->get();
        $mainsTests = \App\Models\MainsTest::latest('id')->get();

        return view('admin.courses.edit', compact('course', 'languages', 'centres', 'quizzes', 'mainsTests'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $before = $course->toArray();

        $validated = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:191|unique:courses,slug,' . $course->id,
            'target_exam' => 'required|string|max:50',
            'level' => 'required|in:foundation,prelims,mains,interview,optional',
            'price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'validity_days' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'thumbnail' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['mrp'] = $validated['mrp'] ?? $validated['price'];

        $course->update($validated);

        AuditLog::record(auth()->user(), 'course', 'update_course', $course, $before, $course->toArray());

        return redirect()->route('admin.courses.edit', $course)->with('success', 'Course details updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $before = $course->toArray();
        $course->delete();

        AuditLog::record(auth()->user(), 'course', 'delete_course', null, $before, null);

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }

    public function storeBatch(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'batch_code' => 'required|string|max:50|unique:course_batches,batch_code',
            'title' => 'required|string|max:255',
            'delivery_mode' => 'required|in:online,offline,hybrid',
            'centre_id' => 'nullable|exists:centres,id',
            'centre_name' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'class_timing' => 'nullable|string|max:100',
            'faculty_names' => 'nullable|string|max:255',
            'is_enrolment_open' => 'boolean',
        ]);

        $validated['course_id'] = $course->id;
        $validated['is_enrolment_open'] = $request->boolean('is_enrolment_open');

        $batch = CourseBatch::create($validated);

        AuditLog::record(auth()->user(), 'course', 'create_batch', $batch, null, $batch->toArray());

        return redirect()->route('admin.courses.edit', $course)->with('success', "Batch '{$batch->title}' created successfully.");
    }

    public function destroyBatch(CourseBatch $batch): RedirectResponse
    {
        $course = $batch->course;
        $batchTitle = $batch->title;
        $batch->delete();

        AuditLog::record(auth()->user(), 'course', 'delete_batch', null, ['title' => $batchTitle], null);

        return redirect()->route('admin.courses.edit', $course)->with('success', "Batch '{$batchTitle}' removed.");
    }

    public function storeLesson(Request $request, CourseBatch $batch): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'activity_type' => 'required|in:video,live,quiz,mains,phone_session,test_series,pdf',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|string',
            'unlock_at' => 'nullable|date',
            'video_url' => 'nullable|string|max:255',
            'notes_pdf_url' => 'nullable|string|max:255',
            'quiz_id' => 'nullable|exists:quizzes,id',
            'mains_test_id' => 'nullable|exists:mains_tests,id',
            'mains_question_text' => 'nullable|string',
            'mains_marks' => 'nullable|integer|min:1',
            'mains_word_limit' => 'nullable|integer|min:50',
            'mentor_name' => 'nullable|string|max:150',
            'mentor_phone' => 'nullable|string|max:50',
            'call_window' => 'nullable|string|max:100',
            'session_instructions' => 'nullable|string',
            'meeting_platform' => 'nullable|string|max:50',
            'meeting_link' => 'nullable|string|max:500',
            'meeting_password' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'alert_minutes_before' => 'nullable|integer|min:0|max:1440',
            'sort_order' => 'nullable|integer|min:1',
            'is_free_preview' => 'boolean',
        ]);

        $validated['course_batch_id'] = $batch->id;
        $validated['lesson_type'] = in_array($validated['activity_type'], ['video', 'pdf', 'live', 'quiz']) 
            ? $validated['activity_type'] 
            : 'video';
        $validated['sort_order'] = $validated['sort_order'] ?? ($batch->lessons()->count() + 1);
        $validated['is_free_preview'] = $request->boolean('is_free_preview');
        $validated['alert_minutes_before'] = $validated['alert_minutes_before'] ?? 30;

        // Calculate exact scheduled_at timestamp
        if (!empty($validated['scheduled_date'])) {
            $time = !empty($validated['scheduled_time']) ? $validated['scheduled_time'] : '00:00:00';
            $validated['scheduled_at'] = \Illuminate\Support\Carbon::parse($validated['scheduled_date'] . ' ' . $time);
        }

        if ($validated['activity_type'] !== 'quiz') {
            $validated['quiz_id'] = null;
        }

        if ($validated['activity_type'] !== 'mains') {
            $validated['mains_test_id'] = null;
        }

        $lesson = CourseLesson::create($validated);

        AuditLog::record(auth()->user(), 'course', 'create_lesson', $lesson, null, $lesson->toArray());

        return redirect()->route('admin.courses.edit', $batch->course)->with('success', "Lesson/Session '{$lesson->title}' added to {$batch->batch_code}.");
    }

    public function updateLesson(Request $request, CourseLesson $lesson): RedirectResponse
    {
        $before = $lesson->toArray();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'activity_type' => 'required|in:video,live,quiz,mains,phone_session,test_series,pdf',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|string',
            'unlock_at' => 'nullable|date',
            'video_url' => 'nullable|string|max:255',
            'notes_pdf_url' => 'nullable|string|max:255',
            'quiz_id' => 'nullable|exists:quizzes,id',
            'mains_test_id' => 'nullable|exists:mains_tests,id',
            'mains_question_text' => 'nullable|string',
            'mains_marks' => 'nullable|integer|min:1',
            'mains_word_limit' => 'nullable|integer|min:50',
            'mentor_name' => 'nullable|string|max:150',
            'mentor_phone' => 'nullable|string|max:50',
            'call_window' => 'nullable|string|max:100',
            'session_instructions' => 'nullable|string',
            'meeting_platform' => 'nullable|string|max:50',
            'meeting_link' => 'nullable|string|max:500',
            'meeting_password' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'alert_minutes_before' => 'nullable|integer|min:0|max:1440',
            'sort_order' => 'nullable|integer|min:1',
            'is_free_preview' => 'boolean',
        ]);

        $validated['lesson_type'] = in_array($validated['activity_type'], ['video', 'pdf', 'live', 'quiz']) 
            ? $validated['activity_type'] 
            : 'video';
        $validated['is_free_preview'] = $request->boolean('is_free_preview');
        $validated['alert_minutes_before'] = $validated['alert_minutes_before'] ?? 30;

        if (!empty($validated['scheduled_date'])) {
            $time = !empty($validated['scheduled_time']) ? $validated['scheduled_time'] : '00:00:00';
            $validated['scheduled_at'] = \Illuminate\Support\Carbon::parse($validated['scheduled_date'] . ' ' . $time);
        } else {
            $validated['scheduled_at'] = null;
        }

        if ($validated['activity_type'] !== 'quiz') {
            $validated['quiz_id'] = null;
        }

        if ($validated['activity_type'] !== 'mains') {
            $validated['mains_test_id'] = null;
        }

        $lesson->update($validated);

        AuditLog::record(auth()->user(), 'course', 'update_lesson', $lesson, $before, $lesson->toArray());

        return redirect()->route('admin.courses.edit', $lesson->batch->course)->with('success', "Lesson '{$lesson->title}' updated.");
    }

    public function destroyLesson(CourseLesson $lesson): RedirectResponse
    {
        $course = $lesson->batch->course;
        $title = $lesson->title;
        $lesson->delete();

        AuditLog::record(auth()->user(), 'course', 'delete_lesson', null, ['title' => $title], null);

        return redirect()->route('admin.courses.edit', $course)->with('success', "Lesson '{$title}' removed.");
    }
}
