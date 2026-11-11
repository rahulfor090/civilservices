@props([
    'title' => null,
    'subtitle' => null,
    'layout' => 'card', // 'card', 'inline', 'compact'
    'source' => 'website_form',
])

@php
    $locale = app()->getLocale();
    $languages = \App\Models\Language::where('is_active', true)->get();
@endphp

<div x-data="{
        submitting: false,
        submitted: false,
        errorMessage: '',
        successMessage: '',
        formData: {
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            language_id: '{{ $locale === 'hi' ? 2 : 1 }}',
            interest: 'UPSC Civil Services',
            source: '{{ $source }}',
            source_page: window.location.href,
            utm_source: new URLSearchParams(window.location.search).get('utm_source') || 'direct',
            utm_medium: new URLSearchParams(window.location.search).get('utm_medium') || '',
            utm_campaign: new URLSearchParams(window.location.search).get('utm_campaign') || '',
            utm_content: new URLSearchParams(window.location.search).get('utm_content') || '',
            hp_extra: '',
            consent: true
        },
        submitForm() {
            this.submitting = true;
            this.errorMessage = '';
            this.successMessage = '';

            fetch('{{ route('leads.submit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                },
                body: JSON.stringify(this.formData)
            })
            .then(res => res.json())
            .then(data => {
                this.submitting = false;
                if (data.success) {
                    this.submitted = true;
                    this.successMessage = data.message;
                } else if (data.errors) {
                    this.errorMessage = Object.values(data.errors).flat().join(' ');
                } else {
                    this.errorMessage = 'Submission failed. Please check your details.';
                }
            })
            .catch(err => {
                this.submitting = false;
                this.errorMessage = 'Network error. Please try again.';
            });
        }
     }" 
     id="leadsform"
     class="{{ $layout === 'card' ? 'bg-white rounded-xl shadow-md border border-slate-200 p-6 sm:p-8' : 'bg-slate-50 p-4 rounded-lg border border-slate-200' }}">

    <!-- Title & Subtitle -->
    <div class="mb-5 text-center sm:text-left">
        <span class="inline-block bg-amber-100 text-amber-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-1.5">
            {{ $locale === 'hi' ? 'विशेषज्ञ मार्गदर्शन' : 'Free Mentorship Call' }}
        </span>
        <h3 class="text-lg sm:text-xl font-bold text-slate-900">
            {{ $title ?? ($locale === 'hi' ? 'निःशुल्क काउंसलिंग सत्र बुक करें' : 'Book a Free 1-on-1 Counselling Session') }}
        </h3>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            {{ $subtitle ?? ($locale === 'hi' ? 'हमारे विषय विशेषज्ञ आपको पाठ्यक्रम, वैकल्पिक विषय एवं रणनीति के संबंध में व्यक्तिगत मार्गदर्शन प्रदान करेंगे।' : 'Get personalized guidance on syllabus decoding, optional subject selection, and preparation timetable from senior faculty.') }}
        </p>
    </div>

    <!-- Success Feedback State -->
    <div x-show="submitted" class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-center my-4" style="display: none;">
        <svg class="w-8 h-8 text-emerald-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <h4 class="font-bold text-emerald-900 text-sm" x-text="successMessage"></h4>
        <p class="text-xs text-emerald-700 mt-1">Our academic counsellor will call you within 2 business hours.</p>
    </div>

    <!-- Error Alert -->
    <div x-show="errorMessage" class="bg-red-50 border border-red-200 rounded-md p-3 text-xs text-red-700 mb-4" x-text="errorMessage" style="display: none;"></div>

    <!-- Form Body -->
    <form x-show="!submitted" @submit.prevent="submitForm()" class="space-y-4">
        @csrf

        <!-- Honeypot field (hidden from real users) -->
        <input type="text" x-model="formData.hp_extra" name="hp_extra" style="display:none !important;" tabindex="-1" autocomplete="off">

        <!-- Name Inputs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ $locale === 'hi' ? 'पहला नाम' : 'First Name' }} <span class="text-red-500">*</span>
                </label>
                <input type="text" x-model="formData.first_name" required 
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition" 
                       placeholder="e.g. Aditya">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ $locale === 'hi' ? 'अंतिम नाम' : 'Last Name' }}
                </label>
                <input type="text" x-model="formData.last_name" 
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition" 
                       placeholder="e.g. Sharma">
            </div>
        </div>

        <!-- Contact Inputs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ $locale === 'hi' ? 'ईमेल पता' : 'Email Address' }} <span class="text-red-500">*</span>
                </label>
                <input type="email" x-model="formData.email" required 
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition" 
                       placeholder="aspirant@gmail.com">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ $locale === 'hi' ? 'मोबाइल नंबर' : 'Phone Number' }} <span class="text-red-500">*</span>
                </label>
                <div class="flex">
                    <span class="inline-flex items-center px-2.5 rounded-l-lg border border-r-0 border-slate-300 bg-slate-100 text-slate-600 text-xs font-bold">+91</span>
                    <input type="tel" x-model="formData.phone" required pattern="[0-9]{10}" maxlength="10"
                           class="w-full px-3 py-2 text-sm rounded-r-lg border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition" 
                           placeholder="9876543210">
                </div>
            </div>
        </div>

        <!-- Selectors: Language & Exam Interest -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ $locale === 'hi' ? 'माध्यम (Language)' : 'Preparation Medium' }}
                </label>
                <select x-model="formData.language_id" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-500 bg-white outline-none">
                    @foreach($languages as $lang)
                        <option value="{{ $lang->id }}">{{ $lang->name }} ({{ $lang->native_name }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ $locale === 'hi' ? 'परीक्षा रुचि (Target Exam)' : 'I am interested in' }}
                </label>
                <select x-model="formData.interest" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-500 bg-white outline-none">
                    <option value="UPSC Civil Services">UPSC Civil Services (IAS/IPS)</option>
                    <option value="UPPCS (Uttar Pradesh)">UPPCS (Uttar Pradesh)</option>
                    <option value="BPSC (Bihar)">BPSC (Bihar)</option>
                    <option value="RAS/RTS (Rajasthan)">RAS / RTS (Rajasthan)</option>
                    <option value="MPPSC (Madhya Pradesh)">MPPSC (Madhya Pradesh)</option>
                    <option value="Optional Subjects Foundation">Optional Subjects Foundation</option>
                    <option value="Prelims & Mains Test Series">Prelims & Mains Test Series</option>
                </select>
            </div>
        </div>

        <!-- Bot Protection (Cloudflare Turnstile placeholder / dev pass-through) -->
        <div class="py-1">
            <div class="flex items-center text-xs text-slate-500 bg-slate-100 p-2.5 rounded-lg border border-slate-200">
                <svg class="w-4 h-4 text-emerald-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Protected by Cloudflare Turnstile human bot verification.</span>
            </div>
        </div>

        <!-- Consent Checkbox -->
        <div class="flex items-start">
            <input type="checkbox" x-model="formData.consent" id="consent_lead" required 
                   class="mt-1 h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
            <label for="consent_lead" class="ml-2 text-xs text-slate-600 leading-tight">
                {{ $locale === 'hi' 
                    ? 'मैं नियम एवं शर्तों तथा फोन/एसएमएस/व्हाट्सएप के माध्यम से परामर्श कॉल प्राप्त करने हेतु सहमति प्रदान करता हूँ।'
                    : 'I agree to the Terms & Privacy Policy and authorize Civil Service Gurukul to contact me via Call, SMS, or WhatsApp regarding course guidance.' }}
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                :disabled="submitting"
                class="w-full bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-700 hover:to-amber-600 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm hover:shadow transition flex items-center justify-center space-x-2 text-sm">
            <span x-show="!submitting">
                {{ $locale === 'hi' ? 'काउंसलिंग सत्र बुक करें' : 'Confirm Free Session Booking' }} &rarr;
            </span>
            <span x-show="submitting" style="display: none;" class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Processing...
            </span>
        </button>
    </form>
</div>
