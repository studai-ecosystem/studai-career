<x-marketing-layout>
@php
    $monthlyPlans = $plans->where('billing_period', 'monthly')->values();
    $yearlyPlans  = $plans->where('billing_period', 'yearly')->values();
    $planThemes = [
        0 => ['from'=>'#6366f1','to'=>'#818cf8','light'=>'#eef2ff','btn'=>'linear-gradient(135deg,#6366f1,#818cf8)'],
        1 => ['from'=>'#7c3aed','to'=>'#a855f7','light'=>'#f5f3ff','btn'=>'linear-gradient(135deg,#7c3aed,#a855f7)'],
        2 => ['from'=>'#0891b2','to'=>'#06b6d4','light'=>'#ecfeff','btn'=>'linear-gradient(135deg,#0891b2,#06b6d4)'],
    ];

    // Comprehensive feature sets per plan slug
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
            'excluded' => [
                'Autonomous AI Agent (24/7 auto-apply)',
                'AI Resume Optimizer & ATS Score',
                'AI Interview Lab & mock interviews',
                'Salary Negotiation Strategist',
                'One-click Apply',
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
            'excluded' => [],
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
            'excluded' => [],
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
            'excluded' => [],
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
            'excluded' => [],
        ],
    ];
@endphp

@push('styles')
<style>
@keyframes fadeUp   { from{opacity:0;transform:translateY(32px)} to{opacity:1;transform:translateY(0)} }
@keyframes floatOrb { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(20px,-30px) scale(1.05)} 66%{transform:translate(-15px,15px) scale(.97)} }
@keyframes gradShift{ 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
@keyframes tickerX  { from{transform:translateX(0)} to{transform:translateX(-50%)} }
@keyframes shimmer  { to{background-position:-200% center} }
@keyframes pulseRing{ 0%{box-shadow:0 0 0 0 rgba(124,58,237,.4)} 70%{box-shadow:0 0 0 14px rgba(124,58,237,0)} 100%{box-shadow:0 0 0 0 rgba(124,58,237,0)} }
.pricing-hero{position:relative;overflow:hidden;background:linear-gradient(135deg,#faf5ff 0%,#f0f9ff 35%,#fdf4ff 65%,#f0fdf4 100%)}
.orb{position:absolute;border-radius:50%;filter:blur(60px);pointer-events:none;animation:floatOrb 8s ease-in-out infinite}
.plan-card{transition:transform .3s ease,box-shadow .3s ease}
.plan-card:hover{transform:translateY(-6px);box-shadow:0 24px 60px rgba(99,102,241,.18)}
.shimmer-btn{background-size:200% auto;animation:shimmer 2s linear infinite}
.animate-card{opacity:0;animation:fadeUp .6s ease forwards}
.delay-1{animation-delay:.1s}.delay-2{animation-delay:.2s}.delay-3{animation-delay:.35s}
.check-icon{color:#10b981}
.ticker-wrap{overflow:hidden}
.ticker{display:flex;gap:48px;animation:tickerX 24s linear infinite;white-space:nowrap}
details summary::-webkit-details-marker{display:none}
details[open] .faq-icon{transform:rotate(180deg)}
.faq-icon{transition:transform .3s}
</style>
@endpush

{{-- HERO --}}
<div class="pricing-hero py-20 px-4">
    <div class="orb" style="width:480px;height:480px;background:radial-gradient(circle,rgba(99,102,241,.18),transparent 70%);top:-120px;left:-140px;animation-duration:10s"></div>
    <div class="orb" style="width:360px;height:360px;background:radial-gradient(circle,rgba(168,85,247,.15),transparent 70%);bottom:-80px;right:-100px;animation-duration:12s;animation-delay:-4s"></div>
    <div class="orb" style="width:240px;height:240px;background:radial-gradient(circle,rgba(6,182,212,.12),transparent 70%);top:60px;right:25%;animation-duration:9s;animation-delay:-2s"></div>
    <div class="relative max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 mb-6 text-xs font-bold uppercase tracking-widest animate-card delay-1" style="background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(168,85,247,.12));border:1px solid rgba(99,102,241,.25);color:#6366f1">
            <span style="width:6px;height:6px;border-radius:50%;background:#6366f1;display:inline-block"></span>
            Simple, Transparent Pricing
        </div>
        <h1 class="text-5xl sm:text-6xl font-extrabold tracking-tight mb-5 animate-card delay-2" style="color:#0f172a">
            Plans that <span style="background:linear-gradient(135deg,#6366f1,#a855f7,#ec4899);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;background-size:200%;animation:gradShift 4s ease infinite">grow with you</span>
        </h1>
        <p class="text-lg text-gray-500 max-w-2xl mx-auto mb-10 animate-card delay-3">Start free. No credit card. Scale as your career grows. All plans include our core AI platform.</p>
        <div class="ticker-wrap mb-10 animate-card delay-3">
            <div class="ticker">
                @foreach(['7-day money-back','No hidden fees','Cancel anytime','SSL encrypted','50k+ professionals',"India's #1 Career AI",'7-day money-back','No hidden fees','Cancel anytime','SSL encrypted','50k+ professionals',"India's #1 Career AI"] as $badge)
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color:#6366f1">
                    <svg style="width:14px;height:14px;color:#10b981" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ $badge }}
                </span>
                @endforeach
            </div>
        </div>
        <div class="flex justify-center items-center gap-4 animate-card delay-3">
            <span id="label-monthly" class="text-sm font-bold text-gray-900">Monthly</span>
            <button id="billing-toggle" aria-label="Toggle billing period" class="relative inline-flex h-8 w-16 items-center rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" style="background:#e5e7eb;transition:background .3s">
                <span id="toggle-dot" class="inline-block h-6 w-6 transform rounded-full bg-white shadow-md transition-transform duration-300" style="transform:translateX(0.25rem)"></span>
            </button>
            <span id="label-yearly" class="text-sm font-medium text-gray-500">Yearly <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold" style="background:#d1fae5;color:#065f46">Save 25%</span></span>
        </div>
    </div>
</div>

{{-- MONTHLY PLANS --}}
<div id="monthly-plans" class="py-12 px-4" style="background:#fafafa">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3 max-w-5xl mx-auto">
            @foreach($monthlyPlans as $i => $plan)
            @php
                $isCurrent = $userPlan && $userPlan->id === $plan->id;
                $isFree    = $plan->price <= 0;
                $appLimit  = $plan->applications_limit;
                $creditLim = $plan->ai_credits;
                $features  = is_array($plan->features) ? $plan->features : [];
                $isUpgrade = $userPlan && $plan->price_monthly > $userPlan->price_monthly;
                $t         = $planThemes[$i % 3];
                $d         = ['delay-1','delay-2','delay-3'][$i % 3];
            @endphp
            <div class="plan-card animate-card {{ $d }} relative flex flex-col rounded-3xl overflow-hidden" style="{{ $plan->is_featured ? 'box-shadow:0 20px 60px rgba(124,58,237,.25);border:2px solid '.$t['from'] : 'border:1.5px solid #e5e7eb;box-shadow:0 4px 24px rgba(0,0,0,.06)' }};background:#fff">
                @if($plan->is_featured)
                <div class="h-1.5 w-full" style="background:linear-gradient(90deg,{{ $t['from'] }},{{ $t['to'] }},{{ $t['from'] }});background-size:200%;animation:gradShift 3s ease infinite"></div>
                <div class="absolute top-5 right-5"><span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold text-white" style="background:linear-gradient(135deg,{{ $t['from'] }},{{ $t['to'] }})">&#9733; Most Popular</span></div>
                @endif
                <div class="p-8 flex-1 flex flex-col">
                    <div class="mb-6">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4" style="background:{{ $t['light'] }}">
                            <svg class="w-6 h-6" style="color:{{ $t['from'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-900">{{ $plan->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $plan->description }}</p>
                    </div>
                    <div class="mb-8">
                        <div class="flex items-baseline gap-1"><span class="text-5xl font-extrabold" style="color:{{ $t['from'] }}">&#8377;{{ number_format($plan->price_monthly) }}</span><span class="text-gray-400 font-medium">/mo</span></div>
                        @if($isFree)<p class="mt-1 text-sm text-gray-400">Free forever &bull; No credit card</p>
                        @else<p class="mt-1 text-sm text-gray-400">Billed monthly &bull; Cancel anytime</p>@endif
                    </div>
                    @php $tierF = $tierFeatures[$plan->slug] ?? $tierFeatures['basic']; @endphp
                    {{-- What this plan is for --}}
                    <div class="mb-4 px-3 py-2.5 rounded-xl" style="background:{{ $t['light'] }};border:1px solid {{ $t['from'] }}22">
                        <p class="text-xs font-semibold mb-0.5" style="color:{{ $t['from'] }}">{{ $tierF['tagline'] }}</p>
                        <p class="text-xs text-gray-500">Best for: {{ $tierF['for'] }}</p>
                    </div>
                    {{-- Included features --}}
                    <ul class="space-y-2.5 flex-1 mb-8">
                        @foreach($tierF['included'] as $feat)
                        <li class="flex items-start gap-2.5">
                            <svg class="check-icon flex-shrink-0 w-4 h-4 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-sm text-gray-700">{{ $feat }}</span>
                        </li>
                        @endforeach
                        @foreach($tierF['excluded'] as $feat)
                        <li class="flex items-start gap-2.5 opacity-40">
                            <svg class="flex-shrink-0 w-4 h-4 mt-0.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="text-sm text-gray-400 line-through">{{ $feat }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @auth
                        @if($isCurrent)<div class="w-full py-3.5 px-6 text-center rounded-2xl font-semibold text-sm" style="background:#f3f4f6;color:#6b7280">&#10003; Current Plan</div>
                        @elseif($isFree)<a href="{{ route('dashboard') }}" class="block w-full py-3.5 px-6 text-center rounded-2xl font-semibold text-sm hover:opacity-90 transition-all hover:-translate-y-0.5" style="background:{{ $t['light'] }};color:{{ $t['from'] }}">Continue for Free</a>
                        @else<a href="{{ route('subscriptions.select-plan', ['plan_id' => $plan->id]) }}" class="shimmer-btn block w-full py-3.5 px-6 text-center rounded-2xl font-bold text-sm text-white transition-all hover:shadow-xl hover:-translate-y-0.5" style="background:{{ $t['btn'] }}">{{ $isUpgrade ? 'Upgrade Now' : 'Get Started' }}</a>@endif
                    @else
                        <a href="{{ route('register') }}" class="shimmer-btn block w-full py-3.5 px-6 text-center rounded-2xl font-bold text-sm text-white transition-all hover:shadow-xl hover:-translate-y-0.5" style="background:{{ $t['btn'] }}">{{ $isFree ? 'Start Free � No Card' : 'Get Started Free' }}</a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- YEARLY PLANS --}}
