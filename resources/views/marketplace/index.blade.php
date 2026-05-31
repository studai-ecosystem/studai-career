@extends('layouts.dashboard')

@section('title', 'Talent Marketplace - StudAI Hire')

@push('styles')
<style>
    .fiverr-hero { background: #EBF2FF; }
    .category-pill { transition: all .2s; }
    .category-pill:hover { background: #2D6CDF; color: #fff; transform: translateY(-1px); box-shadow: none; }
    .gig-card { transition: box-shadow .2s, transform .2s; }
    .gig-card:hover { box-shadow: none; transform: translateY(-3px); }
    .freelancer-card:hover { box-shadow: none; }
    .star-filled { color: #E37400; }
    .badge-pro { background: #3D3D3D; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 3px; }
    .badge-level { background: #1E8E3E; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 3px; }
    .search-bar input:focus { outline: none; }
    .tag-pill { border: 1px solid #EBF2FF; color: #737373; font-size: 13px; border-radius: 20px; padding: 5px 14px; transition: all .15s; cursor: pointer; }
    .tag-pill:hover { border-color: #2D6CDF; color: #2D6CDF; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white overflow-x-hidden">

    {{-- ═══════════════════════════════════════════════════════
         DUAL-SITE TOP NAV BAR
    ═══════════════════════════════════════════════════════ --}}
    <div class="border-b border-gray-200 bg-white sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-0 overflow-x-auto scrollbar-hide">
                <a href="{{ route('marketplace.index') }}"
                   class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-semibold border-b-2 border-blue-600 text-blue-700 whitespace-nowrap">
                    🏪 Marketplace
                </a>
                <a href="{{ route('marketplace.gigs') }}"
                   class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-300 transition whitespace-nowrap">
                    🛒 Buy Services
                </a>
                <a href="{{ route('marketplace.projects') }}"
                   class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-300 transition whitespace-nowrap">
                    📋 Browse Projects
                </a>
                @if(!auth()->check() || auth()->user()->isEmployer())
                <a href="{{ route('marketplace.freelancers') }}"
                   class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-300 transition whitespace-nowrap">
                    👤 Find Talent
                </a>
                @endif
                <div class="h-6 w-px bg-gray-200 mx-2 shrink-0"></div>
                @auth
                    @if(auth()->user()->isEmployer())
                    <a href="{{ route('marketplace.employer.dashboard') }}"
                       class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-semibold border-b-2 border-transparent text-blue-700 hover:border-blue-400 transition whitespace-nowrap">
                        🏢 Company Portal
                    </a>
                    @else
                    <a href="{{ route('marketplace.freelancer.dashboard') }}"
                       class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-semibold border-b-2 border-transparent text-green-700 hover:border-green-400 transition whitespace-nowrap">
                        🎓 Student Portal
                    </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="shrink-0 ml-auto px-4 py-2 my-2 rounded-xl text-sm font-bold text-white whitespace-nowrap" style="background:#2D6CDF;">
                        Sign In to Post / Sell
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         HERO — Search-first, Fiverr-style
    ═══════════════════════════════════════════════════════ --}}
    <div class="fiverr-hero border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-4">
                    Find the perfect <span class="text-blue-600">freelance service</span><br class="hidden md:block"> for your business
                </h1>
                <p class="text-gray-500 text-lg mb-8">Hire skilled professionals — from AI developers to designers, writers to analysts. Fast. Secure. Guaranteed.</p>

                {{-- Search Bar --}}
                <form action="{{ route('marketplace.projects') }}" method="GET" class="flex search-bar bg-white border-2 border-gray-200 rounded-xl shadow-lg overflow-hidden hover:border-blue-400 focus-within:border-blue-500 transition mb-6">
                    <div class="flex items-center pl-4 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" placeholder='Try "Laravel developer", "UI designer", "content writer"...'
                           value="{{ request('search') }}"
                           class="flex-1 px-4 py-4 text-gray-800 bg-transparent border-0 text-base placeholder-gray-400 min-w-0">
                    <button type="submit" class="px-6 py-4 bg-blue-600 text-white font-semibold hover:bg-blue-700 transition whitespace-nowrap">
                        Search
                    </button>
                </form>

                {{-- Popular searches --}}
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="text-gray-500 text-sm self-center">Popular:</span>
                    @foreach(['Laravel Developer', 'UI/UX Design', 'Data Analyst', 'React Developer', 'AI Integration', 'Content Writing'] as $tag)
                        <a href="{{ route('marketplace.projects', ['search' => $tag]) }}" class="tag-pill bg-white hover:bg-blue-50">{{ $tag }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Stats ribbon --}}
        <div class="border-t border-gray-100 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap justify-center divide-x divide-gray-200">
                    <div class="flex items-center gap-2 px-6 py-3">
                        <span class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_projects'] ?? 0) }}+</span>
                        <span class="text-gray-500 text-sm">Open Projects</span>
                    </div>
                    <div class="flex items-center gap-2 px-6 py-3">
                        <span class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_freelancers'] ?? 0) }}+</span>
                        <span class="text-gray-500 text-sm">Verified Freelancers</span>
                    </div>
                    <div class="flex items-center gap-2 px-6 py-3">
                        <span class="text-2xl font-bold text-blue-600">{{ number_format($stats['completed_contracts'] ?? 0) }}+</span>
                        <span class="text-gray-500 text-sm">Projects Completed</span>
                    </div>
                    <div class="flex items-center gap-2 px-6 py-3">
                        <span class="text-2xl font-bold text-blue-600">4.9 &#9733;</span>
                        <span class="text-gray-500 text-sm">Avg. Rating</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         CATEGORY PILLS — Wrap row
    ═══════════════════════════════════════════════════════ --}}
    <div class="border-b border-gray-100 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-2 py-4">
                @php
                $cats = [
                    ['slug' => 'web_development',   'label' => 'Programming & Tech',    'icon' => '💻'],
                    ['slug' => 'design',             'label' => 'Graphics & Design',     'icon' => '🎨'],
                    ['slug' => 'writing',            'label' => 'Writing & Translation', 'icon' => '✍️'],
                    ['slug' => 'ai_ml',              'label' => 'AI & Machine Learning', 'icon' => '🤖'],
                    ['slug' => 'marketing',          'label' => 'Digital Marketing',     'icon' => '📣'],
                    ['slug' => 'data_science',       'label' => 'Data Science',          'icon' => '📊'],
                    ['slug' => 'mobile_development', 'label' => 'Mobile Apps',           'icon' => '📱'],
                    ['slug' => 'devops',             'label' => 'DevOps & Cloud',        'icon' => '⚙️'],
                    ['slug' => 'video_production',   'label' => 'Video & Animation',     'icon' => '🎬'],
                    ['slug' => 'consulting',         'label' => 'Business Consulting',   'icon' => '💼'],
                    ['slug' => 'finance',            'label' => 'Finance & Accounting',  'icon' => '💰'],
                    ['slug' => 'admin_support',      'label' => 'Admin & Support',       'icon' => '🗂️'],
                ];
                $activeCategory = request('category');
                @endphp
                @foreach($cats as $cat)
                    <a href="{{ route('marketplace.projects', ['category' => $cat['slug']]) }}"
                       class="category-pill flex items-center gap-1.5 whitespace-nowrap px-4 py-2 rounded-full text-sm font-medium
                              {{ $activeCategory === $cat['slug'] ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-blue-600 hover:text-white' }}">
                        <span>{{ $cat['icon'] }}</span>
                        <span>{{ $cat['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         POPULAR SERVICES (Gig Cards — Fiverr style)
    ═══════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Popular Services</h2>
                <p class="text-gray-500 text-sm mt-1">Handpicked top-rated projects from our community</p>
            </div>
            <a href="{{ route('marketplace.projects') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center gap-1">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @php
        $catStyles = [
            'web_development'    => ['bg' => '#2D6CDF', 'icon' => '💻'],
            'mobile_development' => ['bg' => '#2D6CDF', 'icon' => '📱'],
            'design'             => ['bg' => '#2D6CDF', 'icon' => '🎨'],
            'writing'            => ['bg' => '#E37400', 'icon' => '✍️'],
            'marketing'          => ['bg' => '#1E8E3E', 'icon' => '📣'],
            'data_science'       => ['bg' => '#2D6CDF', 'icon' => '📊'],
            'ai_ml'              => ['bg' => '#2D6CDF', 'icon' => '🤖'],
            'devops'             => ['bg' => '#737373', 'icon' => '⚙️'],
            'video_production'   => ['bg' => '#2D6CDF', 'icon' => '🎬'],
            'consulting'         => ['bg' => '#2D6CDF', 'icon' => '💼'],
            'finance'            => ['bg' => '#1E8E3E', 'icon' => '💰'],
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($featuredProjects ?? [] as $project)
                @php
                    $style = $catStyles[$project->category] ?? ['bg' => '#2D6CDF', 'icon' => '💼'];
                @endphp
                <a href="{{ route('marketplace.project.show', $project) }}"
                   class="gig-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group">
                    {{-- Banner with inline gradient --}}
                    <div class="h-36 flex items-center justify-center relative overflow-hidden"
                         style="background: {{ $style['bg'] }};">
                        <span class="text-5xl drop-shadow-lg">{{ $style['icon'] }}</span>
                        @if($project->is_urgent)
                            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">🔥 URGENT</span>
                        @endif
                        @if($project->is_featured)
                            <span class="absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-full" style="background:#FFF8EC;color:#E37400;">⭐ TOP</span>
                        @endif
                    </div>

                    {{-- Card body --}}
                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($project->employer?->name ?? 'Client') }}&size=28&background=1A73E8&color=fff&rounded=true"
                                 class="w-7 h-7 rounded-full shrink-0" alt="">
                            <span class="text-xs text-gray-600 truncate font-medium">{{ $project->employer?->name ?? 'Client' }}</span>
                            <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded" style="background:#1E8E3E;color:#fff;">PRO</span>
                        </div>

                        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition mb-2 flex-1 leading-snug">
                            {{ $project->title }}
                        </h3>

                        <div class="flex flex-wrap gap-1 mb-2">
                            @foreach(array_slice($project->skills_required, 0, 2) as $skill)
                                <span class="px-2 py-0.5 text-xs rounded-full" style="background:#EBF2FF;color:#1B57C4;">{{ $skill }}</span>
                            @endforeach
                        </div>

                        <div class="flex items-center gap-1 mb-3">
                            <span style="color:#E37400;" class="text-sm">★★★★★</span>
                            <span class="text-xs font-bold text-gray-800">5.0</span>
                            <span class="text-xs text-gray-400">({{ $project->proposals_count ?? 0 }})</span>
                        </div>

                        <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                            <span class="text-xs text-gray-400 uppercase tracking-wide">Budget from</span>
                            <span class="text-base font-bold text-gray-900">₹{{ number_format($project->budget_min ?? 5000) }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-4 text-center py-16">
                    <div class="text-6xl mb-4">🚀</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Be the first to post a project!</h3>
                    <p class="text-gray-500 mb-5">Connect with top freelancers and get your work done.</p>
                    @auth
                        <a href="{{ route('marketplace.employer.create-project') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 text-white font-bold rounded-xl transition"
                           style="background:#2D6CDF;">
                            + Post a Project
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 text-white font-bold rounded-xl transition"
                           style="background:#2D6CDF;">
                            Get Started Free
                        </a>
                    @endauth
                </div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         DUAL-SIDE SPLIT BANNER
    ═══════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @auth
        @if(auth()->user()->isEmployer())
        {{-- Employer sees only Company card, full-width --}}
        <div class="rounded-2xl p-8 flex flex-col md:flex-row md:items-center gap-6" style="background:#2D6CDF;color:#fff;">
            <div class="text-5xl">🏢</div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-1">Hire Top Student Talent</h3>
                <p class="text-blue-100 text-sm">Post a project and receive proposals from verified student developers, designers, and data scientists — or browse ready-made services and hire directly.</p>
            </div>
            <div class="flex gap-3 flex-wrap flex-shrink-0">
                <a href="{{ route('marketplace.employer.create-project') }}"
                   class="px-6 py-3 rounded-xl font-bold text-sm bg-white hover:bg-blue-50 transition" style="color:#2D6CDF;">
                    + Post a Project
                </a>
                <a href="{{ route('marketplace.gigs') }}"
                   class="px-6 py-3 rounded-xl font-bold text-sm border-2 border-white text-white hover:bg-white hover:text-blue-700 transition">
                    Browse Services
                </a>
                <a href="{{ route('marketplace.freelancers') }}"
                   class="px-6 py-3 rounded-xl font-bold text-sm border-2 border-white text-white hover:bg-white hover:text-blue-700 transition">
                    Find Talent
                </a>
            </div>
        </div>
        @else
        {{-- Job seeker / student sees only their card, full-width --}}
        <div class="rounded-2xl p-8 flex flex-col md:flex-row md:items-center gap-6" style="background:#1E8E3E;color:#fff;">
            <div class="text-5xl">🎓</div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-1">Your Student Marketplace</h3>
                <p class="text-green-100 text-sm">Create gigs, bid on projects, track contracts, and earn — all in one place. Start by listing your first service or finding an open project.</p>
            </div>
            <div class="flex gap-3 flex-wrap flex-shrink-0">
                <a href="{{ route('marketplace.freelancer.create-gig') }}"
                   class="px-6 py-3 rounded-xl font-bold text-sm bg-white hover:bg-green-50 transition" style="color:#1E8E3E;">
                    + Create a Service
                </a>
                <a href="{{ route('marketplace.projects') }}"
                   class="px-6 py-3 rounded-xl font-bold text-sm border-2 border-white text-white hover:bg-white hover:text-green-700 transition">
                    Browse Projects
                </a>
                <a href="{{ route('marketplace.freelancer.dashboard') }}"
                   class="px-6 py-3 rounded-xl font-bold text-sm border-2 border-white text-white hover:bg-white hover:text-green-700 transition">
                    My Dashboard
                </a>
            </div>
        </div>
        @endif
        @else
        {{-- Guests see both cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="rounded-2xl p-7 flex flex-col" style="background:#2D6CDF;color:#fff;">
                <div class="text-3xl mb-3">🏢</div>
                <h3 class="text-xl font-bold mb-1">For Companies</h3>
                <p class="text-blue-100 text-sm mb-4 flex-1">Post a project with your requirements and get proposals from verified student developers, designers, and data scientists.</p>
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-white hover:bg-blue-50 transition" style="color:#2D6CDF;">Post a Project</a>
                </div>
            </div>
            <div class="rounded-2xl p-7 flex flex-col" style="background:#1E8E3E;color:#fff;">
                <div class="text-3xl mb-3">🎓</div>
                <h3 class="text-xl font-bold mb-1">For Students</h3>
                <p class="text-green-100 text-sm mb-4 flex-1">Offer your skills as a service — companies browse your gig listing and order directly. No bidding, no waiting.</p>
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-white hover:bg-green-50 transition" style="color:#1E8E3E;">Get Started Free</a>
                </div>
            </div>
        </div>
        @endauth
    </div>

    {{-- ═══════════════════════════════════════════════════════
         STUDENT SERVICES (gigs companies can buy)
         Shown to: guests and students only (employers see Top Rated Talent instead)
    ═══════════════════════════════════════════════════════ --}}
    @if(!auth()->check() || !auth()->user()->isEmployer())
    @php
    $featuredGigs = \App\Models\FreelancerGig::active()
        ->where('is_featured', true)
        ->with('freelancerProfile.user')
        ->orderByDesc('orders_count')
        ->limit(4)
        ->get();
    if ($featuredGigs->isEmpty()) {
        $featuredGigs = \App\Models\FreelancerGig::active()
            ->with('freelancerProfile.user')
            ->orderByDesc('average_rating')
            ->limit(4)
            ->get();
    }
    $gigCatStyles = [
        'web_development'    => ['bg'=>'#2D6CDF','icon'=>'💻'],
        'mobile_development' => ['bg'=>'#2D6CDF','icon'=>'📱'],
        'design'             => ['bg'=>'#2D6CDF','icon'=>'🎨'],
        'writing'            => ['bg'=>'#E37400','icon'=>'✍️'],
        'marketing'          => ['bg'=>'#1E8E3E','icon'=>'📣'],
        'data_science'       => ['bg'=>'#2D6CDF','icon'=>'📊'],
        'ai_ml'              => ['bg'=>'#2D6CDF','icon'=>'🤖'],
        'devops'             => ['bg'=>'#737373','icon'=>'⚙️'],
    ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Student Services</h2>
                <p class="text-gray-500 text-sm mt-1">Order directly from top-rated student experts — no bidding required</p>
            </div>
            <a href="{{ route('marketplace.gigs') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center gap-1">
                Browse all services
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($featuredGigs as $gig)
                @php $gcs = $gigCatStyles[$gig->category] ?? ['bg'=>'#2D6CDF','icon'=>'💼']; @endphp
                <a href="{{ route('marketplace.gig.show', $gig) }}"
                   class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col group" style="transition:all .2s;">
                    <div class="h-36 flex items-center justify-center relative" style="background:{{ $gcs['bg'] }};">
                        <span class="text-5xl drop-shadow-md">{{ $gcs['icon'] }}</span>
                        @if($gig->is_featured)
                            <span class="absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-full" style="background:#FFF8EC;color:#E37400;">⭐ TOP</span>
                        @endif
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($gig->freelancerProfile?->user?->name ?? 'S') }}&size=26&background=1dbf73&color=fff&rounded=true"
                                 class="rounded-full shrink-0" style="width:26px;height:26px;" alt="">
                            <span class="text-xs text-gray-600 truncate font-medium">{{ $gig->freelancerProfile?->user?->name }}</span>
                            <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded" style="background:#1E8E3E;color:#fff;">PRO</span>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition flex-1 leading-snug mb-2">
                            {{ $gig->title }}
                        </h3>
                        <div class="flex items-center gap-1 mb-3">
                            <span style="color:#E37400;">★</span>
                            <span class="text-xs font-bold text-gray-800">{{ number_format($gig->average_rating,1) }}</span>
                            <span class="text-xs text-gray-400">({{ $gig->total_reviews }})</span>
                            <span class="text-xs text-gray-400 ml-auto">{{ $gig->orders_count }} sold</span>
                        </div>
                        <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                            <span class="text-xs text-gray-400">Starting at</span>
                            <span class="text-base font-extrabold text-gray-900">₹{{ number_format($gig->starting_price) }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-4 text-center py-10">
                    <p class="text-gray-400 text-sm">No student services yet.</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif {{-- end student/guest only --}}

    {{-- ═══════════════════════════════════════════════════════
         TOP FREELANCERS — only shown to employers & guests
    ═══════════════════════════════════════════════════════ --}}
    @if(!auth()->check() || auth()->user()->isEmployer())
    <div class="py-12" style="background:#F0F0EE;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Top Rated Talent</h2>
                    <p class="text-gray-500 text-sm mt-1">Work with the best — verified, reviewed, and ready</p>
                </div>
                <a href="{{ route('marketplace.freelancers') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center gap-1">
                    Browse all
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($topFreelancers ?? [] as $profile)
                    <a href="{{ route('marketplace.freelancer.show', $profile) }}"
                       class="freelancer-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col items-center text-center transition group">
                        <div class="relative mb-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($profile->user?->name ?? 'F') }}&size=80&background=1A73E8&color=fff&rounded=true"
                                 class="rounded-full border-4 border-white shadow-md object-cover" style="width:72px;height:72px;"
                                 alt="{{ $profile->user?->name }}">
                            @if($profile->is_verified)
                                <div class="absolute -bottom-1 -right-1 rounded-full p-1 border-2 border-white" style="background:#2D6CDF;">
                                    <svg class="w-3 h-3" fill="white" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <p class="font-semibold text-gray-900 group-hover:text-blue-600 transition text-sm">{{ $profile->user?->name }}</p>
                        <p class="text-xs mb-2" style="color:#2D6CDF;">{{ Str::limit($profile->professional_title, 35) }}</p>

                        <div class="flex items-center justify-center gap-1 mb-2">
                            <span style="color:#E37400;">★</span>
                            <span class="text-xs font-bold text-gray-800">{{ number_format($profile->average_rating ?? 4.9, 1) }}</span>
                            <span class="text-xs text-gray-400">({{ $profile->total_reviews ?? 0 }})</span>
                        </div>

                        <div class="flex flex-wrap justify-center gap-1 mb-3">
                            @foreach(array_slice($profile->skills, 0, 2) as $skill)
                                <span class="px-2 py-0.5 text-xs rounded-full" style="background:#EBF2FF;color:#1B57C4;">{{ $skill }}</span>
                            @endforeach
                        </div>

                        <div class="w-full border-t border-gray-100 pt-3 flex items-center justify-between">
                            <span class="text-xs text-gray-400">From</span>
                            <span class="text-sm font-bold text-gray-900">₹{{ number_format($profile->hourly_rate ?? 500) }}/hr</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-4 text-center py-10">
                        <p class="text-gray-500 text-sm mb-3">No freelancers have set up profiles yet.</p>
                        @auth
                            <a href="{{ route('marketplace.freelancer.profile') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold rounded-xl transition text-sm"
                               style="background:#2D6CDF;">
                                Create Your Profile
                            </a>
                        @endauth
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif {{-- end employer/guest only --}}

    {{-- ═══════════════════════════════════════════════════════
         HOW IT WORKS — 3-step (Fiverr style)
    ═══════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-12">How StudAI Marketplace Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 relative">
            {{-- Connector lines --}}
            <div class="hidden md:block absolute top-8 left-1/3 right-1/3 h-0.5 bg-blue-100 -translate-y-1/2"></div>

            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-4 shadow-lg">1</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Post a Project</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Describe what you need — budget, timeline, skills. It's free and takes 2 minutes.</p>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-4 shadow-lg">2</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Review Proposals</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Receive proposals from verified professionals. Compare, chat, and choose the best fit.</p>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-4 shadow-lg">3</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Pay Securely</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Funds held in escrow. Release payment only when the work is done to your satisfaction.</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         CATEGORY GRID — Icon cards
    ═══════════════════════════════════════════════════════ --}}
    <div class="bg-gray-50 border-t border-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Explore Categories</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach([
                    ['slug'=>'web_development',   'label'=>'Programming & Tech',    'icon'=>'💻', 'bg'=>'bg-blue-50',   'border'=>'border-blue-100',  'hover'=>'hover:bg-blue-100'],
                    ['slug'=>'design',             'label'=>'Graphics & Design',     'icon'=>'🎨', 'bg'=>'bg-pink-50',   'border'=>'border-pink-100',  'hover'=>'hover:bg-pink-100'],
                    ['slug'=>'writing',            'label'=>'Writing',               'icon'=>'✍️', 'bg'=>'bg-amber-50',  'border'=>'border-amber-100', 'hover'=>'hover:bg-amber-100'],
                    ['slug'=>'ai_ml',              'label'=>'AI & ML',               'icon'=>'🤖', 'bg'=>'bg-violet-50', 'border'=>'border-violet-100','hover'=>'hover:bg-violet-100'],
                    ['slug'=>'marketing',          'label'=>'Digital Marketing',     'icon'=>'📣', 'bg'=>'bg-green-50',  'border'=>'border-green-100', 'hover'=>'hover:bg-green-100'],
                    ['slug'=>'data_science',       'label'=>'Data Science',          'icon'=>'📊', 'bg'=>'bg-cyan-50',   'border'=>'border-cyan-100',  'hover'=>'hover:bg-cyan-100'],
                    ['slug'=>'mobile_development', 'label'=>'Mobile Apps',           'icon'=>'📱', 'bg'=>'bg-purple-50', 'border'=>'border-purple-100','hover'=>'hover:bg-purple-100'],
                    ['slug'=>'devops',             'label'=>'DevOps & Cloud',        'icon'=>'⚙️', 'bg'=>'bg-slate-50',  'border'=>'border-slate-100', 'hover'=>'hover:bg-slate-100'],
                    ['slug'=>'video_production',   'label'=>'Video & Animation',     'icon'=>'🎬', 'bg'=>'bg-red-50',    'border'=>'border-red-100',   'hover'=>'hover:bg-red-100'],
                    ['slug'=>'consulting',         'label'=>'Business',              'icon'=>'💼', 'bg'=>'bg-teal-50',   'border'=>'border-teal-100',  'hover'=>'hover:bg-teal-100'],
                    ['slug'=>'finance',            'label'=>'Finance',               'icon'=>'💰', 'bg'=>'bg-emerald-50','border'=>'border-emerald-100','hover'=>'hover:bg-emerald-100'],
                    ['slug'=>'admin_support',      'label'=>'Admin Support',         'icon'=>'🗂️', 'bg'=>'bg-orange-50', 'border'=>'border-orange-100','hover'=>'hover:bg-orange-100'],
                ] as $cat)
                    <a href="{{ route('marketplace.projects', ['category' => $cat['slug']]) }}"
                       class="flex flex-col items-center justify-center p-5 rounded-2xl border {{ $cat['bg'] }} {{ $cat['border'] }} {{ $cat['hover'] }} transition text-center group">
                        <div class="text-3xl mb-2">{{ $cat['icon'] }}</div>
                        <div class="text-xs font-semibold text-gray-700 group-hover:text-blue-700 leading-tight">{{ $cat['label'] }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         TRUST SIGNALS — Why us
    ═══════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="flex gap-4 items-start">
                <div class="shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Secure Escrow Payments</h3>
                    <p class="text-gray-500 text-sm">Funds are protected in escrow and released only when you approve the work — zero risk.</p>
                </div>
            </div>
            <div class="flex gap-4 items-start">
                <div class="shrink-0 w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Verified Skill Badges</h3>
                    <p class="text-gray-500 text-sm">Every freelancer is reviewed and skill-tested. Badge levels show real expertise, not just claims.</p>
                </div>
            </div>
            <div class="flex gap-4 items-start">
                <div class="shrink-0 w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Built-in Collaboration</h3>
                    <p class="text-gray-500 text-sm">Milestone tracking, real-time messaging, file sharing, and dispute resolution — all in one place.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         CTA BANNER
    ═══════════════════════════════════════════════════════ --}}
    <div class="bg-blue-600 py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h2 class="text-3xl font-extrabold mb-3">Ready to get things done?</h2>
            <p class="text-blue-100 text-lg mb-8">Join thousands of businesses and freelancers building great things together.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('marketplace.employer.create-project') }}"
                       class="px-8 py-3.5 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg">
                        Post a Project Free
                    </a>
                    <a href="{{ route('marketplace.freelancer.dashboard') }}"
                       class="px-8 py-3.5 border-2 border-white text-white font-bold rounded-xl hover:bg-blue-500 transition">
                        Start Freelancing
                    </a>
                @else
                    <a href="{{ route('register') }}"
                       class="px-8 py-3.5 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg">
                        Join for Free
                    </a>
                    <a href="{{ route('login') }}"
                       class="px-8 py-3.5 border-2 border-white text-white font-bold rounded-xl hover:bg-blue-500 transition">
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </div>

</div>
@endsection
