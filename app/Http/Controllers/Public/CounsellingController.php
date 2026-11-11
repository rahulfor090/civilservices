<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Centre;
use App\Models\CounsellingBooking;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CounsellingController extends Controller
{
    public function create(): View
    {
        $centres = Centre::where('is_active', true)->orderBy('sort_order')->get();
        return view('public.counselling.book', compact('centres'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'centre_id' => 'nullable|exists:centres,id',
            'medium' => 'required|in:en,hi',
            'call_time_window' => 'required|in:morning,afternoon,evening',
            'target_year' => 'required|in:2025,2026,2027',
            'interest_category' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking = CounsellingBooking::create([
            'user_id' => auth()->id(),
            'full_name' => $validated['full_name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
            'centre_id' => $validated['centre_id'] ?? null,
            'medium' => $validated['medium'],
            'call_time_window' => $validated['call_time_window'],
            'target_year' => $validated['target_year'],
            'interest_category' => $validated['interest_category'] ?? 'UPSC Foundation',
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Automatically sync into CRM lead pipeline
        Lead::create([
            'name' => $validated['full_name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?: ($validated['mobile'] . '@lead.civilservicegurukul.com'),
            'centre_id' => $validated['centre_id'] ?? null,
            'interest' => $validated['interest_category'] ?? 'UPSC Foundation',
            'source' => 'counselling_booking',
            'source_page' => url()->current(),
            'message' => "Requested time: {$validated['call_time_window']}. Target: {$validated['target_year']}. Notes: " . ($validated['notes'] ?? 'None'),
            'status' => 'new',
            'consent' => true,
        ]);

        return redirect()->back()->with('success', 'Your 1-on-1 expert counselling session has been booked! Our senior academic mentor will call you during your selected time window.');
    }
}
