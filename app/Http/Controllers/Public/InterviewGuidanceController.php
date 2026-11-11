<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Centre;
use App\Models\InterviewGuidanceBooking;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InterviewGuidanceController extends Controller
{
    public function create(): View
    {
        $centres = Centre::where('is_active', true)->orderBy('sort_order')->get();
        return view('public.interview.guidance', compact('centres'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'upsc_roll_number' => 'required|string|max:50',
            'optional_subject' => 'required|string|max:100',
            'medium' => 'required|in:en,hi',
            'centre_id' => 'nullable|exists:centres,id',
            'interview_slot_date' => 'nullable|date|after_or_equal:today',
            'daf_file' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        $dafPath = null;
        if ($request->hasFile('daf_file')) {
            $dafPath = $request->file('daf_file')->store('daf_uploads', 'public');
        }

        InterviewGuidanceBooking::create([
            'user_id' => auth()->id(),
            'full_name' => $validated['full_name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'upsc_roll_number' => $validated['upsc_roll_number'],
            'optional_subject' => $validated['optional_subject'],
            'daf_file_path' => $dafPath,
            'medium' => $validated['medium'],
            'interview_slot_date' => $validated['interview_slot_date'] ?? null,
            'centre_id' => $validated['centre_id'] ?? null,
            'status' => 'registered',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Sync into CRM
        Lead::create([
            'name' => $validated['full_name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'centre_id' => $validated['centre_id'] ?? null,
            'interest' => 'Interview Guidance Programme (IGP)',
            'source' => 'interview_guidance',
            'source_page' => url()->current(),
            'message' => "UPSC Roll No: {$validated['upsc_roll_number']}. Optional: {$validated['optional_subject']}.",
            'status' => 'new',
            'consent' => true,
        ]);

        return redirect()->back()->with('success', 'Your UPSC Interview Guidance & Mock Interview session has been registered successfully! Our Board Secretariat will reach out to confirm your board panel date.');
    }
}
