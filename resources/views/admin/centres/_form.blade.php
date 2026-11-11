@if($errors->any())
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs space-y-1">
        <div class="font-bold">Please fix the following errors:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $isEdit = isset($centre) && $centre->exists;
    $facilitiesValue = old('facilities_text');
    if ($facilitiesValue === null && $isEdit && is_array($centre->facilities)) {
        $facilitiesValue = implode("\n", $centre->facilities);
    }
@endphp

<div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
            Centre Identity &amp; Address
        </h2>
        @if($isEdit)
            <span class="text-xs text-slate-400">Last updated: {{ $centre->updated_at?->diffForHumans() ?? 'N/A' }}</span>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Centre Name *</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $centre->name ?? '') }}"
                   required
                   placeholder="e.g., Delhi Old Rajinder Nagar (National Headquarters)"
                   class="w-full px-3.5 py-2.5 text-sm rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">URL Slug</label>
            <input type="text"
                   name="slug"
                   value="{{ old('slug', $centre->slug ?? '') }}"
                   placeholder="Auto-generated from name if left blank"
                   class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
            @if($isEdit && $centre->isHeadquarters())
                <p class="text-[11px] text-amber-700 mt-1">National HQ slug (<code class="font-mono">{{ $centre->slug }}</code>). Keep unchanged to preserve existing HQ links.</p>
            @endif
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Street Address *</label>
        <textarea name="address"
                  rows="3"
                  required
                  placeholder="Enter complete campus street address, building, landmark..."
                  class="w-full px-3.5 py-2.5 text-sm rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">{{ old('address', $centre->address ?? '') }}</textarea>
        <p class="text-[11px] text-slate-400 mt-1">Displayed across the public Centres directory, Homepage, Top Strip (for HQ), Footer, and Schema.org JSON-LD.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">City *</label>
            <input type="text"
                   name="city"
                   value="{{ old('city', $centre->city ?? '') }}"
                   required
                   placeholder="e.g., New Delhi"
                   class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">State *</label>
            <input type="text"
                   name="state"
                   value="{{ old('state', $centre->state ?? '') }}"
                   required
                   placeholder="e.g., Delhi"
                   class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">PIN Code</label>
            <input type="text"
                   name="pincode"
                   value="{{ old('pincode', $centre->pincode ?? '') }}"
                   placeholder="e.g., 110060"
                   class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
    <div class="border-b border-slate-100 pb-3">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
            Contact, Maps &amp; Facilities
        </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Desk Phone / Helpline *</label>
            <input type="text"
                   name="phone"
                   value="{{ old('phone', $centre->phone ?? '') }}"
                   required
                   placeholder="e.g., +91 9911020626"
                   class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Centre Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $centre->email ?? '') }}"
                   placeholder="e.g., info@civilservicegurukul.com"
                   class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-1">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Display Order</label>
            <input type="number"
                   name="sort_order"
                   min="0"
                   value="{{ old('sort_order', $centre->sort_order ?? ($nextSortOrder ?? 1)) }}"
                   class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Latitude</label>
            <input type="number"
                   step="0.0000001"
                   name="latitude"
                   value="{{ old('latitude', $centre->latitude ?? '') }}"
                   placeholder="e.g., 28.6387000"
                   class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Longitude</label>
            <input type="number"
                   step="0.0000001"
                   name="longitude"
                   value="{{ old('longitude', $centre->longitude ?? '') }}"
                   placeholder="e.g., 77.1852000"
                   class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Google Maps URL</label>
        <input type="url"
               name="google_maps_url"
               value="{{ old('google_maps_url', $centre->google_maps_url ?? '') }}"
               placeholder="https://maps.google.com/?q=..."
               class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Campus Facilities (one per line or comma-separated)</label>
        <textarea name="facilities_text"
                  rows="4"
                  placeholder="Smart AC Classrooms&#10;24x7 Study Library&#10;Offline Doubt Counters"
                  class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">{{ $facilitiesValue }}</textarea>
    </div>

    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   {{ old('is_active', $centre->is_active ?? true) ? 'checked' : '' }}
                   class="rounded border-slate-300 text-brand-navy focus:ring-brand-navy">
            <span class="text-xs font-semibold text-slate-700">Active (visible on public website &amp; booking forms)</span>
        </label>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.centres.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-bold shadow-sm transition">
                {{ $isEdit ? 'Save Centre Changes' : 'Create Centre' }}
            </button>
        </div>
    </div>
</div>

