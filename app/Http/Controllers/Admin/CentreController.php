<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CentreController extends Controller
{
    public function index(Request $request): View
    {
        $query = Centre::query()->orderBy('sort_order')->orderBy('id');

        if ($request->filled('search')) {
            $search = '%' . $request->query('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('city', 'like', $search)
                    ->orWhere('state', 'like', $search)
                    ->orWhere('address', 'like', $search)
                    ->orWhere('pincode', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            if ($request->query('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->query('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $centres = $query->paginate(20)->withQueryString();

        $stats = [
            'total_centres' => Centre::count(),
            'active_centres' => Centre::where('is_active', true)->count(),
            'inactive_centres' => Centre::where('is_active', false)->count(),
            'cities_count' => Centre::where('is_active', true)->distinct('city')->count('city'),
        ];

        return view('admin.centres.index', compact('centres', 'stats'));
    }

    public function create(): View
    {
        $nextSortOrder = (int) Centre::max('sort_order') + 1;

        return view('admin.centres.create', compact('nextSortOrder'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAndPrepare($request);

        $centre = Centre::create($data);

        AuditLog::record(auth()->user(), 'centre', 'create_centre', $centre, null, $centre->toArray());

        return redirect()
            ->route('admin.centres.index')
            ->with('success', "Centre '{$centre->name}' created successfully.");
    }

    public function edit(Centre $centre): View
    {
        return view('admin.centres.edit', compact('centre'));
    }

    public function update(Request $request, Centre $centre): RedirectResponse
    {
        $before = $centre->toArray();
        $data = $this->validateAndPrepare($request, $centre);

        $centre->update($data);

        AuditLog::record(auth()->user(), 'centre', 'update_centre', $centre, $before, $centre->toArray());

        return redirect()
            ->route('admin.centres.index')
            ->with('success', "Centre '{$centre->name}' updated successfully.");
    }

    public function destroy(Centre $centre): RedirectResponse
    {
        $before = $centre->toArray();
        $name = $centre->name;

        $centre->delete();

        AuditLog::record(auth()->user(), 'centre', 'delete_centre', null, $before, null);

        return redirect()
            ->route('admin.centres.index')
            ->with('success', "Centre '{$name}' deleted successfully.");
    }

    /**
     * Validate request inputs and normalize slug, facilities, and boolean flags.
     */
    private function validateAndPrepare(Request $request, ?Centre $centre = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'slug' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('centres', 'slug')->ignore($centre?->id),
            ],
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'address' => 'required|string|max:1000',
            'pincode' => 'nullable|string|max:20',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:191',
            'google_maps_url' => 'nullable|url|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'facilities_text' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $rawSlug = trim((string) ($validated['slug'] ?? ''));
        $slugSource = $rawSlug !== '' ? $rawSlug : ($centre?->slug ?? $validated['name']);
        $slug = Str::slug($slugSource);

        if ($slug === '') {
            $slug = 'centre-' . time();
        }

        // Ensure uniqueness if auto-generated from name on create
        if (!$centre && Centre::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $facilities = null;
        if (!empty($validated['facilities_text'])) {
            $parsed = preg_split('/[\r\n,]+/', $validated['facilities_text']) ?: [];
            $facilities = array_values(array_filter(array_map('trim', $parsed), fn ($item) => $item !== ''));
        }

        return [
            'name' => $validated['name'],
            'slug' => $slug,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'address' => $validated['address'],
            'pincode' => $validated['pincode'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'google_maps_url' => $validated['google_maps_url'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'facilities' => $facilities,
            'sort_order' => (int) ($validated['sort_order'] ?? ($centre?->sort_order ?? 0)),
            'is_active' => $request->has('is_active')
                ? $request->boolean('is_active')
                : ($centre === null),
        ];
    }
}
