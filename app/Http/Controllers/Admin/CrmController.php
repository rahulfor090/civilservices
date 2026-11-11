<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Centre;
use App\Models\Lead;
use App\Models\User;
use App\Services\CrmPipelineService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmController extends Controller
{
    public function __construct(
        protected CrmPipelineService $crmService
    ) {}

    public function index(Request $request): View
    {
        $query = Lead::with(['assignedCounsellor', 'centre', 'activities'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->query('centre_id'));
        }

        if ($request->filled('source')) {
            $query->where('source', $request->query('source'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $leads = $query->paginate(20);

        // Stage counts
        $stageCounts = [
            'all' => Lead::count(),
            'new' => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'qualified' => Lead::where('status', 'qualified')->count(),
            'counselling_scheduled' => Lead::where('status', 'counselling_scheduled')->count(),
            'won' => Lead::where('status', 'won')->count(),
            'lost' => Lead::where('status', 'lost')->count(),
        ];

        $counsellors = User::all();
        $centres = Centre::where('is_active', true)->get();

        return view('admin.crm.index', compact('leads', 'stageCounts', 'counsellors', 'centres'));
    }

    public function updateStage(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,qualified,counselling_scheduled,won,lost',
            'notes' => 'nullable|string|max:1000',
            'follow_up_at' => 'nullable|date',
        ]);

        $followUp = !empty($validated['follow_up_at']) ? Carbon::parse($validated['follow_up_at']) : null;

        $this->crmService->transitionStage(
            $lead,
            $validated['status'],
            $validated['notes'] ?? null,
            $followUp,
            auth()->user()
        );

        return redirect()->back()->with('success', "Lead #{$lead->id} updated to '{$validated['status']}'.");
    }

    public function assign(Request $request, Lead $lead): RedirectResponse
    {
        $request->validate(['assigned_to' => 'required|exists:users,id']);
        $counsellor = User::findOrFail($request->input('assigned_to'));

        $this->crmService->assignLead($lead, $counsellor, auth()->user());

        return redirect()->back()->with('success', "Lead assigned to {$counsellor->name}.");
    }
}
