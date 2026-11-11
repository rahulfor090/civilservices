@extends('layouts.app')

@section('title', 'Platform Reports & Analytics Hub | Admin Console')

@section('content')
<div class="bg-gray-100 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-navy-900 uppercase tracking-widest bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded">Executive Intelligence</span>
                <h1 class="text-2xl font-extrabold text-gray-900 mt-2">Reports, Analytics & Data Exports</h1>
                <p class="text-xs text-gray-500 mt-1">Export raw academic data, candidate attempt scorecards, and admissions CRM logs for external audit.</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 shadow-sm">
                    ← Admin Dashboard
                </a>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs font-bold text-gray-500 uppercase">Total Leads</span>
                <span class="block text-2xl font-extrabold text-gray-900 mt-1">{{ $metrics['total_leads'] }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs font-bold text-emerald-600 uppercase">Converted (Won)</span>
                <span class="block text-2xl font-extrabold text-emerald-700 mt-1">{{ $metrics['converted_leads'] }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs font-bold text-blue-600 uppercase">Quiz Attempts</span>
                <span class="block text-2xl font-extrabold text-blue-700 mt-1">{{ $metrics['quiz_attempts'] }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs font-bold text-purple-600 uppercase">Mains Copies</span>
                <span class="block text-2xl font-extrabold text-purple-700 mt-1">{{ $metrics['mains_submissions'] }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs font-bold text-amber-600 uppercase">Active Students</span>
                <span class="block text-2xl font-extrabold text-amber-700 mt-1">{{ $metrics['active_students'] }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs font-bold text-indigo-600 uppercase">Courses</span>
                <span class="block text-2xl font-extrabold text-indigo-700 mt-1">{{ $metrics['courses_offered'] }}</span>
            </div>
        </div>

        <!-- One-Click Export Hub -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900">Download Raw CSV Datasets</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Leads CSV Export -->
                <div class="border border-gray-200 rounded-xl p-5 hover:border-primary-500 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Admissions Leads & Counselling Data</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Complete raw inquiries with UTM attribution, target exams, centres, and sales status history.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <a href="{{ route('admin.reports.export.leads') }}" class="inline-flex items-center px-4 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-lg text-xs font-bold shadow-sm transition-colors w-full justify-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Export Leads (CSV)
                        </a>
                    </div>
                </div>

                <!-- Quiz Performance Export -->
                <div class="border border-gray-200 rounded-xl p-5 hover:border-primary-500 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Quiz & Test Performance Records</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Individual candidate test attempts, net scores, negative penalties, accuracy percentages, and duration.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <a href="{{ route('admin.reports.export.quizzes') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-sm transition-colors w-full justify-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Export Quiz Scores (CSV)
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
