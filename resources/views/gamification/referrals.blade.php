@extends('layouts.dashboard')

@section('title', 'Referrals')
@section('page-title', 'Referral Program')
@section('page-description', 'Invite friends and earn bonus points')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl p-6 text-white" style="background:#2D6CDF;box-shadow: none">
        <h1 class="text-2xl font-bold mb-1">Referral Program</h1>
        <p class="text-blue-100 text-sm">Invite friends to StudAI Hire and earn bonus points for every signup</p>
    </div>

    {{-- Referral Code Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <h2 class="font-bold text-gray-900 dark:text-white mb-4 text-lg">Your Referral Code</h2>
        <div class="flex items-center gap-3">
            <div class="flex-1 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl px-5 py-3 font-mono text-xl font-bold tracking-widest text-center" style="color:#2D6CDF">
                {{ strtoupper($referralCode) }}
            </div>
            <button onclick="copyCode('{{ strtoupper($referralCode) }}')"
                    class="px-5 py-3 rounded-xl text-sm font-semibold text-white flex-shrink-0 transition-all"
                    style="background:#2D6CDF">
                Copy
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-3">Share this code with friends. You'll earn <strong>500 points</strong> for every friend who signs up!</p>

        {{-- Share buttons --}}
        <div class="flex gap-3 mt-4 flex-wrap">
            <a href="https://wa.me/?text={{ urlencode('Join me on StudAI Hire — India\'s AI career platform! Use my referral code: '.strtoupper($referralCode).' → '.config('app.url').'/register') }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-green-500 text-white hover:bg-green-600 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp
            </a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode('Just joined StudAI Hire — AI-powered career platform! Sign up with my referral code '.strtoupper($referralCode).' 🚀') }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-black text-white hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.259 5.629L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                Share on X
            </a>
            <button onclick="copyLink()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Copy Link
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold" style="color:#2D6CDF">{{ $stats['total_referrals'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Referrals</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-green-600">{{ $stats['successful_referrals'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Successful</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-amber-500">{{ number_format($stats['points_earned'] ?? 0) }}</p>
            <p class="text-xs text-gray-500 mt-1">Points Earned</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-purple-600">{{ $stats['pending_referrals'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Pending</p>
        </div>
    </div>

    <a href="{{ route('gamification.dashboard') }}" class="inline-block px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
        ← Back to Dashboard
    </a>
</div>

@push('scripts')
<script>
function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => alert('Referral code copied!'));
}
function copyLink() {
    const url = `{{ config('app.url') }}/register?ref={{ strtoupper($referralCode) }}`;
    navigator.clipboard.writeText(url).then(() => alert('Link copied!'));
}
</script>
@endpush
@endsection
