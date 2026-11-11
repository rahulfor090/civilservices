<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // 1. Honeypot check for bots
        if (!empty($request->input('hp_extra'))) {
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Thank you for your interest!']);
            }
            return back()->with('success', 'Your enquiry has been received.');
        }

        // 2. Validate input
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|min:10|max:15',
            'language_id' => 'nullable|exists:languages,id',
            'interest' => 'nullable|string|max:100',
            'source_page' => 'nullable|string|max:255',
            'consent' => 'accepted',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $fullName = trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? ''));

        // 3. Extract UTM tracking attributes
        $utmParams = [
            'utm_source' => $request->input('utm_source', 'direct'),
            'utm_medium' => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
            'utm_content' => $request->input('utm_content'),
        ];

        // 4. Create or update Lead in CRM
        $lead = Lead::create([
            'uuid' => (string) Str::uuid(),
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'mobile' => $validated['phone'],
            'language_id' => $validated['language_id'] ?? null,
            'interest' => $validated['interest'] ?? 'UPSC Civil Services',
            'source' => $request->input('source', 'website_form'),
            'source_page' => $validated['source_page'] ?? $request->header('referer'),
            'utm_json' => $utmParams,
            'status' => 'new',
            'consent' => true,
        ]);

        // 5. Log activity
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'status_change',
            'outcome' => 'Lead captured from ' . ($lead->source_page ?? 'website'),
            'notes' => 'Automatic submission recorded with marketing attribution.',
            'created_at' => now(),
        ]);

        $message = app()->getLocale() === 'hi' 
            ? 'धन्यवाद! आपकी काउंसलिंग पूछताछ सफलतापूर्वक दर्ज कर ली गई है। हमारी टीम शीघ्र संपर्क करेगी।' 
            : 'Thank you! Your counselling request has been registered. Our academic mentor will contact you shortly.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'lead_id' => $lead->uuid,
            ]);
        }

        return back()->with('success', $message);
    }
}
