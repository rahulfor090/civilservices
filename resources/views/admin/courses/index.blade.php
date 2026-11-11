@extends('layouts.admin')

@section('title', 'Courses & LMS Manager')

@section('content')
<div class="space-y-6">
    <!-- Header with Stats & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Courses & LMS Management</h1>
            <p class="text-xs text-slate-500 mt-1">Author video courses, classroom batches, lecture notes, and student curriculum enrollments.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.create') }}" 
               class="px-4 py-2.5 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-bold shadow-md flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create New Course</span>
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Courses</div>
            <div class="text-2xl font-black text-brand-navy mt-1">{{ $stats['total_courses'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Published & Live</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['published_courses'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Active Batches</div>
            <div class="text-2xl font-black text-indigo-700 mt-1">{{ $stats['total_batches'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Total Enrolled Students</div>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ $stats['total_enrollments'] }}</div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.courses.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search courses by title, slug..." 
                       class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
            </div>

            <div>
                <select name="target_exam" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    <option value="">All Target Exams</option>
                    <option value="upsc_cse" {{ request('target_exam') == 'upsc_cse' ? 'selected' : '' }}>UPSC Civil Services (IAS/IPS)</option>
                    <option value="uppcs" {{ request('target_exam') == 'uppcs' ? 'selected' : '' }}>UPPSC (UP Civil Services)</option>
                    <option value="bpsc" {{ request('target_exam') == 'bpsc' ? 'selected' : '' }}>BPSC (Bihar PCS)</option>
                    <option value="ras" {{ request('target_exam') == 'ras' ? 'selected' : '' }}>RAS (Rajasthan PCS)</option>
                    <option value="mppsc" {{ request('target_exam') == 'mppsc' ? 'selected' : '' }}>MPPSC (Madhya Pradesh)</option>
                </select>
            </div>

            <div>
                <select name="status" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published (Active)</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'target_exam', 'status', 'language_id']))
                    <a href="{{ route('admin.courses.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Courses Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Course Title & Slug</th>
                        <th class="py-3.5 px-4">Target Exam</th>
                        <th class="py-3.5 px-4">Level</th>
                        <th class="py-3.5 px-4">Batches / Lessons</th>
                        <th class="py-3.5 px-4">Fee / MRP</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($courses as $course)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                    <span>{{ $course->title }}</span>
                                    @if($course->is_featured)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Featured</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">/courses/{{ $course->slug }}</div>
                                <div class="text-[11px] text-slate-500 mt-1 line-clamp-1">{{ $course->excerpt }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-800 uppercase">
                                    {{ str_replace('_', ' ', $course->target_exam) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-1 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 capitalize">
                                    {{ $course->level }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $course->batches_count }} Batches</div>
                                <div class="text-[11px] text-slate-500">{{ $course->batches->flatMap->lessons->count() }} Lessons &bull; {{ $course->enrollments_count }} Students</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 font-mono">
                                    ₹{{ number_format($course->price, 0) }}
                                </div>
                                @if($course->mrp > $course->price)
                                    <div class="text-[10px] text-slate-400 line-through font-mono">₹{{ number_format($course->mrp, 0) }}</div>
                                @endif
                                <div class="text-[10px] text-slate-400">{{ $course->validity_days }} Days</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($course->status === 'published')
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Live
                                    </span>
                                @elseif($course->status === 'draft')
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Draft
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Archived
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.courses.edit', $course) }}" 
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition-colors">
                                        Edit & Batches
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                <p class="text-base font-semibold text-slate-600">No courses found matching your criteria</p>
                                <p class="mt-1">Click "Create New Course" above to author your first course curriculum.</p>
                                <a href="{{ route('admin.courses.create') }}" class="inline-block mt-4 px-4 py-2 rounded-xl bg-brand-navy text-white text-xs font-semibold">
                                    + Add New Course
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($courses->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
