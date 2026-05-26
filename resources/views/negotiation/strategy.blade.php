@extends('layouts.dashboard')

@section('title', 'Strategy Analyzer - ' . strtoupper($strategy->role))

@php
    $csd = $strategy->company_salary_data ?? [];
    $conservative = (float) ($csd['conservative'] ?? $strategy->minimum_acceptable ?? ($strategy->optimal_ask * 0.95));
    $competitive  = (float) ($csd['competitive']  ?? $strategy->optimal_ask);
    $aggressive   = (float) ($csd['aggressive']   ?? $strategy->stretch_goal ?? ($strategy->optimal_ask * 1.12));
    $probCons     = (int)   ($csd['prob_conservative'] ?? 80);
    $probComp     = (int)   ($csd['prob_competitive']  ?? 60);
    $probAgg      = (int)   ($csd['prob_aggressive']   ?? 35);
    $marketDemand = ucfirst($csd['demand'] ?? 'medium');
    $marketTrend  = ucfirst($csd['trend'] ?? 'stable');
    $yoyChange    = (float) ($csd['yoy_change'] ?? 0);
    $aiRationale  = $csd['ai_rationale'] ?? '';

    $offered    = (float) $strategy->offered_salary;
    $percentile = (float) $strategy->offered_salary_percentile;
    $strength   = $strategy->offer_strength;
    $strengthLabel = match($strength) {
        'excellent' => 'Above Market', 'good' => 'At Market', 'fair' => 'Fair', default => 'Below Market',
    };
    $strengthColor = match($strength) {
        'excellent' => '#16a34a', 'good' => '#2563eb', 'fair' => '#d97706', default => '#dc2626',
    };

    $gainCons = $offered > 0 ? round((($conservative - $offered) / $offered) * 100, 1) : 0;
    $gainComp = $offered > 0 ? round((($competitive  - $offered) / $offered) * 100, 1) : 0;
    $gainAgg  = $offered > 0 ? round((($aggressive   - $offered) / $offered) * 100, 1) : 0;

    $totalComp  = $strategy->total_comp_optimization ?? [];
    $bonusPct   = is_array($totalComp) ? ($totalComp['target_bonus'] ?? 15) : 15;
    $baseBonus  = round($competitive * ($bonusPct / 100), 1);
    $annualComp = round($competitive + $baseBonus, 1);
    $benefits   = is_array($strategy->benefits_to_negotiate) ? $strategy->benefits_to_negotiate : [];

    $cultureRaw  = $strategy->company_culture_analysis ?? [];
    $cultureText = is_array($cultureRaw) ? ($cultureRaw['analysis'] ?? ($cultureRaw[0] ?? '')) : (string) $cultureRaw;

    $rawSummary  = preg_replace('/^#{1,6}\s+.+$/m', '', trim($strategy->ai_summary ?? ''));
    $rawSummary  = preg_replace('/\*\*([^*]+)\*\*/', '$1', $rawSummary);
    $sumLines    = array_values(array_filter(array_map('trim', explode("\n", $rawSummary))));
    $shortSummary = \Illuminate\Support\Str::limit(implode(' ', array_slice($sumLines, 0, 2)), 220);

    $nextSteps = [];
    if ($percentile < 50) {
        $nextSteps[] = 'Research ' . $strategy->company_name . ' on Glassdoor/LinkedIn to validate the salary band.';
        $nextSteps[] = 'Prepare a 2-minute value pitch: 2�3 achievements with numbers (revenue saved, users shipped, etc.).';
        $nextSteps[] = 'Counter within 24�48 hours � target ?' . number_format($competitive) . ' LPA (competitive ask at 75th percentile).';
    } else {
        $nextSteps[] = 'Ask for a written breakdown: base + bonus structure + equity + benefits before countering.';
        $nextSteps[] = 'Counter at ?' . number_format($competitive) . ' LPA citing market alignment � the data backs you up.';
        $nextSteps[] = 'If base is capped, negotiate: signing bonus, 6-month review clause, or extra PTO days.';
    }
    $nextSteps[] = 'Use "View Scripts" for a word-for-word email � never negotiate verbally first if you can avoid it.';
    $nextSteps[] = 'If rejected: ask "What would it take for this role to reach &#8377;' . number_format($competitive) . ' LPA?" � keep it open.';
@endphp

@section('content')
<div class="min-h-screen py-6" style="background:#f8fafc;">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

