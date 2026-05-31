<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Header --}}
    <div class="mb-6 text-center">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl" style="background:#EBF2FF; color:#2D6CDF">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-extrabold tracking-tight" style="color:#0C0C0C">Set a new password</h2>
        <p class="text-sm mt-1" style="color:#737373">For your security, please replace your temporary password before continuing.</p>
    </div>

    <form method="POST" action="{{ route('password.force.update') }}" class="space-y-4">
        @csrf

        {{-- New Password --}}
        <div>
            <label for="password" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">New password</label>
            <input id="password" name="password" type="password" required autofocus autocomplete="new-password"
                class="auth-input" placeholder="At least 8 characters" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
            <p class="mt-1.5 text-[11px]" style="color:#A8A8A8">Use upper &amp; lower case letters and at least one number.</p>
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#737373">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                class="auth-input" placeholder="Re-enter new password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs" />
        </div>

        <button type="submit" class="btn-auth">
            <span>Update password &amp; continue</span>
        </button>
    </form>
</x-guest-layout>
