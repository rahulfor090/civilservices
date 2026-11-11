<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MainsSubmission;
use App\Models\MainsTest;
use App\Services\MainsEvaluationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MainsSubmissionController extends Controller
{
    public function __construct(
        protected MainsEvaluationService $evaluationService
    ) {}

    public function index(): View
    {
        $tests = MainsTest::where('status', 'active')->withCount('questions')->get();
        $submissions = MainsSubmission::where('user_id', auth()->id())
            ->with(['test', 'evaluator'])
            ->orderByDesc('submitted_at')
            ->get();

        return view('student.mains.index', compact('tests', 'submissions'));
    }

    public function show(MainsTest $mainsTest): View
    {
        $mainsTest->load('questions');
        $existingSubmission = MainsSubmission::where('user_id', auth()->id())
            ->where('mains_test_id', $mainsTest->id)
            ->latest()
            ->first();

        return view('student.mains.show', compact('mainsTest', 'existingSubmission'));
    }

    public function store(Request $request, MainsTest $mainsTest): RedirectResponse
    {
        $request->validate([
            'answer_copy_pdf' => 'required|file|mimes:pdf|max:20480', // up to 20MB
            'candidate_roll_no' => 'nullable|string|max:50',
        ]);

        $path = $request->file('answer_copy_pdf')->store('mains_copies', 'public');

        $submission = $this->evaluationService->submitCopy(
            auth()->user(),
            $mainsTest,
            $path,
            $request->input('candidate_roll_no')
        );

        return redirect()->route('student.mains.index')
            ->with('success', "Answer copy #{$submission->id} for '{$mainsTest->title}' has been submitted successfully for expert evaluation!");
    }

    public function evaluated(MainsSubmission $submission): View
    {
        // Enforce ownership
        if ($submission->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized access to this answer copy evaluation.');
        }

        $submission->load(['test.questions', 'scores.question', 'evaluator']);

        return view('student.mains.evaluated', compact('submission'));
    }
}
