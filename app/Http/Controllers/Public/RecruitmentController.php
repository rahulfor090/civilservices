<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\RecruitmentApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecruitmentController extends Controller
{
    public function teacher(): View
    {
        return view('public.recruitment.teacher');
    }

    public function storeTeacher(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'subject_specialization' => 'required|string|max:150',
            'experience_years' => 'required|integer|min:0|max:50',
            'resume_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        $resumePath = null;
        if ($request->hasFile('resume_file')) {
            $resumePath = $request->file('resume_file')->store('resumes', 'public');
        }

        RecruitmentApplication::create([
            'full_name' => $validated['full_name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'role_applied' => 'faculty',
            'subject_specialization' => $validated['subject_specialization'],
            'experience_years' => $validated['experience_years'],
            'resume_path' => $resumePath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'applied',
        ]);

        return redirect()->back()->with('success', 'Your Faculty Application has been submitted to the Academic Dean. We will review your profile and contact you for a demo lecture.');
    }

    public function writer(): View
    {
        return view('public.recruitment.writer');
    }

    public function storeWriter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'subject_specialization' => 'required|string|max:150',
            'experience_years' => 'required|integer|min:0|max:50',
            'resume_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        $resumePath = null;
        if ($request->hasFile('resume_file')) {
            $resumePath = $request->file('resume_file')->store('resumes', 'public');
        }

        RecruitmentApplication::create([
            'full_name' => $validated['full_name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'role_applied' => 'content_writer',
            'subject_specialization' => $validated['subject_specialization'],
            'experience_years' => $validated['experience_years'],
            'resume_path' => $resumePath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'applied',
        ]);

        return redirect()->back()->with('success', 'Your Content Writer application has been submitted to the Editorial Board. We will review your writing samples.');
    }
}
