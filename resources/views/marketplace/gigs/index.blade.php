@extends('layouts.dashboard')
@section('title', 'Browse Student Services - StudAI Hire')

@push('styles')
<style>
.gig-card:hover { transform: translateY(-2px); box-shadow: none; }
.gig-card { transition: all .2s; }
.cat-pill.active { background:#2D6CDF; color:#fff; }
.cat-pill { background:#F0F0EE; color:#444; cursor:pointer; transition:all .15s; }
</style>
@endpush

@section('content')
{{-- Dual-site sub-nav --}}
<div class="border-b border-gray-200 bg-white sticky top-0 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-0 overflow-x-auto">
            <a href="{{ route('marketplace.index') }}" class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-blue-600 whitespace-nowrap">🏪 Marketplace</a>
            <a href="{{ route('marketplace.gigs') }}" class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-semibold border-b-2 border-blue-600 text-blue-700 whitespace-nowrap">🛒 Buy Services</a>
            <a href="{{ route('marketplace.projects') }}" class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-blue-600 whitespace-nowrap">📋 Browse Projects</a>
            @if(!auth()->check() || auth()->user()->isEmployer())
            <a href="{{ route('marketplace.freelancers') }}" class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-blue-600 whitespace-nowrap">👤 Find Talent</a>
            @endif
            <div class="h-6 w-px bg-gray-200 mx-2 shrink-0"></div>
            @auth
                @if(auth()->user()->isEmployer())
                <a href="{{ route('marketplace.employer.dashboard') }}" class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-semibold border-b-2 border-transparent text-blue-700 hover:border-blue-400 whitespace-nowrap">🏢 Company Portal</a>
                @else
                <a href="{{ route('marketplace.freelancer.dashboard') }}" class="shrink-0 flex items-center gap-2 px-5 py-3.5 text-sm font-semibold border-b-2 border-transparent text-green-700 hover:border-green-400 whitespace-nowrap">🎓 Student Portal</a>
                @endif
            @endauth
        </div>
    </div>
</div>
@php
$catStyles = [
    'web_development'    => ['label'=>'Web Development',   'icon'=>'💻','bg'=>'#2D6CDF'],
    'mobile_development' => ['label'=>'Mobile Apps',       'icon'=>'📱','bg'=>'#2D6CDF'],
    'design'             => ['label'=>'Design & Creative', 'icon'=>'🎨','bg'=>'#2D6CDF'],
    'writing'            => ['label'=>'Writing & Content', 'icon'=>'✍️','bg'=>'#E37400'],
    'marketing'          => ['label'=>'Digital Marketing', 'icon'=>'📣','bg'=>'#1E8E3E'],
    'data_science'       => ['label'=>'Data & Analytics',  'icon'=>'📊','bg'=>'#2D6CDF'],
    'ai_ml'              => ['label'=>'AI & ML',           'icon'=>'🤖','bg'=>'#2D6CDF'],
    'devops'             => ['label'=>'DevOps & Cloud',    'icon'=>'⚙️','bg'=>'#737373'],
    'video_production'   => ['label'=>'Video & Animation', 'icon'=>'🎬','bg'=>'#2D6CDF'],
    'consulting'         => ['label'=>'Consulting',        'icon'=>'💼','bg'=>'#2D6CDF'],
];
$activeCategory = request('category','');
@endphp

<div class="min-h-screen" style="background:#F0F0EE;">

    {{-- Hero banner --}}
    <div class="text-white py-12 px-4" style="background:#2D6CDF;">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-3xl md:text-4xl font-extrabold mb-3">Browse Student Services</h1>
            <p class="text-blue-100 text-lg mb-6">Hire verified student talent — expert skills, startup-friendly pricing.</p>
            <form method="GET" action="{{ route('marketplace.gigs') }}" class="max-w-2xl mx-auto flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search: 'React app', 'Logo design', 'Python ML'..."
                       class="flex-1 px-5 py-3 rounded-xl text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                @if($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                <button type="submit" class="px-6 py-3 rounded-xl font-bold text-sm" style="background:#1E8E3E;color:#fff;">
                    Search
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Sidebar filters --}}
            <aside class="lg:w-60 shrink-0">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
                    <h3 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wide">Category</h3>
                    <div class="flex flex-col gap-1.5">
                        <a href="{{ route('marketplace.gigs') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium {{ !$activeCategory ? 'text-white' : 'text-gray-700 hover:bg-gray-50' }} transition"
                           style="{{ !$activeCategory ? 'background:#2D6CDF;' : '' }}">
                            All Services
                        </a>
                        @foreach($categories as $key => $cat)
                            <a href="{{ route('marketplace.gigs', ['category'=>$key] + request()->except('category','page')) }}"
                               class="px-3 py-2 rounded-lg text-sm font-medium {{ $activeCategory===$key ? 'text-white' : 'text-gray-700 hover:bg-gray-50' }} transition flex items-center gap-2"
                               style="{{ $activeCategory===$key ? 'background:#2D6CDF;' : '' }}">
                                <span>{{ $cat['icon'] }}</span>
                                {{ $cat['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
                    <h3 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wide">Sort By</h3>
                    @foreach(['featured'=>'Best Match','rating'=>'Top Rated','orders'=>'Best Seller','newest'=>'Newest'] as $val=>$label)
                        <a href="{{ route('marketplace.gigs', ['sort'=>$val] + request()->except('sort','page')) }}"
                           class="block px-3 py-2 rounded-lg text-sm {{ request('sort')===$val ? 'font-bold text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }} transition">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- Post your project CTA --}}
                <div class="rounded-2xl p-5 text-white text-center" style="background:#1E8E3E;">
                    <div class="text-3xl mb-2">📋</div>
                    <p class="font-bold text-sm mb-1">Need a custom project?</p>
                    <p class="text-green-100 text-xs mb-3">Post a project and get proposals from students.</p>
                    <a href="{{ route('marketplace.employer.create-project') }}"
                       class="block py-2 rounded-xl text-xs font-bold bg-white" style="color:#1E8E3E;">
                        Post a Project
                    </a>
                </div>
            </aside>

            {{-- Gig grid --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-5">
                    <p class="text-gray-600 text-sm">
                        <span class="font-bold text-gray-900">{{ $gigs->total() }}</span> services available
                        @if($activeCategory && isset($categories[$activeCategory]))
                            in <span class="font-semibold text-blue-600">{{ $categories[$activeCategory]['label'] }}</span>
                        @endif
                    </p>
                    <div class="flex items-center gap-2 text-sm">
                        @auth
                            <a href="{{ route('marketplace.freelancer.gigs') }}"
                               class="px-4 py-2 rounded-xl font-semibold text-white text-xs" style="background:#2D6CDF;">
                                + Offer a Service
                            </a>
                        @endauth
                    </div>
                </div>

                @if($gigs->isEmpty())
                    <div class="text-center py-20">
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No services found</h3>
                        <p class="text-gray-500 mb-5">Try a different category or search term.</p>
                        <a href="{{ route('marketplace.gigs') }}" class="px-5 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:#2D6CDF;">
                            Clear Filters
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
                        @foreach($gigs as $gig)
                            @php
                                $cs = $catStyles[$gig->category] ?? ['bg'=>'#2D6CDF','icon'=>'💼','label'=>'Service'];
                                $startPrice = $gig->starting_price;
                            @endphp
                            <a href="{{ route('marketplace.gig.show', $gig) }}"
                               class="gig-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col group">

                                {{-- Banner --}}
                                <div class="h-36 flex items-center justify-center relative" style="background:{{ $cs['bg'] }};">
                                    <span class="text-5xl drop-shadow-md">{{ $cs['icon'] }}</span>
                                    @if($gig->is_featured)
                                        <span class="absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-full" style="background:#FFF8EC;color:#E37400;">⭐ TOP RATED</span>
                                    @endif
                                    <span class="absolute bottom-2 left-2 text-xs font-semibold px-2 py-0.5 rounded-full" style="background:rgba(0,0,0,.35);color:#fff;">
                                        {{ $cs['label'] }}
                                    </span>
                                </div>

                                {{-- Body --}}
                                <div class="p-4 flex flex-col flex-1">
                                    {{-- Seller --}}
                                    <div class="flex items-center gap-2 mb-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($gig->freelancerProfile?->user?->name ?? 'S') }}&size=28&background=1A73E8&color=fff&rounded=true"
                                             class="rounded-full" style="width:26px;height:26px;" alt="">
                                        <span class="text-xs font-medium text-gray-700 truncate">{{ $gig->freelancerProfile?->user?->name }}</span>
                                        @if($gig->freelancerProfile?->is_verified)
                                            <svg class="w-3.5 h-3.5 ml-auto shrink-0" fill="#2D6CDF" viewBox="0 0 20 20">
                                                <path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                            </svg>
                                        @endif
                                    </div>

                                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2 flex-1 mb-2 leading-snug">
                                        {{ $gig->title }}
                                    </h3>

                                    {{-- Tags --}}
                                    @if(count($gig->tags))
                                        <div class="flex flex-wrap gap-1 mb-2">
                                            @foreach(array_slice($gig->tags, 0, 2) as $tag)
                                                <span class="px-2 py-0.5 text-xs rounded-full" style="background:#EBF2FF;color:#1B57C4;">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Rating + orders --}}
                                    <div class="flex items-center gap-2 mb-3">
                                        <span style="color:#E37400;">★</span>
                                        <span class="text-xs font-bold text-gray-800">{{ number_format($gig->average_rating, 1) }}</span>
                                        <span class="text-xs text-gray-400">({{ $gig->total_reviews }})</span>
                                        @if($gig->orders_count > 0)
                                            <span class="text-xs text-gray-400 ml-auto">{{ $gig->orders_count }} orders</span>
                                        @endif
                                    </div>

                                    {{-- Price --}}
                                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                                        <span class="text-xs text-gray-400">Starting at</span>
                                        <span class="text-base font-extrabold text-gray-900">
                                            ₹{{ number_format($startPrice) }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    {{ $gigs->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