{{-- Back --}}
<div class="mb-5">
    <a href="{{ route('negotiation.dashboard') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-800 transition-colors font-medium">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Dashboard
    </a>
</div>

{{-- Header --}}
<div class="rounded-2xl shadow-lg mb-6 overflow-hidden">
    <div class="px-8 py-6 text-white" style="background:linear-gradient(135deg,#1e1b4b 0%,#3730a3 55%,#4f46e5 100%);">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-widest text-indigo-200">Negotiation Strategy</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" style="background:rgba(255,255,255,0.15);">{{ $strategy->confidence_score }}% Confidence</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white mb-1">{{ strtoupper($strategy->role) }}</h1>
                <p class="text-indigo-200 text-base">{{ $strategy->company_name }} &nbsp;�&nbsp; {{ $strategy->location }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-6">
                <div class="text-center">
                    <p class="text-xs text-indigo-300 mb-1">Current Offer</p>
                    <p class="text-2xl font-bold text-white">&#8377;{{ number_format($offered) }} <span class="text-sm font-normal">LPA</span></p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-indigo-300 mb-1">Market Position</p>
                    <p class="text-2xl font-bold" style="color:{{ $strengthColor }};">{{ $strengthLabel }}</p>
                    <p class="text-xs text-indigo-300">{{ round($percentile) }}th percentile</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-indigo-300 mb-1">Demand</p>
                    <p class="text-2xl font-bold text-white">{{ $marketDemand }}</p>
                    @if($yoyChange != 0)
                    <p class="text-xs {{ $yoyChange > 0 ? 'text-green-300' : 'text-red-300' }}">{{ $yoyChange > 0 ? '+' : '' }}{{ $yoyChange }}% YoY</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($aiRationale)
    <div class="px-8 py-3 text-sm text-indigo-900" style="background:#eef2ff;">
        <span class="font-semibold">?? Market Intel:</span> {{ $aiRationale }}
    </div>
    @endif
</div>

{{-- 3-Tier Strategy Cards --}}
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Negotiation Tiers � Choose Your Strategy</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Conservative --}}
        <div class="bg-white rounded-2xl border-2 border-green-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Conservative</span>
                <span class="text-xs text-gray-400 font-medium">~{{ $probCons }}% success</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mb-1">&#8377;{{ number_format($conservative) }} <span class="text-base font-normal text-gray-400">LPA</span></p>
            <p class="text-sm font-semibold {{ $gainCons > 0 ? 'text-green-600' : 'text-gray-400' }} mb-3">{{ $gainCons > 0 ? '+' . $gainCons . '% above offer' : 'At offer level' }}</p>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-4"><div class="bg-green-500 h-2 rounded-full" style="width:{{ $probCons }}%"></div></div>
            <p class="text-xs text-gray-500 leading-relaxed">Low-risk ask. Use when the company has rigid pay bands or when you prioritize certainty over maximizing gain.</p>
        </div>
        {{-- Competitive --}}
        <div class="rounded-2xl border-2 border-indigo-400 shadow-xl p-6 relative" style="background:linear-gradient(150deg,#eef2ff 0%,#f0fdf4 100%);">
            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 whitespace-nowrap">
                <span class="px-4 py-1 rounded-full text-xs font-bold bg-indigo-600 text-white shadow-md">? Recommended</span>
            </div>
            <div class="flex items-center justify-between mb-3 mt-1">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">Competitive</span>
                <span class="text-xs text-gray-400 font-medium">~{{ $probComp }}% success</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mb-1">&#8377;{{ number_format($competitive) }} <span class="text-base font-normal text-gray-400">LPA</span></p>
            <p class="text-sm font-semibold {{ $gainComp > 0 ? 'text-indigo-600' : 'text-gray-400' }} mb-3">{{ $gainComp > 0 ? '+' . $gainComp . '% above offer' : 'Market rate' }}</p>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-4"><div class="bg-indigo-500 h-2 rounded-full" style="width:{{ $probComp }}%"></div></div>
            <p class="text-xs text-gray-600 leading-relaxed">Targets the 75th percentile. Market data supports this number. Best balance of ambition and probability of success.</p>
        </div>
        {{-- Aggressive --}}
        <div class="bg-white rounded-2xl border-2 border-orange-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">Aggressive</span>
                <span class="text-xs text-gray-400 font-medium">~{{ $probAgg }}% success</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mb-1">&#8377;{{ number_format($aggressive) }} <span class="text-base font-normal text-gray-400">LPA</span></p>
            <p class="text-sm font-semibold {{ $gainAgg > 0 ? 'text-orange-600' : 'text-gray-400' }} mb-3">{{ $gainAgg > 0 ? '+' . $gainAgg . '% above offer' : 'Stretch ask' }}</p>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-4"><div class="bg-orange-500 h-2 rounded-full" style="width:{{ $probAgg }}%"></div></div>
            <p class="text-xs text-gray-500 leading-relaxed">90th percentile target. Use only if you have competing offers, rare skills, or are willing to walk away.</p>
        </div>
    </div>
