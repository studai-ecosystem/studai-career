@extends('layouts.dashboard')
@section('title', $project->title . ' - StudAI Hire')

@push('styles')
<style>
/* Fiverr-style gig page */
.gig-tab { border-bottom: 2px solid transparent; padding-bottom: 14px; color: #62626a; font-weight:500; transition: all .15s; }
.gig-tab.active, .gig-tab:hover { border-color: #1A73E8; color: #1A73E8; }
.pkg-tab { padding: 12px 20px; font-weight:600; font-size:.875rem; transition: all .15s; cursor:pointer; border-bottom:3px solid transparent; }
.pkg-tab.active { border-color: #1A73E8; color: #1A73E8; }
.pkg-tab:not(.active) { color:#62626a; }
.check-row { display:flex; align-items:center; gap:8px; padding:8px 0; border-bottom:1px solid #f0f0f0; font-size:.875rem; color:#404145; }
.check-row:last-child { border:none; }
.check-icon { color:#1dbf73; font-weight:bold; flex-shrink:0; }
.cross-icon { color:#c5c6c9; flex-shrink:0; }
.seller-stat { text-align:center; padding:0 16px; }
.seller-stat:not(:last-child) { border-right:1px solid #e4e5e7; }
.badge-online { width:10px;height:10px;background:#1dbf73;border-radius:50%;border:2px solid #fff;display:inline-block;vertical-align:middle;margin-right:4px; }
</style>
@endpush

@section('content')
@php
$catStyles = [
    'web_development'    => ['bg' => 'linear-gradient(135deg,#3b82f6,#4f46e5)', 'icon' => '💻', 'label' => 'Programming & Tech'],
    'mobile_development' => ['bg' => 'linear-gradient(135deg,#a855f7,#ec4899)', 'icon' => '📱', 'label' => 'Mobile Apps'],
    'design'             => ['bg' => 'linear-gradient(135deg,#ec4899,#f43f5e)', 'icon' => '🎨', 'label' => 'Graphics & Design'],
    'writing'            => ['bg' => 'linear-gradient(135deg,#f59e0b,#f97316)', 'icon' => '✍️', 'label' => 'Writing & Translation'],
    'marketing'          => ['bg' => 'linear-gradient(135deg,#22c55e,#14b8a6)', 'icon' => '📣', 'label' => 'Digital Marketing'],
    'data_science'       => ['bg' => 'linear-gradient(135deg,#06b6d4,#3b82f6)', 'icon' => '📊', 'label' => 'Data Science & AI'],
    'ai_ml'              => ['bg' => 'linear-gradient(135deg,#7c3aed,#9333ea)', 'icon' => '🤖', 'label' => 'AI & Machine Learning'],
    'devops'             => ['bg' => 'linear-gradient(135deg,#64748b,#374151)', 'icon' => '⚙️', 'label' => 'DevOps & Cloud'],
];
$style = $catStyles[$project->category] ?? ['bg' => 'linear-gradient(135deg,#3b82f6,#4f46e5)', 'icon' => '💼', 'label' => 'Services'];
$skillsRaw = $project->skills_required;
$skills = is_array($skillsRaw) ? $skillsRaw : (json_decode($skillsRaw ?? '[]', true) ?: []);
$employer = $project->employer;
$midBudget = intval((($project->budget_min ?? 5000) + ($project->budget_max ?? 20000)) / 2);
$packages = [
    ['label'=>'Basic',    'price'=>$project->budget_min ?? 5000,  'days'=>intval(($project->estimated_duration_days??30)*.4), 'revisions'=>2],
    ['label'=>'Standard', 'price'=>$midBudget,                    'days'=>intval(($project->estimated_duration_days??30)*.7), 'revisions'=>5],
    ['label'=>'Premium',  'price'=>$project->budget_max ?? 20000, 'days'=>$project->estimated_duration_days??30,             'revisions'=>99],
];
@endphp

<div class="min-h-screen" style="background:#f9f9f9;">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-5 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('marketplace.index') }}" class="hover:text-blue-600">Marketplace</a>
        <span>›</span>
        <a href="{{ route('marketplace.projects') }}" class="hover:text-blue-600">{{ $style['label'] }}</a>
        <span>›</span>
        <span class="text-gray-700 truncate max-w-xs">{{ Str::limit($project->title, 50) }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8 items-start">

        {{-- LEFT COLUMN --}}
        <div class="flex-1 min-w-0">

            {{-- Title & badges --}}
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full" style="background:#eff6ff;color:#1d4ed8;">
                    {{ $style['label'] }}
                </span>
                @if($project->is_urgent)
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full" style="background:#fef2f2;color:#b91c1c;">🔥 Urgent</span>
                @endif
                @if($project->is_featured)
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full" style="background:#fefce8;color:#92400e;">⭐ Featured</span>
                @endif
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight mb-5">{{ $project->title }}</h1>

            {{-- Seller mini-bar --}}
            <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-200 flex-wrap">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($employer->name ?? 'Client') }}&size=48&background=1A73E8&color=fff&rounded=true"
                         class="rounded-full" style="width:48px;height:48px;" alt="">
                    <span class="absolute bottom-0 right-0 block" style="width:11px;height:11px;background:#1dbf73;border-radius:50%;border:2px solid #fff;"></span>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $employer->name ?? 'Client' }}</p>
                    <p class="text-xs text-gray-500">{{ $employer->email ?? 'Verified Client' }}</p>
                </div>
                <div class="flex items-center gap-1 ml-2">
                    <span style="color:#ffb33e;">★★★★★</span>
                    <span class="text-xs font-bold text-gray-800">5.0</span>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-bold rounded" style="background:#1dbf73;color:#fff;">PRO CLIENT</span>
                </div>
            </div>

            {{-- Gig banner --}}
            <div class="rounded-2xl overflow-hidden mb-6 shadow-sm flex items-center justify-center relative"
                 style="height:300px;background:{{ $style['bg'] }};">
                <span style="font-size:80px;filter:drop-shadow(0 8px 24px rgba(0,0,0,.2));">{{ $style['icon'] }}</span>
                <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.35),transparent 50%);"></div>
                <div style="position:absolute;bottom:20px;left:24px;right:24px;" class="flex flex-wrap gap-2">
                    @foreach(array_slice($skills, 0, 4) as $skill)
                        <span style="background:rgba(255,255,255,.25);color:#fff;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600;">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex gap-6 border-b border-gray-200 mb-6 overflow-x-auto" id="gig-tabs">
                <button onclick="switchTab('overview')" class="gig-tab active whitespace-nowrap" id="tab-overview">Overview</button>
                <button onclick="switchTab('description')" class="gig-tab whitespace-nowrap" id="tab-description">About this Project</button>
                <button onclick="switchTab('requirements')" class="gig-tab whitespace-nowrap" id="tab-requirements">Requirements & FAQ</button>
                <button onclick="switchTab('reviews')" class="gig-tab whitespace-nowrap" id="tab-reviews">Reviews</button>
            </div>

            {{-- Tab: Overview --}}
            <div id="panel-overview" class="tab-panel">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                    <h2 class="font-bold text-gray-900 text-lg mb-4">Project at a Glance</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 rounded-xl" style="background:#f0f4ff;">
                            <div class="text-2xl mb-1">📅</div>
                            <div class="font-bold text-gray-900 text-sm">{{ $project->estimated_duration_days ?? '—' }} Days</div>
                            <div class="text-xs text-gray-500">Duration</div>
                        </div>
                        <div class="text-center p-4 rounded-xl" style="background:#f0fdf4;">
                            <div class="text-2xl mb-1">💰</div>
                            <div class="font-bold text-gray-900 text-sm">₹{{ number_format($project->budget_min ?? 0) }}+</div>
                            <div class="text-xs text-gray-500">Budget</div>
                        </div>
                        <div class="text-center p-4 rounded-xl" style="background:#fdf4ff;">
                            <div class="text-2xl mb-1">📋</div>
                            <div class="font-bold text-gray-900 text-sm">{{ $project->proposals_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500">Proposals</div>
                        </div>
                        <div class="text-center p-4 rounded-xl" style="background:#fff7ed;">
                            <div class="text-2xl mb-1">👁️</div>
                            <div class="font-bold text-gray-900 text-sm">{{ $project->views_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500">Views</div>
                        </div>
                    </div>
                </div>

                {{-- Skills --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                    <h2 class="font-bold text-gray-900 text-lg mb-4">Skills Required</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($skills as $skill)
                            <a href="{{ route('marketplace.projects') }}"
                               class="px-4 py-2 rounded-full text-sm font-medium border border-gray-200 hover:border-blue-400 hover:text-blue-600 text-gray-700 transition">
                                {{ $skill }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Deliverables --}}
                @if($project->deliverables)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                        <h2 class="font-bold text-gray-900 text-lg mb-4">What You Will Deliver</h2>
                        <ul class="space-y-2">
                            @foreach(explode("\n", $project->deliverables) as $line)
                                @if(trim($line))
                                    <li class="flex items-start gap-2 text-gray-700 text-sm">
                                        <span style="color:#1dbf73;" class="mt-0.5 font-bold shrink-0">✓</span>
                                        {{ trim($line) }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Similar Projects --}}
                @if(isset($similarProjects) && $similarProjects->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="font-bold text-gray-900 text-lg mb-4">Similar Projects</h2>
                        <div class="space-y-3">
                            @foreach($similarProjects->take(4) as $sim)
                                <a href="{{ route('marketplace.project.show', $sim) }}"
                                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition group">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0"
                                         style="background:{{ $catStyles[$sim->category]['bg'] ?? 'linear-gradient(135deg,#3b82f6,#4f46e5)' }};">
                                        {{ $catStyles[$sim->category]['icon'] ?? '💼' }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 truncate">{{ $sim->title }}</p>
                                        <p class="text-xs text-gray-400">₹{{ number_format($sim->budget_min ?? 0) }} – ₹{{ number_format($sim->budget_max ?? 0) }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tab: Description --}}
            <div id="panel-description" class="tab-panel hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                    <h2 class="font-bold text-gray-900 text-lg mb-4">About This Project</h2>
                    <div class="text-gray-700 leading-relaxed text-sm whitespace-pre-line">{{ $project->description }}</div>
                </div>
            </div>

            {{-- Tab: Requirements & FAQ --}}
            <div id="panel-requirements" class="tab-panel hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                    <h2 class="font-bold text-gray-900 text-lg mb-4">Submission Requirements</h2>
                    <div class="text-gray-700 leading-relaxed text-sm whitespace-pre-line">{{ $project->requirements ?? 'Share your portfolio, relevant work experience, and why you are the best fit. Include links to similar completed projects. Availability for a 30-min discovery call is preferred.' }}</div>
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-3 text-sm">Include in your proposal:</h3>
                        <ul class="space-y-2">
                            @foreach(['Portfolio / past work samples', 'Your approach and methodology', 'Timeline and milestones breakdown', 'Your competitive rate', 'Questions about the project (optional)'] as $req)
                                <li class="flex items-center gap-2 text-sm text-gray-700">
                                    <span style="color:#1A73E8;" class="font-bold">→</span> {{ $req }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 text-lg mb-2">FAQ</h2>
                    @foreach([
                        ['q' => 'What is the payment process?', 'a' => 'Payments are held in escrow. Funds are released to the freelancer only after you approve each milestone.'],
                        ['q' => 'Can I request revisions?', 'a' => 'Yes. You can request revisions on any milestone before approving it. Disputes can be raised if needed.'],
                        ['q' => 'How do I select the right freelancer?', 'a' => 'Review proposals, check portfolio links, and message candidates before hiring from this project page.'],
                        ['q' => 'What if the project scope changes?', 'a' => 'The freelancer can propose additional milestones through the contract dashboard if scope increases.'],
                    ] as $faq)
                        <details class="border-b border-gray-100 last:border-none group">
                            <summary class="flex items-center justify-between py-3 cursor-pointer text-sm font-semibold text-gray-900">
                                {{ $faq['q'] }}
                                <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <p class="text-sm text-gray-600 pb-4 leading-relaxed">{{ $faq['a'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>

            {{-- Tab: Reviews --}}
            <div id="panel-reviews" class="tab-panel hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-6 mb-6 pb-6 border-b border-gray-100">
                        <div class="text-center">
                            <div class="text-5xl font-extrabold text-gray-900">5.0</div>
                            <div class="flex justify-center gap-0.5 my-1">
                                @for($i=0;$i<5;$i++) <span style="color:#ffb33e;font-size:1.1rem;">★</span> @endfor
                            </div>
                            <div class="text-xs text-gray-500">Based on {{ $project->proposals_count ?? 0 }} proposals</div>
                        </div>
                        <div class="flex-1">
                            @foreach([5,4,3,2,1] as $star)
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs text-gray-500 w-4">{{ $star }}</span>
                                    <div class="flex-1 rounded-full" style="background:#f0f0f0;height:6px;">
                                        <div class="rounded-full" style="background:#ffb33e;height:6px;width:{{ $star===5?'90%':($star===4?'7%':'1%') }};"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-center text-gray-400 py-6 text-sm">Reviews will appear once the project is completed.</p>
                </div>
            </div>

        </div>{{-- /left --}}

        {{-- RIGHT SIDEBAR --}}
        <div class="w-full lg:w-80 xl:w-96 shrink-0 sticky top-4">

            {{-- Package Card --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden mb-4">
                <div class="flex border-b border-gray-100">
                    @foreach($packages as $i => $pkg)
                        <button onclick="switchPkg({{ $i }})" id="pkg-tab-{{ $i }}"
                                class="pkg-tab flex-1 {{ $i===1?'active':'' }}">
                            {{ $pkg['label'] }}
                        </button>
                    @endforeach
                </div>
                @foreach($packages as $i => $pkg)
                    <div id="pkg-panel-{{ $i }}" class="{{ $i!==1?'hidden':'' }} p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <div class="text-2xl font-extrabold text-gray-900">₹{{ number_format($pkg['price']) }}</div>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $pkg['label'] }} package</p>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-gray-900 text-sm">{{ $pkg['days'] }}-day delivery</div>
                                <p class="text-xs text-gray-400">{{ $pkg['revisions'] === 99 ? 'Unlimited' : $pkg['revisions'] }} revisions</p>
                            </div>
                        </div>
                        @php
                        $allFeatures = ['Source files included','Commercial use license','Dedicated project manager','Post-launch support (30 days)','Priority support'];
                        $included = array_slice($allFeatures, 0, $i+2);
                        $excluded = array_slice($allFeatures, $i+2);
                        @endphp
                        <div class="space-y-0 mb-4">
                            @foreach($included as $f)
                                <div class="check-row"><span class="check-icon">✓</span><span>{{ $f }}</span></div>
                            @endforeach
                            @foreach($excluded as $f)
                                <div class="check-row" style="opacity:.4;"><span class="cross-icon">✕</span><span>{{ $f }}</span></div>
                            @endforeach
                        </div>
                        @auth
                            @if(auth()->id() !== ($project->employer_id ?? null))
                                @if($hasApplied ?? false)
                                    <div class="text-center py-3 rounded-xl mb-3 text-sm font-semibold" style="background:#f0fdf4;color:#15803d;">✓ Proposal Submitted</div>
                                    <a href="{{ route('marketplace.freelancer.proposals') }}"
                                       class="block w-full text-center py-3 rounded-xl text-sm font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                                        View My Proposals
                                    </a>
                                @else
                                    <button onclick="openProposalModal({{ $pkg['price'] }})"
                                            class="block w-full py-3 rounded-xl text-white font-bold text-base hover:opacity-90 transition mb-2"
                                            style="background:#1A73E8;">
                                        Submit Proposal → ₹{{ number_format($pkg['price']) }}
                                    </button>
                                    <button onclick="openContactModal()"
                                       class="block w-full text-center py-2.5 rounded-xl font-semibold text-sm border-2 hover:bg-blue-50 transition"
                                       style="border-color:#1A73E8;color:#1A73E8;">
                                        Contact Client
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('marketplace.employer.manage-project', $project) }}"
                                   class="block w-full text-center py-3 rounded-xl text-white font-bold text-sm" style="background:#1A73E8;">
                                    Manage Project
                                </a>
                                <a href="{{ route('marketplace.employer.edit-project', $project) }}"
                                   class="block w-full text-center py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition mt-2">
                                    Edit Project
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="block w-full text-center py-3 rounded-xl text-white font-bold text-base hover:opacity-90 transition"
                               style="background:#1A73E8;">
                                Login to Submit Proposal
                            </a>
                        @endauth
                    </div>
                @endforeach
            </div>

            {{-- Deadline card --}}
            @if($project->deadline)
                <div class="bg-white rounded-2xl border border-orange-200 p-4 mb-4 flex items-center gap-3">
                    <span class="text-2xl">⏰</span>
                    <div>
                        <p class="text-sm font-semibold text-orange-700">Deadline</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }} · {{ \Carbon\Carbon::parse($project->deadline)->diffForHumans() }}</p>
                    </div>
                </div>
            @endif

            {{-- Client card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-900 mb-4">About the Client</h3>
                <div class="flex items-center gap-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($employer->name ?? 'Client') }}&size=52&background=1A73E8&color=fff&rounded=true"
                         style="width:52px;height:52px;" class="rounded-full" alt="">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $employer->name ?? 'Client' }}</p>
                        <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                            <span style="width:8px;height:8px;background:#1dbf73;border-radius:50%;display:inline-block;"></span> Online
                        </p>
                    </div>
                </div>
                <div class="flex justify-around text-center mb-4 pb-4 border-b border-gray-100">
                    <div><p class="font-bold text-gray-900 text-sm">{{ $project->proposals_count ?? 0 }}</p><p class="text-xs text-gray-400">Proposals</p></div>
                    <div><p class="font-bold text-gray-900 text-sm">{{ $project->views_count ?? 0 }}</p><p class="text-xs text-gray-400">Views</p></div>
                    <div><p class="font-bold text-gray-900 text-sm">{{ $project->allows_remote ? '🌍' : '📍' }}</p><p class="text-xs text-gray-400">{{ $project->allows_remote ? 'Remote' : 'On-site' }}</p></div>
                </div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Posted</dt><dd class="font-medium text-gray-900">{{ $project->published_at?->diffForHumans() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Budget Type</dt><dd class="font-medium text-gray-900">{{ $project->project_type === 'fixed_price' ? 'Fixed Price' : 'Hourly' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Experience</dt><dd class="font-medium text-gray-900">{{ ucfirst($project->experience_level ?? 'Any') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Budget Range</dt><dd class="font-medium text-gray-900">₹{{ number_format($project->budget_min??0) }} – ₹{{ number_format($project->budget_max??0) }}</dd></div>
                </dl>
            </div>

        </div>{{-- /sidebar --}}
    </div>
</div>
</div>

{{-- Contact Client Modal --}}
<div id="contactModal" class="fixed inset-0 z-50 hidden" style="background:rgba(0,0,0,.5);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Contact Client</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Send a message to the project owner</p>
                </div>
                <button onclick="closeContactModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
            </div>
            @auth
            <form action="{{ route('marketplace.project.contact', $project) }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div class="flex items-center gap-3 p-3 rounded-xl" style="background:#f8faff;">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0" style="background:#1A73E8;">
                        {{ strtoupper(substr($project->employer?->name ?? 'C', 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-gray-900">{{ $project->employer?->name ?? 'Client' }}</p>
                        <p class="text-xs text-gray-500">Re: {{ Str::limit($project->title, 50) }}</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subject *</label>
                    <input type="text" name="subject" required maxlength="200"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                           value="Interested in your project: {{ Str::limit($project->title, 60) }}">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message *</label>
                    <textarea name="message" rows="5" required minlength="20" maxlength="2000"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                              placeholder="Hi, I saw your project and I'd love to learn more about it. I have experience with..."></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="submit" class="flex-1 py-3 text-white font-bold rounded-xl hover:opacity-90 transition" style="background:#1A73E8;">
                        Send Message
                    </button>
                    <button type="button" onclick="closeContactModal()"
                            class="px-5 py-3 border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </form>
            @else
            <div class="p-6 text-center">
                <p class="text-gray-600 mb-4">Please sign in to contact this client.</p>
                <a href="{{ route('login') }}" class="inline-block px-6 py-3 text-white font-bold rounded-xl" style="background:#1A73E8;">Sign In</a>
            </div>
            @endauth
        </div>
    </div>
</div>

{{-- Proposal Modal --}}
<div id="proposalModal" class="fixed inset-0 z-50 hidden" style="background:rgba(0,0,0,.5);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 text-lg">Submit Your Proposal</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
            </div>
            <form id="proposalForm" action="{{ route('marketplace.freelancer.submit-proposal', $project) }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-gray-700">Cover Letter *</label>
                        <button type="button" onclick="aiGenerateCoverLetter()"
                                id="aiGenerateBtn"
                                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all"
                                style="background:#eff6ff;color:#1A73E8;border:1px solid #bfdbfe;">
                            <span id="aiGenerateIcon">✨</span>
                            <span id="aiGenerateBtnText">AI Generate</span>
                        </button>
                    </div>
                    <textarea name="cover_letter" id="coverLetterArea" rows="5" required
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                              placeholder="Introduce yourself and explain why you are the best fit..."></textarea>
                    <p id="aiGenerateError" class="hidden mt-1 text-xs text-red-500"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bid Amount (₹) *</label>
                        <input type="number" name="proposed_amount" id="bidAmount" required min="1"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                               placeholder="{{ $project->budget_min ?? 5000 }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Delivery (days) *</label>
                        <input type="number" name="estimated_duration_days" required min="1"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                               value="{{ $project->estimated_duration_days ?? 30 }}"
                               placeholder="{{ $project->estimated_duration_days ?? 30 }}">
                    </div>
                </div>
                <div class="rounded-xl p-3 text-sm" style="background:#eff6ff;color:#1e40af;">
                    💡 <strong>Tip:</strong> Proposals with portfolio links and clear methodology get 3× more responses.
                </div>
                <div id="proposalError" class="hidden rounded-xl p-3 text-sm font-medium" style="background:#fef2f2;color:#dc2626;"></div>
                <div class="flex gap-3 pt-1">
                    <button type="submit" id="proposalSubmitBtn" class="flex-1 py-3 text-white font-bold rounded-xl hover:opacity-90 transition" style="background:#1A73E8;">
                        Submit Proposal
                    </button>
                    <button type="button" onclick="closeModal()"
                            class="px-5 py-3 border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchTab(name) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.gig-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('panel-' + name).classList.remove('hidden');
    document.getElementById('tab-' + name).classList.add('active');
}
function switchPkg(idx) {
    [0,1,2].forEach(i => {
        document.getElementById('pkg-panel-' + i).classList.add('hidden');
        document.getElementById('pkg-tab-' + i).classList.remove('active');
    });
    document.getElementById('pkg-panel-' + idx).classList.remove('hidden');
    document.getElementById('pkg-tab-' + idx).classList.add('active');
}
function openProposalModal(price) {
    document.getElementById('proposalModal').classList.remove('hidden');
    const bidInput = document.getElementById('bidAmount');
    if (bidInput) bidInput.value = price;
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('proposalModal').classList.add('hidden');
    document.body.style.overflow = '';
}
function openContactModal() {
    document.getElementById('contactModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.getElementById('proposalModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
document.getElementById('contactModal').addEventListener('click', function(e) {
    if (e.target === this) closeContactModal();
});

// Proposal form — AJAX submit
document.getElementById('proposalForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form    = this;
    const btn     = document.getElementById('proposalSubmitBtn');
    const errDiv  = document.getElementById('proposalError');

    btn.disabled = true;
    btn.textContent = 'Submitting…';
    errDiv.classList.add('hidden');

    try {
        const res  = await fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: new FormData(form),
        });
        const data = await res.json();

        if (data.success) {
            closeModal();
            // Show success banner
            const banner = document.createElement('div');
            banner.className = 'fixed top-4 right-4 z-[9999] px-5 py-3 rounded-xl shadow-lg text-white font-semibold text-sm';
            banner.style.background = '#15803d';
            banner.textContent = '✓ Proposal submitted successfully!';
            document.body.appendChild(banner);
            setTimeout(() => banner.remove(), 4000);
            // Optionally reload to refresh "Proposal Submitted" state
            setTimeout(() => location.reload(), 1200);
        } else {
            errDiv.textContent = '⚠ ' + (data.message || 'Failed to submit proposal.');
            errDiv.classList.remove('hidden');
            btn.disabled = false;
            btn.textContent = 'Submit Proposal';
        }
    } catch (err) {
        errDiv.textContent = '⚠ Network error. Please try again.';
        errDiv.classList.remove('hidden');
        btn.disabled = false;
        btn.textContent = 'Submit Proposal';
    }
});

async function aiGenerateCoverLetter() {
    const btn    = document.getElementById('aiGenerateBtn');
    const icon   = document.getElementById('aiGenerateIcon');
    const label  = document.getElementById('aiGenerateBtnText');
    const area   = document.getElementById('coverLetterArea');
    const errEl  = document.getElementById('aiGenerateError');

    btn.disabled = true;
    icon.textContent = '⏳';
    label.textContent = 'Generating...';
    errEl.classList.add('hidden');

    try {
        const res = await fetch('{{ route("marketplace.ai.cover-letter") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                project_title: {!! json_encode($project->title) !!},
                project_description: {!! json_encode(Str::limit($project->description ?? '', 600)) !!},
                skills_required: {!! json_encode($project->skills_required ?? []) !!},
                budget: {{ $project->budget_min ?? 0 }}
            })
        });
        const data = await res.json();
        if (data.cover_letter) {
            area.value = data.cover_letter;
            area.style.borderColor = '#1A73E8';
            setTimeout(() => area.style.borderColor = '', 1500);
        } else {
            throw new Error(data.error || 'Failed to generate');
        }
    } catch (e) {
        errEl.textContent = '⚠ ' + e.message;
        errEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        icon.textContent = '✨';
        label.textContent = 'AI Generate';
    }
}
</script>
@endsection
