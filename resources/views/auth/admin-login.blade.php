<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if(session('error'))
        <div class="mb-4 p-3.5 rounded-xl text-sm flex items-center gap-2" style="background:#fef2f2; border:1px solid #FCA5A5; color:#B91C1C">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 text-center">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl" style="background:#0C0C0C; color:#fff">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-extrabold tracking-tight" style="color:#0C0C0C">
            Stud<span style="color:#2D6CDF">AI</span> One Admin
        </h2>
        <p class="text-sm mt-1" style="color:#737373">Restricted access &mdash; administrators only</p>
    </div>

    {{-- Restricted badge --}}
    <div class="mb-5 flex w-full items-center justify-center gap-2 rounded-xl py-2 text-xs font-semibold"
         style="background:#eef1f5; border:1.5px solid rgba(28,52,77,.18); color:#0C0C0C">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
        Secure administrator sign-in
    </div>

    <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Email Address</label>
            <input id="email" name="email" type="email" required autofocus autocomplete="username"
                value="{{ old('email') }}" class="auth-input" placeholder="you@studai.one" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                class="auth-input" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
        </div>

        {{-- Remember --}}
        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm cursor-pointer" style="color:#737373">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#2D6CDF] focus:ring-[#2D6CDF]">
                Keep me signed in
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-semibold transition-colors" style="color:#2D6CDF">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-auth employer-btn">
            <span>🛡️ Sign in to Admin</span>
        </button>
    </form>

    <p class="text-center text-sm mt-6" style="color:#737373">
        Not an administrator?
        <a href="{{ route('login') }}" class="font-semibold transition-colors ml-1" style="color:#2D6CDF">
            Go to member sign in →
        </a>
    </p>
</x-guest-layout>
