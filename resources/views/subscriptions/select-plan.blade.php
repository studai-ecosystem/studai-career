<x-layouts.dashboard :title="'Confirm Plan'">
@php
$tierFeatures = [
    'free' => [
        'tagline'  => 'Start your job search manually — no card needed',
        'for'      => 'Students & casual job seekers exploring opportunities',
        'included' => [
            '10 job applications/month (manual)',
            '10 AI credits/month',
            'Basic AI Cover Letter (uses 1 credit)',
            '5 smart job alert emails/day',
            'Public profile listing on StudAI',
            'Community support forum',
        ],
    ],
    'basic' => [
        'tagline'  => 'Let AI apply to hundreds of jobs for you — on autopilot',
        'for'      => 'Active job seekers who want AI doing the heavy lifting',
        'included' => [
            'Unlimited job applications',
            '50 AI credits/month',
            '🤖 Autonomous AI Agent — auto-applies 24/7 while you sleep',
            'One-click Apply on all major platforms',
            'AI Resume Review & ATS Score Optimizer',
            'AI Cover Letter — personalized per job role',
            'AI Interview Lab — role-specific practice questions',
            '100 smart job alert notifications/day',
            'Enhanced recruiter profile visibility',
            'Application status tracker (basic)',
            'Email support',
        ],
    ],
    'pro' => [
        'tagline'  => 'Full AI career OS — agent + coaching + negotiation + analytics',
        'for'      => 'Professionals who want every career advantage AI can offer',
        'included' => [
            'Unlimited job applications',
            '200 AI credits/month',
            '🤖 Autonomous AI Agent — auto-applies 24/7 while you sleep',
            'One-click Apply on all major platforms',
            'AI Resume Optimizer + multiple resume variants',
            'AI Cover Letter — personalized per job role',
            'AI Interview Lab — advanced coaching mode',
            '🎯 AI Career Coach — personalized career roadmap & advice',
            '💰 Salary Negotiation Strategist — counter-offer scripts & market data',
            '🔍 Skill Gap Analyzer — learn exactly what to upskill next',
            'Advanced Application Tracker with analytics dashboard',
            'Unlimited smart job alerts',
            'API Access (10,000 calls/month)',
            'Priority support (24h response SLA)',
        ],
    ],
    'basic-annual' => [
        'tagline'  => 'Everything in Basic — save 17% by committing to a full year',
        'for'      => 'Job seekers ready to commit to a focused, AI-powered search',
        'included' => [
            'Unlimited job applications',
            '600 AI credits/year (50/month)',
            '🤖 Autonomous AI Agent — auto-applies 24/7 while you sleep',
            'One-click Apply on all major platforms',
            'AI Resume Review & ATS Score Optimizer',
            'AI Cover Letter — personalized per job role',
            'AI Interview Lab — role-specific practice questions',
            'Unlimited smart job alerts',
            'Enhanced recruiter profile visibility',
            'Application status tracker (basic)',
            'Email support',
            '✅ Save ₹998 vs monthly billing',
        ],
    ],
    'pro-annual' => [
        'tagline'  => 'Everything in Pro — save 25% on the full AI career platform',
        'for'      => 'Professionals investing seriously in long-term career growth',
        'included' => [
            'Unlimited job applications',
            '2,400 AI credits/year (200/month)',
            '🤖 Autonomous AI Agent — auto-applies 24/7 while you sleep',
            'One-click Apply on all major platforms',
            'AI Resume Optimizer + multiple resume variants',
            'AI Cover Letter — personalized per job role',
            'AI Interview Lab — advanced coaching mode',
            '🎯 AI Career Coach — personalized career roadmap & advice',
            '💰 Salary Negotiation Strategist — counter-offer scripts & market data',
            '🔍 Skill Gap Analyzer — learn exactly what to upskill next',
            'Advanced Application Tracker with analytics dashboard',
            'Unlimited smart job alerts',
            'API Access (10,000 calls/month)',
            'Priority support (24h response SLA)',
            '✅ Save ₹2,998 vs monthly billing',
        ],
    ],
];
$tierF = $tierFeatures[$plan->slug] ?? $tierFeatures['basic'];
@endphp

    <div style="max-width:560px;margin:0 auto;padding:8px 0 40px">
        <div class="rounded-2xl overflow-hidden" style="background:#fff;border:1px solid #ebebf4;box-shadow:0 4px 24px rgba(99,102,241,.10)">
            <div style="background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:20px 24px">
                <a href="{{ route('pricing') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-white/70 hover:text-white mb-3" style="text-decoration:none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Back to Plans
                </a>
                <h1 class="text-xl font-bold text-white">{{ $plan->name }} Plan</h1>
                <p class="text-sm text-white/70 mt-1">{{ $plan->description }}</p>
            </div>
            <div class="p-6">
                    <!-- Plan Details -->
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h3>
                        <p class="mt-2 text-gray-600">{{ $plan->description }}</p>
                    </div>

                    <!-- Pricing -->
                    <div class="mb-6">
                        @php
                            $planPrice    = (float) $plan->price;
                            $isYearly     = $plan->billing_period === 'yearly';
                            $displayPrice = $isYearly ? number_format($planPrice / 12) : number_format($planPrice);
                            $billingLabel = $isYearly ? 'Billed ₹' . number_format($planPrice) . '/year · Cancel anytime' : 'Billed monthly · Cancel anytime';
                        @endphp

                        <!-- Price display -->
                        <div class="p-5 rounded-2xl mb-4" style="background:linear-gradient(135deg,#eef2ff,#f5f3ff);border:2px solid #a5b4fc">
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold" style="color:#4f46e5">₹{{ $displayPrice }}</span>
                                <span class="text-gray-500 font-medium">/{{ $isYearly ? 'mo' : 'month' }}</span>
                            </div>
                            <p class="text-sm mt-1" style="color:#6366f1">{{ $billingLabel }}</p>
                        </div>

                        <form id="billing-form" method="POST" action="{{ route('subscriptions.subscribe') }}">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <input type="hidden" name="billing_cycle" value="{{ $plan->billing_period }}">

                            <!-- Features Summary -->
                            <div class="p-4 bg-indigo-50 rounded-xl mb-5">
                                <p class="text-xs font-medium text-indigo-500 uppercase tracking-wide mb-1">Best for</p>
                                <p class="text-sm text-indigo-800 font-medium mb-3">{{ $tierF['for'] }}</p>
                                <h4 class="font-semibold text-indigo-900 mb-3">What's Included:</h4>
                                <ul class="space-y-2 text-sm text-indigo-800">
                                    @foreach($tierF['included'] as $feature)
                                    <li class="flex items-start gap-2">
                                        <svg class="h-4 w-4 flex-shrink-0 text-indigo-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span>{{ $feature }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-5">
                                <h4 class="font-semibold text-gray-900 mb-3 text-sm">Payment Method</h4>
                                <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer" style="border-color:#6366f1;background:#eef2ff">
                                    <input type="radio" name="gateway" value="razorpay" class="h-4 w-4 text-indigo-600" checked>
                                    <div class="ml-3">
                                        <span class="font-semibold text-gray-900">Razorpay</span>
                                        <p class="text-xs text-gray-500 mt-0.5">Card · UPI · Net Banking · Wallets</p>
                                    </div>
                                    <svg class="ml-auto w-8 h-8 opacity-70" viewBox="0 0 48 48" fill="none"><rect width="48" height="48" rx="8" fill="#072654"/><path d="M12 30l6-12h5l-4 8h6l-8 4h-5z" fill="#3395FF"/></svg>
                                </label>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <a href="{{ route('pricing') }}"
                                   class="flex-1 py-3.5 px-6 text-center border border-gray-300 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 transition text-sm">
                                    Back
                                </a>
                                @if($planPrice > 0)
                                <button type="button" id="pay-btn"
                                        onclick="payNow({{ $planPrice }}, {{ $plan->id }})"
                                        class="flex-1 py-3.5 px-6 text-white rounded-xl font-bold text-sm transition hover:opacity-90 hover:-translate-y-0.5 hover:shadow-lg"
                                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                                    Pay ₹{{ number_format($planPrice) }}
                                </button>
                                @else
                                <button type="submit"
                                        class="flex-1 py-3.5 px-6 text-white rounded-xl font-bold text-sm transition"
                                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                                    Activate Free Plan
                                </button>
                                @endif
                            </div>
                        </form>
                    </div>

                                    <!-- Terms -->
                    <div class="mt-5 text-xs text-gray-400 text-center">
                        By proceeding, you agree to our <a href="#" class="text-indigo-500 hover:underline">Terms of Service</a> and
                        <a href="#" class="text-indigo-500 hover:underline">Refund Policy</a>.
                        Payments secured by Razorpay.
                    </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
    function payNow(amount, planId) {
        var btn = document.getElementById('pay-btn');
        if (btn) { btn.disabled = true; btn.textContent = 'Processing…'; }

        fetch("{{ route('razorpay.create-order') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ amount: amount })
        })
        .then(function(res) {
            return res.json().then(function(data) {
                if (!res.ok) throw new Error(data.error || 'Order creation failed');
                return data;
            });
        })
        .then(function(data) {
            var options = {
                key:         "{{ config('services.razorpay.key') }}",
                amount:      Math.round(amount * 100),
                currency:    "INR",
                name:        "StudAI Hire",
                description: "{{ $plan->name }} Subscription",
                image:       "{{ asset('assets/logo/icon.png') }}",
                order_id:    data.order_id,
                handler: function(response) {
                    fetch("{{ route('razorpay.verify-payment') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            razorpay_order_id:  response.razorpay_order_id,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_signature:  response.razorpay_signature,
                            amount:              amount,
                            plan_id:             planId
                        })
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(result) {
                        if (result.status === 'success') {
                            window.location.href = result.redirect_url || "{{ route('dashboard') }}";
                        } else {
                            alert('Payment recorded but verification had an issue. Contact support with your payment ID: ' + response.razorpay_payment_id);
                        }
                    })
                    .catch(function() {
                        alert('Network error during verification. Your payment ID is: ' + response.razorpay_payment_id + '. Please contact support.');
                    });
                },
                prefill: {
                    name:  "{{ auth()->user()->name }}",
                    email: "{{ auth()->user()->email }}"
                },
                notes: { plan_id: planId },
                theme: { color: "#4F46E5" },
                modal: {
                    ondismiss: function() {
                        if (btn) { btn.disabled = false; btn.textContent = 'Pay ₹{{ number_format((float)$plan->price) }}'; }
                    }
                }
            };
            var rzp = new Razorpay(options);
            rzp.on('payment.failed', function(response) {
                alert('Payment failed: ' + response.error.description);
                if (btn) { btn.disabled = false; btn.textContent = 'Pay ₹{{ number_format((float)$plan->price) }}'; }
            });
            rzp.open();
        })
        .catch(function(err) {
            alert('Could not initiate payment: ' + err.message);
            if (btn) { btn.disabled = false; btn.textContent = 'Pay ₹{{ number_format((float)$plan->price) }}'; }
        });
    }
    </script>
    @endpush
</x-layouts.dashboard>
