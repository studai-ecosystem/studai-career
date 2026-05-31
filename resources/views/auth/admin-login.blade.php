<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

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
        <p class="text-sm mt-1" style="color:#737373">Restricted access &mdash; administrators only.</p>
    </div>

    <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold mb-1.5" style="color:#0C0C0C">Email</label>
            <input id="email" name="email" type="email" required autofocus autocomplete="username"
                value="{{ old('email') }}"
                class="w-full rounded-xl border px-4 py-3 text-sm transition focus:outline-none focus:ring-2"
                style="border-color:#E5E7EB" placeholder="you@studai.one" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-semibold mb-1.5" style="color:#0C0C0C">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                class="w-full rounded-xl border px-4 py-3 text-sm transition focus:outline-none focus:ring-2"
                style="border-color:#E5E7EB" placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember --}}
        <label class="flex items-center gap-2 text-sm" style="color:#737373">
            <input type="checkbox" name="remember" class="rounded border-gray-300" style="color:#2D6CDF">
            Keep me signed in
        </label>

        <button type="submit"
            class="w-full rounded-xl px-4 py-3 text-sm font-bold text-white transition hover:opacity-90"
            style="background:#0C0C0C">
            Sign in to admin
        </button>
    </form>
</x-guest-layout>
