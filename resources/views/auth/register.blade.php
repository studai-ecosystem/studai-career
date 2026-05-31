<x-guest-layout>
    <div x-data="{
        role: '{{ old('account_type', request('type') === 'employer' ? 'employer' : 'job_seeker') }}'
    }" class="space-y-5">

        {{-- Header --}}
        <div class="text-center">
            <h2 class="text-2xl font-extrabold tracking-tight" style="color:#0C0C0C">Create your account</h2>
            <p class="text-sm mt-1" style="color:#737373">Powered by <span style="color:#2D6CDF; font-weight:600">Orin™</span> AI Engine</p>
        </div>

        {{-- Role Selector --}}
        <div class="grid grid-cols-2 gap-3">
            <button type="button" @click="role = 'job_seeker'"
                :class="role === 'job_seeker' ? 'role-card active-seeker' : 'role-card'"
                class="relative flex flex-col items-center gap-2 text-center w-full">
                <div :class="role === 'job_seeker' ? 'bg-[#EBF2FF] text-[#2D6CDF]' : 'bg-gray-100 text-gray-400'"
                    class="flex h-11 w-11 items-center justify-center rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span :class="role === 'job_seeker' ? 'text-[#0C2E72]' : 'text-gray-600'" class="text-xs font-bold transition-colors">Job Seeker</span>
                <span class="text-[10px] leading-tight" style="color:#A8A8A8">Find jobs &amp; AI career tools</span>
                <span x-show="role === 'job_seeker'" class="absolute top-2 right-2 text-[#2D6CDF]">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </span>
            </button>

            <button type="button" @click="role = 'employer'"
                :class="role === 'employer' ? 'role-card active-employer' : 'role-card'"
                class="relative flex flex-col items-center gap-2 text-center w-full">
                <div :class="role === 'employer' ? 'bg-[#EBF2FF] text-[#0C0C0C]' : 'bg-gray-100 text-gray-400'"
                    class="flex h-11 w-11 items-center justify-center rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span :class="role === 'employer' ? 'text-[#0C0C0C]' : 'text-gray-600'" class="text-xs font-bold transition-colors">Company</span>
                <span class="text-[10px] leading-tight" style="color:#A8A8A8">Post jobs &amp; hire via S.C.O.U.T™</span>
                <span x-show="role === 'employer'" class="absolute top-2 right-2 text-[#0C0C0C]">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </span>
            </button>
        </div>

        {{-- Employer S.C.O.U.T badge --}}
        <div x-show="role === 'employer'" x-transition
            class="rounded-xl px-4 py-3" style="background:#EBF2FF; border:1px solid #BFCFEE">
            <p class="text-xs font-semibold flex items-center gap-2" style="color:#0C0C0C">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                S.C.O.U.T™ — Smart Candidate Optimization &amp; Universal Talent. After signing up, complete your Corporate DNA profile to unlock AI hiring intelligence.
            </p>
        </div>

        {{-- Registration Form --}}
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="account_type" :value="role">

            {{-- COMPANY FIELDS --}}
            <div x-show="role === 'employer'" x-transition class="space-y-4">
                <div class="text-xs font-bold uppercase tracking-wider pb-1.5" style="color:#737373; border-bottom:1.5px solid #EBF2FF">
                    Company Information
                </div>

                <div>
                    <label for="company_name" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Company Name *</label>
                    <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" autocomplete="organization"
                        class="auth-input" placeholder="e.g. Acme Technologies Pvt. Ltd." />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-1.5 text-xs" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="industry" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Industry</label>
                        <select id="industry" name="industry" class="auth-input">
                            <option value="">Select industry</option>
                            <option value="Technology" {{ old('industry') === 'Technology' ? 'selected' : '' }}>Technology / IT</option>
                            <option value="Finance" {{ old('industry') === 'Finance' ? 'selected' : '' }}>Finance &amp; Banking</option>
                            <option value="Healthcare" {{ old('industry') === 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                            <option value="Education" {{ old('industry') === 'Education' ? 'selected' : '' }}>Education</option>
                            <option value="Retail" {{ old('industry') === 'Retail' ? 'selected' : '' }}>Retail &amp; E-commerce</option>
                            <option value="Manufacturing" {{ old('industry') === 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                            <option value="Consulting" {{ old('industry') === 'Consulting' ? 'selected' : '' }}>Consulting</option>
                            <option value="Media" {{ old('industry') === 'Media' ? 'selected' : '' }}>Media &amp; Entertainment</option>
                            <option value="Real Estate" {{ old('industry') === 'Real Estate' ? 'selected' : '' }}>Real Estate</option>
                            <option value="Other" {{ old('industry') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('industry')" class="mt-1.5 text-xs" />
                    </div>
                    <div>
                        <label for="company_size" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Company Size</label>
                        <select id="company_size" name="company_size" class="auth-input">
                            <option value="">Select size</option>
                            <option value="1-10" {{ old('company_size') === '1-10' ? 'selected' : '' }}>1–10 employees</option>
                            <option value="11-50" {{ old('company_size') === '11-50' ? 'selected' : '' }}>11–50 employees</option>
                            <option value="51-200" {{ old('company_size') === '51-200' ? 'selected' : '' }}>51–200 employees</option>
                            <option value="201-500" {{ old('company_size') === '201-500' ? 'selected' : '' }}>201–500 employees</option>
                            <option value="501-1000" {{ old('company_size') === '501-1000' ? 'selected' : '' }}>501–1,000 employees</option>
                            <option value="1000+" {{ old('company_size') === '1000+' ? 'selected' : '' }}>1,000+ employees</option>
                        </select>
                        <x-input-error :messages="$errors->get('company_size')" class="mt-1.5 text-xs" />
                    </div>
                </div>

                <div>
                    <label for="company_website" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Company Website (optional)</label>
                    <input id="company_website" type="text" name="company_website" value="{{ old('company_website') }}"
                        class="auth-input" placeholder="https://yourcompany.com" />
                    <x-input-error :messages="$errors->get('company_website')" class="mt-1.5 text-xs" />
                </div>

                <div>
                    <label for="hr_email" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">HR / Hiring Email *</label>
                    <input id="hr_email" type="email" name="hr_email" value="{{ old('hr_email') }}"
                        class="auth-input" placeholder="hr@yourcompany.com" x-bind:required="role === 'employer'" />
                    <p class="mt-1 text-xs" style="color:#A8A8A8">Candidate notifications sent from this address.</p>
                    <x-input-error :messages="$errors->get('hr_email')" class="mt-1.5 text-xs" />
                </div>

                <div class="text-xs font-bold uppercase tracking-wider pb-1.5 pt-1" style="color:#737373; border-bottom:1.5px solid #EBF2FF">
                    Your Account Details
                </div>
            </div>

            {{-- COMMON FIELDS --}}
            <div>
                <label for="name" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">
                    <span x-text="role === 'employer' ? 'Your Full Name *' : 'Full Name *'">Full Name *</span>
                </label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                    class="auth-input" placeholder="Your full name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs" />
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">
                    <span x-text="role === 'employer' ? 'Work Email Address *' : 'Email Address *'">Email *</span>
                </label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    class="auth-input" x-bind:placeholder="role === 'employer' ? 'you@yourcompany.com' : 'you@example.com'" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Phone Number (optional)</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                    class="auth-input" placeholder="+91 98765 43210" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5 text-xs" />
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Password *</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="auth-input" placeholder="••••••••" />
                <p class="mt-1 text-xs" style="color:#A8A8A8">Min 8 characters with uppercase, lowercase &amp; numbers</p>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Confirm Password *</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="auth-input" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs" />
            </div>

            {{-- Terms --}}
            <div class="flex items-start gap-3">
                <input id="terms" name="terms" type="checkbox" required
                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-[#2D6CDF] focus:ring-[#2D6CDF]" />
                <label for="terms" class="text-xs leading-relaxed" style="color:#737373">
                    I agree to the
                    <a href="/terms" class="font-semibold transition-colors hover:text-[#1B57C4]" style="color:#2D6CDF">Terms of Service</a>
                    and
                    <a href="/privacy" class="font-semibold transition-colors hover:text-[#1B57C4]" style="color:#2D6CDF">Privacy Policy</a>
                </label>
            </div>
            <x-input-error :messages="$errors->get('terms')" class="mt-1 text-xs" />

            {{-- Submit --}}
            <button type="submit"
                :class="role === 'employer' ? 'btn-auth employer-btn' : 'btn-auth'">
                <span x-text="role === 'employer' ? '🏢 Create Company Account →' : '🚀 Create Job Seeker Account →'">Create Account</span>
            </button>

            <p class="text-center text-sm" style="color:#737373">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold transition-colors hover:text-[#1B57C4] ml-1" style="color:#2D6CDF">Sign in →</a>
            </p>
        </form>

        <div class="auth-divider">or sign up with</div>
        <x-social-login-buttons />
    </div>
</x-guest-layout>
