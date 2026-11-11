<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Centre;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CentreController extends Controller
{
    public function index(Request $request): View
    {
        $query = Centre::query()->where('is_active', true)->orderBy('sort_order');

        if ($request->filled('city')) {
            $query->where('city', $request->query('city'));
        }

        $centres = $query->get();
        $cities = Centre::where('is_active', true)->distinct()->pluck('city')->sort();

        return view('public.centres.index', compact('centres', 'cities'));
    }

    public function show(string $slug): View
    {
        $centre = Centre::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $batches = $centre->batches()->with('course')->get();

        return view('public.centres.show', compact('centre', 'batches'));
    }
}
