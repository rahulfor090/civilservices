<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\MainsSubmission;
use App\Models\Role;
use App\Models\User;
use App\Services\MainsEvaluationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class MainsEvaluationController extends Controller
{
    public function __construct(
        protected MainsEvaluationService $evaluationService
    ) {}

    public function index(Request $request): View
    {
        $query = MainsSubmission::with(['user', 'test', 'evaluator'])->latest('submitted_at');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $submissions = $query->paginate(20)->withQueryString();

        $evaluators = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['evaluator', 'super_admin', 'editor']);
        })->get();

        foreach ($evaluators as $ev) {
            $ev->assigned_copies_count = MainsSubmission::where('evaluator_id', $ev->id)->count();
            $ev->pending_copies_count = MainsSubmission::where('evaluator_id', $ev->id)->whereIn('status', ['submitted', 'assigned'])->count();
            $ev->completed_copies_count = MainsSubmission::where('evaluator_id', $ev->id)->where('status', 'evaluated')->count();
        }

        $stats = [
            'total_submissions' => MainsSubmission::count(),
            'pending_assignment' => MainsSubmission::where('status', 'submitted')->count(),
            'under_evaluation' => MainsSubmission::where('status', 'assigned')->count(),
            'evaluated' => MainsSubmission::where('status', 'evaluated')->count(),
            'total_evaluators' => $evaluators->count(),
        ];

        $activeTab = $request->query('tab', 'queue');

        return view('admin.mains.index', compact('submissions', 'evaluators', 'stats', 'activeTab'));
    }

    public function show(MainsSubmission $submission): View
    {
        $submission->load(['user', 'test.questions', 'scores', 'evaluator']);
        $evaluators = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['evaluator', 'super_admin', 'editor']);
        })->get();

        return view('admin.mains.evaluate', compact('submission', 'evaluators'));
    }

    public function assign(Request $request, MainsSubmission $submission): RedirectResponse
    {
        $request->validate(['evaluator_id' => 'required|exists:users,id']);
        $evaluator = User::findOrFail($request->input('evaluator_id'));

        $this->evaluationService->assignEvaluator($submission, $evaluator);

        return redirect()->back()->with('success', "Submission #{$submission->id} successfully assigned to {$evaluator->name}.");
    }

    public function evaluate(Request $request, MainsSubmission $submission): RedirectResponse
    {
        $request->validate([
            'rubrics' => 'required|array',
            'rubrics.*.question_id' => 'required|exists:mains_test_questions,id',
            'rubrics.*.intro_score' => 'required|numeric|min:0',
            'rubrics.*.content_score' => 'required|numeric|min:0',
            'rubrics.*.structure_score' => 'required|numeric|min:0',
            'rubrics.*.conclusion_score' => 'required|numeric|min:0',
            'rubrics.*.comments' => 'nullable|string|max:1000',
            'general_remarks' => 'nullable|string|max:2000',
            'evaluated_copy_pdf' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $evaluatedPdfPath = null;
        if ($request->hasFile('evaluated_copy_pdf')) {
            $evaluatedPdfPath = $request->file('evaluated_copy_pdf')->store('evaluated_copies', 'public');
        }

        $this->evaluationService->evaluateCopy(
            $submission,
            $request->input('rubrics'),
            $evaluatedPdfPath,
            $request->input('general_remarks')
        );

        return redirect()->route('admin.mains.index')
            ->with('success', "Answer copy #{$submission->id} has been evaluated and scorecard published!");
    }

    public function storeEvaluator(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        $evaluatorRole = Role::where('slug', 'evaluator')->first();
        if (!$evaluatorRole) {
            $evaluatorRole = Role::create([
                'name' => 'Faculty Evaluator',
                'slug' => 'evaluator',
                'description' => 'Evaluates student UPSC Mains answer copies with structured rubrics',
                'is_system' => true,
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? '9876543210',
            'country_code' => '+91',
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            'locale' => 'en',
            'email_verified_at' => now(),
        ]);

        $user->roles()->attach($evaluatorRole->id);

        AuditLog::record(auth()->user(), 'mains', 'create_evaluator', $user, null, [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        return redirect()->route('admin.mains.index', ['tab' => 'evaluators'])
            ->with('success', "Faculty Evaluator '{$user->name}' created successfully.");
    }

    public function destroyEvaluator(User $user): RedirectResponse
    {
        $evaluatorRole = Role::where('slug', 'evaluator')->first();
        if ($evaluatorRole) {
            $user->roles()->detach($evaluatorRole->id);
        }

        AuditLog::record(auth()->user(), 'mains', 'remove_evaluator_role', $user, null, ['user_id' => $user->id]);

        return redirect()->route('admin.mains.index', ['tab' => 'evaluators'])
            ->with('success', "Evaluator role removed for {$user->name}.");
    }
}