<div id="yearly-plans" class="hidden py-12 px-4" style="background:#fafafa">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3 max-w-5xl mx-auto">
            @foreach($yearlyPlans as $i => $plan)
            @php
                $isCurrent = $userPlan && $userPlan->id === $plan->id;
                $appLimit  = $plan->applications_limit;
                $creditLim = $plan->ai_credits;
                $features  = is_array($plan->features) ? $plan->features : [];
                $savings   = $features['savings_percentage'] ?? null;
                $isUpgrade = $userPlan && $plan->price_monthly > $userPlan->price_monthly;
                $t         = $planThemes[$i % 3];
            @endphp
            <div class="plan-card animate-card delay-{{ ($i%3)+1 }} relative flex flex-col rounded-3xl overflow-hidden" style="{{ $plan->is_featured ? 'box-shadow:0 20px 60px rgba(124,58,237,.25);border:2px solid '.$t['from'] : 'border:1.5px solid #e5e7eb;box-shadow:0 4px 24px rgba(0,0,0,.06)' }};background:#fff">
                @if($savings)<div class="py-2 text-center text-xs font-bold text-white tracking-wide uppercase" style="background:linear-gradient(90deg,#059669,#10b981)">Save {{ $savings }}% vs Monthly</div>@endif
                @if($plan->is_featured)<div class="absolute top-5 right-5"><span class="inline-flex px-3 py-1 rounded-full text-xs font-bold text-white" style="background:linear-gradient(135deg,{{ $t['from'] }},{{ $t['to'] }})">Best Value</span></div>@endif
                <div class="p-8 flex-1 flex flex-col">
                    <div class="mb-6">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4" style="background:{{ $t['light'] }}"><svg class="w-6 h-6" style="color:{{ $t['from'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        <h3 class="text-xl font-extrabold text-gray-900">{{ $plan->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $plan->description }}</p>
                    </div>
                    <div class="mb-8">
                        <div class="flex items-baseline gap-1"><span class="text-5xl font-extrabold" style="color:{{ $t['from'] }}">&#8377;{{ number_format($plan->price_monthly) }}</span><span class="text-gray-400 font-medium">/mo</span></div>
                        <p class="mt-1 text-sm text-gray-400">Billed &#8377;{{ number_format($plan->price) }}/year &bull; Cancel anytime</p>
                    </div>
                    @php $tierF = $tierFeatures[$plan->slug] ?? $tierFeatures['pro-annual']; @endphp
                    {{-- What this plan is for --}}
                    <div class="mb-4 px-3 py-2.5 rounded-xl" style="background:{{ $t['light'] }};border:1px solid {{ $t['from'] }}22">
                        <p class="text-xs font-semibold mb-0.5" style="color:{{ $t['from'] }}">{{ $tierF['tagline'] }}</p>
                        <p class="text-xs text-gray-500">Best for: {{ $tierF['for'] }}</p>
                    </div>
                    {{-- Included features --}}
                    <ul class="space-y-2.5 flex-1 mb-8">
                        @foreach($tierF['included'] as $feat)
                        <li class="flex items-start gap-2.5">
                            <svg class="check-icon flex-shrink-0 w-4 h-4 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-sm text-gray-700">{{ $feat }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @auth
                        @if($isCurrent)<div class="w-full py-3.5 px-6 text-center rounded-2xl font-semibold text-sm" style="background:#f3f4f6;color:#6b7280">&#10003; Current Plan</div>
                        @else<a href="{{ route('subscriptions.select-plan', ['plan_id' => $plan->id]) }}" class="shimmer-btn block w-full py-3.5 px-6 text-center rounded-2xl font-bold text-sm text-white transition-all hover:shadow-xl hover:-translate-y-0.5" style="background:{{ $t['btn'] }}">{{ $isUpgrade ? 'Upgrade &amp; Save' : 'Get Annual Plan' }}</a>@endif
                    @else
                        <a href="{{ route('register') }}" class="shimmer-btn block w-full py-3.5 px-6 text-center rounded-2xl font-bold text-sm text-white transition-all hover:shadow-xl hover:-translate-y-0.5" style="background:linear-gradient(135deg,#059669,#10b981)">Get Started &amp; Save 25%</a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- GUARANTEE BAR --}}
<div class="py-6 px-4 text-center" style="background:linear-gradient(135deg,#ecfdf5,#f0fdf4)">
    <div class="flex flex-wrap justify-center gap-6 text-sm font-medium" style="color:#065f46">
        @foreach(['7-day money-back guarantee','Cancel anytime','No hidden fees','SSL secured','24/7 AI support'] as $g)
        <span class="flex items-center gap-1.5"><svg class="w-4 h-4" style="color:#10b981" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>{{ $g }}</span>
        @endforeach
    </div>
</div>

{{-- FAQ --}}
<div class="py-20 px-4" style="background:#fff">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-3">Frequently Asked Questions</h2>
            <p class="text-gray-500">Everything you need to know about StudAI Hire pricing.</p>
        </div>
        <div class="space-y-3">
            @foreach([
                ['q'=>'Can I change plans anytime?','a'=>"Yes! Upgrade or downgrade at any time. Changes take effect immediately and we'll prorate the difference.",'color'=>'#6366f1'],
                ['q'=>'What payment methods do you accept?','a'=>'We accept all major credit/debit cards, UPI, net banking, and digital wallets through Razorpay and PayU.','color'=>'#7c3aed'],
                ['q'=>'Is there a refund policy?','a'=>'Yes � 7-day money-back guarantee, no questions asked.','color'=>'#0891b2'],
                ['q'=>'What counts as an AI credit?','a'=>'Each AI action (resume analysis, cover letter, interview question, salary insight) uses 1 credit. Complex tasks use 2-3 credits.','color'=>'#059669'],
                ['q'=>'Do unused credits roll over?','a'=>'Applications and AI credits reset each month and do not roll over.','color'=>'#d97706'],
            ] as $faq)
            <details class="group rounded-2xl overflow-hidden" style="border:1.5px solid #e5e7eb;background:#fff">
                <summary class="flex items-center justify-between px-6 py-4 cursor-pointer list-none select-none">
                    <span class="text-sm font-semibold text-gray-900">{{ $faq['q'] }}</span>
                    <svg class="faq-icon w-5 h-5 flex-shrink-0 ml-4" style="color:{{ $faq['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-6 pb-5 text-sm text-gray-600 leading-relaxed" style="border-top:1px solid #f3f4f6"><div class="pt-3">{{ $faq['a'] }}</div></div>
            </details>
            @endforeach
        </div>
    </div>
</div>

{{-- BOTTOM CTA --}}
<div class="py-20 px-4 text-center relative overflow-hidden" style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 50%,#a855f7 100%)">
    <div class="relative max-w-2xl mx-auto">
        <h2 class="text-4xl font-extrabold text-white mb-4">Ready to put your career on autopilot?</h2>
        <p class="mb-8 text-lg" style="color:rgba(255,255,255,.8)">Start free today. No credit card required. 50,000+ professionals trust StudAI Hire.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl font-bold text-indigo-700 transition-all hover:shadow-2xl hover:-translate-y-1" style="background:#fff">
                Get Started Free
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            @if(Route::has('contact'))
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl font-bold text-white border-2 hover:bg-white/10 transition-all" style="border-color:rgba(255,255,255,.3)">Talk to Sales</a>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    var toggle=document.getElementById('billing-toggle'),dot=document.getElementById('toggle-dot'),lY=document.getElementById('label-yearly'),lM=document.getElementById('label-monthly'),monthly=document.getElementById('monthly-plans'),yearly=document.getElementById('yearly-plans');
    if(!toggle)return;
    var isYearly=false;
    toggle.addEventListener('click',function(){
        isYearly=!isYearly;
        if(isYearly){toggle.style.background='#6366f1';dot.style.transform='translateX(2rem)';lY.style.fontWeight='700';lY.style.color='#111827';lM.style.fontWeight='400';lM.style.color='#6b7280';monthly.classList.add('hidden');yearly.classList.remove('hidden');}
        else{toggle.style.background='#e5e7eb';dot.style.transform='translateX(0.25rem)';lM.style.fontWeight='700';lM.style.color='#111827';lY.style.fontWeight='400';lY.style.color='#6b7280';monthly.classList.remove('hidden');yearly.classList.add('hidden');}
    });
    var cards=document.querySelectorAll('.animate-card');
    var obs=new IntersectionObserver(function(entries){entries.forEach(function(e){if(e.isIntersecting){e.target.style.animationPlayState='running';obs.unobserve(e.target);}});},{threshold:0.1});
    cards.forEach(function(c){c.style.animationPlayState='paused';obs.observe(c);});
})();
</script>
@endpush
</x-marketing-layout>