</div>

{{-- Market + Compensation --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    {{-- Market Position --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-lg font-bold text-gray-900">Market Position</h2>
            @if($aiRationale)
            <span class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-full font-medium">AI-Powered</span>
            @else
            <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full">Estimated</span>
            @endif
        </div>
        <p class="text-xs text-gray-400 mb-5">{{ $strategy->role }} � {{ $strategy->location }} � {{ $strategy->years_experience }}yr exp</p>
        <div style="height:140px;"><canvas id="marketChart"></canvas></div>
        <div class="grid grid-cols-5 gap-1 text-center mt-4">
            @php
            $cols = [
                ['25th', number_format($strategy->market_percentile_25 ?? 0), '#9ca3af'],
                ['Median', number_format($strategy->market_median ?? 0), '#374151'],
                ['75th', number_format($strategy->market_percentile_75 ?? 0), '#10b981'],
                ['90th', number_format($strategy->market_percentile_90 ?? 0), '#f59e0b'],
                ['Offer', number_format($offered), '#4f46e5'],
            ];
            @endphp
            @foreach($cols as $col)
            <div>
                <p class="text-[10px] text-gray-400">{{ $col[0] }}</p>
                <p class="text-sm font-bold" style="color:{{ $col[2] }};">&#8377;{{ $col[1] }}</p>
            </div>
            @endforeach
        </div>
        <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-500">
            <span>?? Trend: <strong>{{ $marketTrend }}</strong>{{ $yoyChange != 0 ? ' (' . ($yoyChange > 0 ? '+' : '') . $yoyChange . '% YoY)' : '' }}</span>
            <span>?? Demand: <strong>{{ $marketDemand }}</strong></span>
        </div>
    </div>

    {{-- Compensation Breakdown --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-1">Total Compensation Breakdown</h2>
        <p class="text-xs text-gray-400 mb-4">Competitive ask: &#8377;{{ number_format($competitive) }} LPA</p>
        <div class="space-y-0">
            @foreach([
                ['Base Salary', 'Fixed monthly', '&#8377;' . number_format($competitive) . ' LPA', '#1e1b4b'],
                ['Annual Bonus', '~' . $bonusPct . '% of base (negotiate up)', '&#8377;' . number_format($baseBonus, 1) . ' LPA', '#16a34a'],
                ['Equity / RSUs', '4-year vest � push for this', 'Negotiate', '#4f46e5'],
                ['Signing Bonus', 'One-time � ask if base is capped', 'Negotiate', '#7c3aed'],
                ['Extra PTO', '+5 days is standard ask', '?0.3�0.5L equiv.', '#d97706'],
                ['Learning Budget', 'Courses, certs, conferences', '?0.5�1L equiv.', '#0891b2'],
            ] as [$name, $sub, $val, $color])
            <div class="flex justify-between items-center py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $name }}</p>
                    <p class="text-xs text-gray-400">{{ $sub }}</p>
                </div>
                <p class="text-sm font-bold" style="color:{{ $color }};">{{ $val }}</p>
            </div>
            @endforeach
        </div>
        <div class="mt-3 flex justify-between items-center bg-indigo-50 px-4 py-3 rounded-xl">
            <div>
                <p class="text-sm font-bold text-gray-900">Total Package</p>
                <p class="text-xs text-gray-500">Base + bonus (equity excluded)</p>
            </div>
            <p class="text-lg font-extrabold text-indigo-700">&#8377;{{ number_format($annualComp, 1) }} LPA</p>
        </div>
    </div>
</div>

{{-- Leverage + Company --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    {{-- Leverage --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Your Negotiation Leverage</h2>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div style="height:160px;"><canvas id="leverageChart"></canvas></div>
            <div class="space-y-2 flex flex-col justify-center">
                @foreach(array_slice($strategy->strongest_points ?? [], 0, 4) as $pt)
                <div class="flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <p class="text-xs text-gray-700">{{ is_array($pt) ? ($pt['point'] ?? '') : $pt }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @if(!empty($strategy->risk_factors))
        <div class="border-t border-gray-100 pt-3">
            <p class="text-[10px] font-bold text-red-400 uppercase mb-2">Watch Out For</p>
            @foreach(array_slice($strategy->risk_factors, 0, 2) as $risk)
            <div class="flex items-start gap-2 mb-1.5">
                <svg class="w-3.5 h-3.5 text-orange-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <p class="text-xs text-gray-600">{{ is_array($risk) ? (($risk['factor'] ?? '') . (isset($risk['impact']) ? ' � ' . $risk['impact'] : '')) : $risk }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Company + Tactics --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Company Tactics</h2>
        <div class="grid grid-cols-3 gap-3 mb-5">
            @foreach([
                ['Flexibility', ucfirst($strategy->company_negotiation_flexibility ?? 'medium'), $strategy->company_negotiation_flexibility === 'high' ? '#16a34a' : ($strategy->company_negotiation_flexibility === 'low' ? '#dc2626' : '#d97706')],
                ['Tone', ucfirst($strategy->recommended_tone ?? 'Confident'), '#4f46e5'],
                ['Timing', ucwords(str_replace('_',' ',$strategy->recommended_timing ?? '48h')), '#374151'],
            ] as [$label, $val, $col])
            <div class="text-center p-3 rounded-xl bg-gray-50">
                <p class="text-[10px] text-gray-400 mb-1">{{ $label }}</p>
                <p class="font-bold text-sm" style="color:{{ $col }};">{{ $val }}</p>
            </div>
            @endforeach
        </div>
        @if(!empty($strategy->recommended_tactics))
        <div class="mb-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Recommended Tactics</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach(array_slice($strategy->recommended_tactics, 0, 6) as $t)
                <span class="px-2.5 py-1 rounded-full text-xs font-medium border border-indigo-200 bg-indigo-50 text-indigo-700">{{ ucwords(str_replace('_',' ',$t)) }}</span>
                @endforeach
            </div>
        </div>
        @endif
        @if($cultureText)
        <div class="bg-blue-50 rounded-xl p-4">
            <p class="text-[10px] font-bold text-blue-600 uppercase mb-1">Culture Signal</p>
            <p class="text-xs text-gray-700 leading-relaxed">{{ \Illuminate\Support\Str::limit(strip_tags(preg_replace('/\*\*([^*]+)\*\*/', '$1', preg_replace('/^#{1,6}\s.+$/m','', $cultureText))), 180) }}</p>
        </div>
        @endif
    </div>
</div>

{{-- AI Insight --}}
@if($shortSummary)
<div class="rounded-2xl shadow-sm mb-6 overflow-hidden">
    <div class="px-6 py-4 text-white flex items-start gap-3" style="background:linear-gradient(90deg,#4f46e5,#7c3aed);">
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mt-0.5 text-base">??</div>
        <div>
            <p class="font-bold text-sm mb-0.5">AI Assessment</p>
            <p class="text-sm text-white/90">{{ $shortSummary }}</p>
        </div>
    </div>
    @if(!empty($strategy->ai_warnings))
    <div class="px-6 py-3 bg-amber-50 border-t border-amber-100 flex items-start gap-2">
        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <p class="text-xs text-amber-700">{{ is_array($strategy->ai_warnings) ? implode(' � ', $strategy->ai_warnings) : $strategy->ai_warnings }}</p>
    </div>
    @endif
</div>
@endif

{{-- Action Plan --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4">&#128203; Your Action Plan</h2>
    <div class="space-y-3">
        @foreach($nextSteps as $i => $step)
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</div>
            <p class="text-sm text-gray-700 pt-0.5 leading-relaxed">{{ $step }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- Readiness compact --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-bold text-gray-900">Preparation Readiness</h2>
        <span class="text-2xl font-extrabold text-indigo-600">{{ $readinessScore }}%</span>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-2 mb-5">
        <div class="h-2 rounded-full" style="width:{{ $readinessScore }}%;background:linear-gradient(90deg,#4f46e5,#ec4899);"></div>
    </div>
    <div class="grid grid-cols-5 gap-2 text-center">
        @foreach($readinessFactors as $f)
        <div>
            <p class="text-[10px] text-gray-400 mb-1">{{ $f['name'] }}</p>
            <div class="w-full bg-gray-100 rounded-full h-1 mb-1">
                <div class="h-1 rounded-full {{ $f['status']==='complete' ? 'bg-green-500' : ($f['status']==='partial' ? 'bg-yellow-400' : 'bg-gray-300') }}" style="width:{{ ($f['points']/($f['max_points']??20))*100 }}%"></div>
            </div>
            <p class="text-xs font-bold text-gray-700">{{ $f['points'] }}/{{ $f['max_points']??20 }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- Action Buttons --}}
<div class="flex flex-wrap gap-3 justify-center pb-8">
    <a href="{{ route('negotiation.scenarios', $strategy->id) }}" class="px-7 py-3 rounded-xl font-bold text-sm text-white shadow-lg transition-all hover:scale-105" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">View Scenarios &rarr;</a>
    <a href="{{ route('negotiation.scripts', $strategy->id) }}" class="px-7 py-3 rounded-xl font-bold text-sm text-white shadow-lg transition-all hover:scale-105" style="background:linear-gradient(135deg,#4f46e5,#1A73E8);">View Scripts &rarr;</a>
    <a href="{{ route('negotiation.chatbot') }}" class="px-7 py-3 rounded-xl font-bold text-sm text-white shadow-lg transition-all hover:scale-105" style="background:linear-gradient(135deg,#059669,#0d9488);">&#129302; AI Negotiation Agent</a>
    @if($strategy->sessions()->where('outcome', null)->exists())
    <a href="{{ route('negotiation.coaching', $strategy->sessions()->where('outcome', null)->first()->id) }}" class="px-7 py-3 rounded-xl font-bold text-sm text-white shadow-lg transition-all hover:scale-105" style="background:linear-gradient(135deg,#1A73E8,#0B57D0);">Resume Coaching &rarr;</a>
    @else
    <button onclick="startCoachingSession()" class="px-7 py-3 rounded-xl font-bold text-sm text-white shadow-lg transition-all hover:scale-105" style="background:linear-gradient(135deg,#1A73E8,#0B57D0);">Start Live Coaching &rarr;</button>
    @endif
</div>

</div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('marketChart').getContext('2d'), {
    type:'bar',
    data:{
        labels:['25th','Median','75th','90th','Your Offer'],
        datasets:[{
            data:[{{ $strategy->market_percentile_25??0 }},{{ $strategy->market_median??0 }},{{ $strategy->market_percentile_75??0 }},{{ $strategy->market_percentile_90??0 }},{{ $offered }}],
            backgroundColor:['#d1d5db','#374151','#10b981','#f59e0b','#4f46e5'],
            borderRadius:6,
        }]
    },
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>'\u20B9'+c.raw+' LPA'}}},scales:{y:{beginAtZero:false,ticks:{callback:v=>'\u20B9'+v}}}}
});

@php $ld = $leverageAnalysis ?? ['market_position'=>60,'experience'=>50,'skills'=>50,'alternatives'=>40]; @endphp
new Chart(document.getElementById('leverageChart').getContext('2d'), {
    type:'radar',
    data:{
        labels:['Market','Experience','Skills','Alternatives'],
        datasets:[{data:[{{ $ld['market_position']??50 }},{{ $ld['experience']??50 }},{{ $ld['skills']??50 }},{{ $ld['alternatives']??40 }}],backgroundColor:'rgba(99,102,241,0.15)',borderColor:'#6366f1',borderWidth:2,pointBackgroundColor:'#6366f1',pointRadius:3}]
    },
    options:{responsive:true,maintainAspectRatio:false,scales:{r:{beginAtZero:true,max:100,ticks:{stepSize:25,font:{size:9}},pointLabels:{font:{size:9}}}},plugins:{legend:{display:false}}}
});

async function startCoachingSession() {
    try {
        const r = await fetch('/api/negotiation/session', {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({strategy_id:{{ $strategy->id }},session_type:'live_coaching',communication_mode:'email'})});
        const d = await r.json();
        if(d.success) window.location.href='/negotiation/coaching/'+d.session.id;
        else alert('Could not start: '+d.message);
    } catch(e){alert('Error. Please try again.');}
}
</script>
@endpush
@endsection