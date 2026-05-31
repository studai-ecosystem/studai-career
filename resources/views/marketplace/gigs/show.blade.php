@extends('layouts.dashboard')
@section('title', $gig->title . ' - StudAI Hire')

@push('styles')
<style>
.pkg-tab { padding:12px 20px; font-weight:600; font-size:.875rem; cursor:pointer; border-bottom:3px solid transparent; transition:all .15s; }
.pkg-tab.active { border-color:#2D6CDF; color:#2D6CDF; }
.pkg-tab:not(.active) { color:#737373; }
.check-row { display:flex; align-items:center; gap:8px; padding:8px 0; border-bottom:1px solid #F0F0EE; font-size:.875rem; color:#3D3D3D; }
.check-row:last-child { border:none; }
.check-icon { color:#1E8E3E; font-weight:bold; flex-shrink:0; }
.gig-tab { border-bottom:2px solid transparent; padding-bottom:12px; color:#737373; font-weight:500; transition:all .15s; }
.gig-tab.active { border-color:#2D6CDF; color:#2D6CDF; }
</style>
@endpush

@section('content')
@php
$catStyles = [
    'web_development'    => ['bg'=>'#2D6CDF','icon'=>'💻','label'=>'Web Development'],
    'mobile_development' => ['bg'=>'#2D6CDF','icon'=>'📱','label'=>'Mobile Apps'],
    'design'             => ['bg'=>'#2D6CDF','icon'=>'🎨','label'=>'Design & Creative'],
    'writing'            => ['bg'=>'#E37400','icon'=>'✍️','label'=>'Writing & Content'],
    'marketing'          => ['bg'=>'#1E8E3E','icon'=>'📣','label'=>'Digital Marketing'],
    'data_science'       => ['bg'=>'#2D6CDF','icon'=>'📊','label'=>'Data & Analytics'],
    'ai_ml'              => ['bg'=>'#2D6CDF','icon'=>'🤖','label'=>'AI & ML'],
    'devops'             => ['bg'=>'#737373','icon'=>'⚙️','label'=>'DevOps & Cloud'],
    'video_production'   => ['bg'=>'#2D6CDF','icon'=>'🎬','label'=>'Video & Animation'],
    'consulting'         => ['bg'=>'#2D6CDF','icon'=>'💼','label'=>'Consulting'],
];
$cs       = $catStyles[$gig->category] ?? ['bg'=>'#2D6CDF','icon'=>'💼','label'=>'Service'];
$profile  = $gig->freelancerProfile;
$packages = $gig->packages;
@endphp

<div class="min-h-screen" style="background:#F0F0EE;">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-5 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('marketplace.index') }}" class="hover:text-blue-600">Marketplace</a>
        <span>›</span>
        <a href="{{ route('marketplace.gigs') }}" class="hover:text-blue-600">Services</a>
        <span>›</span>
        <a href="{{ route('marketplace.gigs', ['category'=>$gig->category]) }}" class="hover:text-blue-600">{{ $cs['label'] }}</a>
        <span>›</span>
        <span class="text-gray-700">{{ Str::limit($gig->title, 45) }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8 items-start">

        {{-- LEFT: Gig details --}}
        <div class="flex-1 min-w-0">

            {{-- Title --}}
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full" style="background:#EBF2FF;color:#1B57C4;">{{ $cs['label'] }}</span>
                @if($gig->is_featured)
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full" style="background:#FFF8EC;color:#E37400;">⭐ Top Rated</span>
                @endif
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight mb-5">{{ $gig->title }}</h1>

            {{-- Seller mini-bar --}}
            <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-200 flex-wrap">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($profile?->user?->name ?? 'Student') }}&size=48&background=1A73E8&color=fff&rounded=true"
                         class="rounded-full" style="width:48px;height:48px;" alt="">
                    <span class="absolute bottom-0 right-0" style="width:11px;height:11px;background:#1E8E3E;border-radius:50%;border:2px solid #fff;display:block;"></span>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $profile?->user?->name }}</p>
                    <p class="text-xs" style="color:#2D6CDF;">{{ Str::limit($profile?->professional_title ?? '', 40) }}</p>
                </div>
                <div class="flex items-center gap-1 ml-2">
                    <span style="color:#E37400;">★</span>
                    <span class="text-xs font-bold text-gray-800">{{ number_format($gig->average_rating, 1) }}</span>
                    <span class="text-xs text-gray-400">({{ $gig->total_reviews }} reviews)</span>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-bold rounded" style="background:#1E8E3E;color:#fff;">VERIFIED</span>
                    <span class="text-xs text-gray-400">{{ $gig->orders_count }} orders completed</span>
                </div>
            </div>

            {{-- Gig banner --}}
            <div class="rounded-2xl overflow-hidden mb-6 shadow-sm flex items-center justify-center relative"
                 style="height:280px;background:{{ $cs['bg'] }};">
                <span style="font-size:80px;filter:drop-shadow(0 8px 24px rgba(0,0,0,.2));">{{ $cs['icon'] }}</span>
                <div style="position:absolute;inset:0;background:rgba(0,0,0,.4);"></div>
                <div style="position:absolute;bottom:20px;left:24px;right:24px;" class="flex flex-wrap gap-2">
                    @foreach($gig->tags as $tag)
                        <span style="background:rgba(255,255,255,.25);color:#fff;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600;">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex gap-6 border-b border-gray-200 mb-6 overflow-x-auto">
                <button onclick="switchTab('overview')" class="gig-tab active whitespace-nowrap" id="tab-overview">Overview</button>
                <button onclick="switchTab('description')" class="gig-tab whitespace-nowrap" id="tab-description">About This Service</button>
                <button onclick="switchTab('seller')" class="gig-tab whitespace-nowrap" id="tab-seller">About the Seller</button>
                <button onclick="switchTab('reviews')" class="gig-tab whitespace-nowrap" id="tab-reviews">
                    Reviews <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-2 py-0.5 ml-1">{{ $gig->total_reviews }}</span>
                </button>
            </div>

            {{-- Tab: Overview --}}
            <div id="panel-overview" class="tab-panel">
                {{-- Stats --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                    <h2 class="font-bold text-gray-900 text-lg mb-4">Service Highlights</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 rounded-xl" style="background:#EBF2FF;">
                            <div class="text-2xl mb-1">⚡</div>
                            <div class="font-bold text-gray-900 text-sm">{{ $packages[0]['delivery_days'] ?? '—' }} Days</div>
                            <div class="text-xs text-gray-500">Fastest Delivery</div>
                        </div>
                        <div class="text-center p-4 rounded-xl" style="background:#EDFAF2;">
                            <div class="text-2xl mb-1">✅</div>
                            <div class="font-bold text-gray-900 text-sm">{{ $gig->orders_count }}</div>
                            <div class="text-xs text-gray-500">Completed Orders</div>
                        </div>
                        <div class="text-center p-4 rounded-xl" style="background:#EBF2FF;">
                            <div class="text-2xl mb-1">⭐</div>
                            <div class="font-bold text-gray-900 text-sm">{{ number_format($gig->average_rating, 1) }}/5</div>
                            <div class="text-xs text-gray-500">Average Rating</div>
                        </div>
                        <div class="text-center p-4 rounded-xl" style="background:#FFF8EC;">
                            <div class="text-2xl mb-1">👁️</div>
                            <div class="font-bold text-gray-900 text-sm">{{ number_format($gig->views_count) }}</div>
                            <div class="text-xs text-gray-500">Profile Views</div>
                        </div>
                    </div>
                </div>

                {{-- Tags / Skills --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                    <h2 class="font-bold text-gray-900 text-lg mb-4">Tags</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($gig->tags as $tag)
                            <a href="{{ route('marketplace.gigs', ['search'=>$tag]) }}"
                               class="px-4 py-2 rounded-full text-sm font-medium border border-gray-200 hover:border-blue-400 hover:text-blue-600 text-gray-700 transition">
                                {{ $tag }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Requirements --}}
                @if($gig->requirements)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                        <h2 class="font-bold text-gray-900 text-lg mb-3">What I Need From You</h2>
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $gig->requirements }}</p>
                    </div>
                @endif

                {{-- Related gigs --}}
                @if($relatedGigs->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="font-bold text-gray-900 text-lg mb-4">Related Services</h2>
                        <div class="space-y-3">
                            @foreach($relatedGigs as $rg)
                                <a href="{{ route('marketplace.gig.show', $rg) }}"
                                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition group">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0"
                                         style="background:{{ ($catStyles[$rg->category]['bg'] ?? '#2D6CDF') }};">
                                        {{ $catStyles[$rg->category]['icon'] ?? '💼' }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 truncate">{{ $rg->title }}</p>
                                        <p class="text-xs text-gray-400">From ₹{{ number_format($rg->starting_price) }}</p>
                                    </div>
                                    <span style="color:#E37400;">★</span>
                                    <span class="text-xs font-bold text-gray-700">{{ number_format($rg->average_rating,1) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tab: Description --}}
            <div id="panel-description" class="tab-panel hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 text-lg mb-4">About This Service</h2>
                    <div class="text-gray-700 leading-relaxed text-sm whitespace-pre-line">{{ $gig->description }}</div>
                </div>
            </div>

            {{-- Tab: Seller --}}
            <div id="panel-seller" class="tab-panel hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4 mb-5">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($profile?->user?->name ?? 'S') }}&size=72&background=1A73E8&color=fff&rounded=true"
                             class="rounded-full" style="width:72px;height:72px;" alt="">
                        <div>
                            <h2 class="font-bold text-gray-900 text-lg">{{ $profile?->user?->name }}</h2>
                            <p class="text-sm" style="color:#2D6CDF;">{{ $profile?->professional_title }}</p>
                            <div class="flex items-center gap-1 mt-1">
                                <span style="color:#E37400;">★</span>
                                <span class="text-sm font-bold text-gray-800">{{ number_format($gig->average_rating,1) }}</span>
                                <span class="text-xs text-gray-400">({{ $gig->total_reviews }} reviews)</span>
                            </div>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('marketplace.message', $profile) }}"
                               class="px-4 py-2 rounded-xl border-2 text-sm font-semibold transition hover:bg-blue-50"
                               style="border-color:#2D6CDF;color:#2D6CDF;">
                                Contact Me
                            </a>
                        </div>
                    </div>
                    <dl class="grid grid-cols-2 gap-4 text-sm border-t border-gray-100 pt-5">
                        <div>
                            <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Hourly Rate</dt>
                            <dd class="font-semibold text-gray-900">₹{{ number_format($profile?->hourly_rate ?? 0) }}/hr</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Availability</dt>
                            <dd class="font-semibold text-gray-900">{{ ucfirst($profile?->availability ?? 'Available') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Experience</dt>
                            <dd class="font-semibold text-gray-900">{{ ucfirst($profile?->experience_level ?? 'Intermediate') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Orders Done</dt>
                            <dd class="font-semibold text-gray-900">{{ $gig->orders_count }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Tab: Reviews --}}
            <div id="panel-reviews" class="tab-panel hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-6 pb-6 border-b border-gray-100 mb-4">
                        <div class="text-center">
                            <div class="text-5xl font-extrabold text-gray-900">{{ number_format($gig->average_rating,1) }}</div>
                            <div class="flex justify-center gap-0.5 my-1">
                                @for($i=0;$i<5;$i++)<span style="color:#E37400;font-size:1.1rem;">★</span>@endfor
                            </div>
                            <div class="text-xs text-gray-500">{{ $gig->total_reviews }} reviews</div>
                        </div>
                        <div class="flex-1">
                            @foreach([5,4,3,2,1] as $star)
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs text-gray-500 w-4">{{ $star }}</span>
                                    <div class="flex-1 rounded-full" style="background:#F0F0EE;height:6px;">
                                        <div class="rounded-full" style="background:#E37400;height:6px;width:{{ $star===5?'88%':($star===4?'9%':'1%') }};"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-center text-gray-400 text-sm py-4">Detailed reviews are loaded once orders are completed.</p>
                </div>
            </div>

        </div>{{-- /left --}}

        {{-- RIGHT SIDEBAR: Packages + Order --}}
        <div class="w-full lg:w-80 xl:w-96 shrink-0 sticky top-4">

            {{-- Package tabs --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden mb-4">
                <div class="flex border-b border-gray-100">
                    @foreach($packages as $i => $pkg)
                        <button onclick="switchPkg({{ $i }})" id="pkg-tab-{{ $i }}"
                                class="pkg-tab flex-1 {{ $i===1 ? 'active' : '' }}">
                            {{ ucfirst($pkg['type'] ?? 'Package') }}
                        </button>
                    @endforeach
                </div>

                @foreach($packages as $i => $pkg)
                    <div id="pkg-panel-{{ $i }}" class="{{ $i!==1?'hidden':'' }} p-5">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="text-2xl font-extrabold text-gray-900">₹{{ number_format($pkg['price'] ?? 0) }}</div>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $pkg['title'] ?? ucfirst($pkg['type'] ?? '') }}</p>
                            </div>
                            <div class="text-right text-sm">
                                <div class="font-semibold text-gray-900">{{ $pkg['delivery_days'] ?? 7 }}-day delivery</div>
                                <p class="text-xs text-gray-400">
                                    {{ ($pkg['revisions'] ?? 2) == 99 ? 'Unlimited' : ($pkg['revisions'] ?? 2) }} revisions
                                </p>
                            </div>
                        </div>

                        <div class="space-y-0 mb-5">
                            @foreach($pkg['features'] ?? [] as $feature)
                                <div class="check-row">
                                    <span class="check-icon">✓</span>
                                    <span>{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>

                        @auth
                            @if(auth()->id() !== ($profile?->user_id ?? null))
                                <button onclick="openOrderModal('{{ $pkg['type'] ?? 'basic' }}', {{ $pkg['price'] ?? 0 }}, {{ $pkg['delivery_days'] ?? 7 }})"
                                        class="block w-full py-3 rounded-xl text-white font-bold text-base hover:opacity-90 transition mb-2"
                                        style="background:#1E8E3E;">
                                    Order Now — ₹{{ number_format($pkg['price'] ?? 0) }}
                                </button>
                                <a href="{{ route('marketplace.message', $profile) }}"
                                   class="block w-full text-center py-2.5 rounded-xl font-semibold text-sm border-2 hover:bg-blue-50 transition"
                                   style="border-color:#2D6CDF;color:#2D6CDF;">
                                    Contact Seller
                                </a>
                            @else
                                <a href="{{ route('marketplace.freelancer.edit-gig', $gig) }}"
                                   class="block w-full text-center py-3 rounded-xl text-white font-bold text-sm" style="background:#2D6CDF;">
                                    Edit My Gig
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="block w-full text-center py-3 rounded-xl text-white font-bold text-base hover:opacity-90 transition"
                               style="background:#1E8E3E;">
                                Login to Order
                            </a>
                        @endauth
                    </div>
                @endforeach
            </div>

            {{-- Seller card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-900 mb-4">About the Seller</h3>
                <div class="flex items-center gap-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($profile?->user?->name ?? 'S') }}&size=52&background=1A73E8&color=fff&rounded=true"
                         style="width:52px;height:52px;" class="rounded-full" alt="">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $profile?->user?->name }}</p>
                        <p class="text-xs flex items-center gap-1 mt-0.5" style="color:#2D6CDF;">
                            {{ Str::limit($profile?->professional_title ?? '', 30) }}
                        </p>
                    </div>
                </div>
                <dl class="space-y-2 text-sm border-t border-gray-100 pt-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Response Time</dt>
                        <dd class="font-medium text-gray-900">~1 hour</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Orders Done</dt>
                        <dd class="font-medium text-gray-900">{{ $gig->orders_count }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Member Since</dt>
                        <dd class="font-medium text-gray-900">{{ $profile?->created_at?->format('M Y') ?? 'Recently' }}</dd>
                    </div>
                </dl>
                <div class="mt-4 pt-3 border-t border-gray-100 text-center text-xs text-gray-400">
                    🔒 Secure payments via StudAI escrow
                </div>
            </div>

        </div>{{-- /sidebar --}}
    </div>
</div>
</div>

{{-- Order Modal --}}
<div id="orderModal" class="fixed inset-0 z-50 hidden" style="background:rgba(0,0,0,.5);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 text-lg">Place Your Order</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
            </div>
            <form action="{{ route('marketplace.gig.order', $gig) }}" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="package_type" id="orderPackageType" value="standard">
                <div class="rounded-xl p-4 border border-gray-200 flex items-center justify-between" style="background:#F0F0EE;">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm" id="orderPkgTitle">Standard Package</p>
                        <p class="text-xs text-gray-500 mt-0.5" id="orderDelivery">7-day delivery</p>
                    </div>
                    <div class="text-xl font-extrabold text-gray-900" id="orderPrice">₹0</div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Your Requirements *</label>
                    <textarea name="requirements" rows="5" required minlength="20"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                              placeholder="Describe what you need: project goals, reference links, brand colors, tech stack preferences, deadline, etc. The more detail, the better!"></textarea>
                </div>
                <div class="rounded-xl p-3 text-sm" style="background:#EDFAF2;color:#1E8E3E;">
                    🔒 Payment is held in escrow and released to the freelancer only when you approve the work.
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="submit" class="flex-1 py-3 text-white font-bold rounded-xl hover:opacity-90 transition" style="background:#1E8E3E;">
                        Confirm Order
                    </button>
                    <button type="button" onclick="closeOrderModal()" class="px-5 py-3 border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
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
    const count = {{ count($packages) }};
    for(let i=0;i<count;i++){
        document.getElementById('pkg-panel-'+i).classList.add('hidden');
        document.getElementById('pkg-tab-'+i).classList.remove('active');
    }
    document.getElementById('pkg-panel-'+idx).classList.remove('hidden');
    document.getElementById('pkg-tab-'+idx).classList.add('active');
}
const pkgData = @json($packages);
function openOrderModal(type, price, days) {
    document.getElementById('orderModal').classList.remove('hidden');
    document.getElementById('orderPackageType').value = type;
    document.getElementById('orderPrice').textContent = '₹' + price.toLocaleString('en-IN');
    document.getElementById('orderDelivery').textContent = days + '-day delivery';
    document.getElementById('orderPkgTitle').textContent = type.charAt(0).toUpperCase() + type.slice(1) + ' Package';
    document.body.style.overflow = 'hidden';
}
function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.getElementById('orderModal').addEventListener('click', e => {
    if(e.target === document.getElementById('orderModal')) closeOrderModal();
});
@if(session('success'))
    // Show success
    console.log('Order placed: {{ addslashes(session("success")) }}');
@endif
</script>
@endsection
