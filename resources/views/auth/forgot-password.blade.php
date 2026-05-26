<x-guest-layout>
    <div class="space-y-5">
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:linear-gradient(135deg,#e0e7ff,#ede9fe)">
                    <svg class="w-7 h-7" style="color:#6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight" style="color:#1a1a2e">Forgot password?</h2>
            <p class="text-sm mt-1.5 max-w-xs mx-auto leading-relaxed" style="color:#6b7280">
                No worries. Enter your email and we'll send you a reset link.
            </p>
        </div>

        <x-auth-session-status class="p-3.5 rounded-xl text-sm" :status="session('status')"
            style="background:#f0fdf4; border:1px solid #86efac; color:#16a34a" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#6b7280">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="auth-input" placeholder="you@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" style="color:#dc2626" />
            </div>

            <button type="submit" class="btn-auth">
                <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Send Reset Link
            </button>
        </form>

        <p class="text-center text-sm" style="color:#6b7280">
            Remembered it?
            <a href="{{ route('login') }}" class="font-semibold transition-colors hover:text-indigo-700 ml-1" style="color:#6366f1">Back to sign in →</a>
        </p>
    </div>
</x-guest-layout>
