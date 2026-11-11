@extends('layouts.admin')

@section('title', 'Add New Centre')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.centres.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">&larr; Back to All Centres</a>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Add New Classroom Centre</h1>
        <p class="text-xs text-slate-500 mt-1">Create a new offline study centre with its street address, contact helpline, and Google Maps link.</p>
    </div>

    <form action="{{ route('admin.centres.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('admin.centres._form')
    </form>
</div>
@endsection

