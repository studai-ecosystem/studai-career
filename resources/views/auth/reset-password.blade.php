<x-guest-layout>
    <div class="space-y-5">
        {{-- Header --}}
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:linear-gradient(135deg,#e0e7ff,#ede9fe)">
                    <svg class="w-7 h-7" style="color:#6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight" style="color:#1a1a2e">Set new password</h2>
            <p class="text-sm mt-1.5 max-w-xs mx-auto leading-relaxed" style="color:#6b7280">
                Choose a strong password to secure your account.
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#6b7280">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                    class="auth-input" placeholder="you@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" style="color:#dc2626" />
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#6b7280">New Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="auth-input" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" style="color:#dc2626" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#6b7280">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="auth-input" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs" style="color:#dc2626" />
            </div>

            <button type="submit" class="btn-auth">
                <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Reset Password
            </button>
        </form>

        <p class="text-center text-sm" style="color:#6b7280">
            Remembered it?
            <a href="{{ route('login') }}" class="font-semibold transition-colors hover:text-indigo-700 ml-1" style="color:#6366f1">Back to sign in →</a>
        </p>
    </div>
</x-guest-layout>
