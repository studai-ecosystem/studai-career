<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if(session('error'))
        <div class="mb-4 p-3.5 rounded-xl text-sm flex items-center gap-2" style="background:#fef2f2; border:1px solid #FCA5A5; color:#2D6CDF">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-3.5 rounded-xl text-sm flex items-center gap-2" style="background:#EDFAF2; border:1px solid #A3D9B4; color:#1E8E3E">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-extrabold tracking-tight" style="color:#0C0C0C">Welcome back</h2>
        <p class="text-sm mt-1" style="color:#737373">Sign in to your <span style="color:#2D6CDF; font-weight:600">AI Career Platform</span></p>
    </div>

    <div x-data="{ role: '{{ old('login_type', request('type') === 'employer' ? 'employer' : 'seeker') }}' }" class="space-y-5">

        {{-- Role selector --}}
        <div class="grid grid-cols-2 gap-3">
            <button type="button" @click="role = 'seeker'"
                :class="role === 'seeker' ? 'role-card active-seeker' : 'role-card'"
                class="flex flex-col items-center gap-2 text-center w-full">
                <div :class="role === 'seeker' ? 'bg-[#EBF2FF] text-[#2D6CDF]' : 'bg-gray-100 text-gray-400'"
                    class="flex h-11 w-11 items-center justify-center rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span :class="role === 'seeker' ? 'text-[#0C2E72]' : 'text-gray-600'" class="text-xs font-bold transition-colors">Job Seeker</span>
                <span class="text-[10px] leading-tight" style="color:#A8A8A8">Find jobs &amp; career tools</span>
                <span x-show="role === 'seeker'" class="absolute top-2 right-2 text-[#2D6CDF]">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </span>
            </button>

            <button type="button" @click="role = 'employer'"
                :class="role === 'employer' ? 'role-card active-employer' : 'role-card'"
                class="flex flex-col items-center gap-2 text-center w-full">
                <div :class="role === 'employer' ? 'bg-[#EBF2FF] text-[#0C0C0C]' : 'bg-gray-100 text-gray-400'"
                    class="flex h-11 w-11 items-center justify-center rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span :class="role === 'employer' ? 'text-[#0C0C0C]' : 'text-gray-600'" class="text-xs font-bold transition-colors">Company</span>
                <span class="text-[10px] leading-tight" style="color:#A8A8A8">Post jobs &amp; hire talent</span>
                <span x-show="role === 'employer'" class="absolute top-2 right-2 text-[#0C0C0C]">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </span>
            </button>
        </div>

        {{-- Context hint --}}
        <p x-show="role === 'seeker'" class="text-center text-xs font-medium" style="color:#2D6CDF; margin-top:-4px">
            Signing in as <strong>Job Seeker</strong> — AI Career Agent
        </p>
        <p x-show="role === 'employer'" class="text-center text-xs font-medium" style="color:#0C0C0C; margin-top:-4px">
            Signing in as <strong>Employer</strong> — S.C.O.U.T™
        </p>

        {{-- Login form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="login_type" :value="role">

            <div>
                <label for="email" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="auth-input"
                    x-bind:placeholder="role === 'employer' ? 'you@yourcompany.com' : 'you@example.com'" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" style="color:#2D6CDF" />
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="auth-input" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" style="color:#2D6CDF" />
            </div>

            <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-sm cursor-pointer" style="color:#737373">
                    <input type="checkbox" name="remember"
                        class="w-4 h-4 rounded border-gray-300 text-[#2D6CDF] focus:ring-[#2D6CDF]">
                    Remember me
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       :class="role === 'employer' ? 'text-[#0C0C0C] hover:text-[#0C0C0C]' : 'text-[#2D6CDF] hover:text-[#1B57C4]'"
                       class="text-sm font-semibold transition-colors">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit"
                :class="role === 'employer' ? 'btn-auth employer-btn' : 'btn-auth'">
                <span x-text="role === 'employer' ? '🏢 Sign in to S.C.O.U.T™' : '🚀 Sign in to Career Agent'"></span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="auth-divider">or continue with</div>

        <x-social-login-buttons />

        <p class="text-center text-sm" style="color:#737373">
            Don't have an account?
            <a :href="role === 'employer' ? '{{ route('register') }}?type=employer' : '{{ route('register') }}'"
               :class="role === 'employer' ? 'text-[#0C0C0C] hover:text-[#0C0C0C]' : 'text-[#2D6CDF] hover:text-[#1B57C4]'"
               class="font-semibold transition-colors ml-1">
                Sign up free →
            </a>
        </p>
    </div>
</x-guest-layout>
