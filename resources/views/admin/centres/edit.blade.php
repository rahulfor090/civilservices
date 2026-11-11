@extends('layouts.admin')

@section('title', 'Edit Centre - ' . $centre->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.centres.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">&larr; Back to All Centres</a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Edit Centre: {{ $centre->name }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-slate-500">
                <span>{{ $centre->city }}, {{ $centre->state }}</span>
                <span>&bull;</span>
                <span class="font-mono text-slate-400">/centres/{{ $centre->slug }}</span>
            </div>
        </div>
        @if($centre->is_active)
            <a href="{{ route('centres.show', $centre->slug) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1.5 transition self-start sm:self-center">
                <span>View Public Page</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        @endif
    </div>

    <form action="{{ route('admin.centres.update', $centre) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.centres._form')
    </form>
</div>
@endsection

