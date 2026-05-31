@extends('layouts.dashboard')
@section('title', 'My Services - StudAI Hire')

@section('content')
<div class="min-h-screen" style="background:#F0F0EE;">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Services</h1>
            <p class="text-gray-500 text-sm mt-1">Manage your service listings — companies browse and order directly.</p>
        </div>
        @if($profile)
            <a href="{{ route('marketplace.freelancer.create-gig') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-bold rounded-xl hover:opacity-90 transition text-sm"
               style="background:#2D6CDF;">
                + Create New Service
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-5 px-5 py-3 rounded-xl text-sm font-medium" style="background:#EDFAF2;color:#1E8E3E;border:1px solid #A3D9B4;">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if(!$profile)
        {{-- No profile yet --}}
        <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center">
            <div class="text-6xl mb-4">🧑‍💼</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Set Up Your Freelancer Profile First</h2>
            <p class="text-gray-500 text-sm mb-6">You need a freelancer profile before you can offer services to companies.</p>
            <a href="{{ route('marketplace.freelancer.profile') }}"
               class="inline-flex items-center gap-2 px-6 py-3 text-white font-bold rounded-xl transition hover:opacity-90"
               style="background:#2D6CDF;">
                Create Profile
            </a>
        </div>
    @elseif($gigs->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center">
            <div class="text-6xl mb-4">🚀</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">You haven't listed any services yet</h2>
            <p class="text-gray-500 text-sm mb-6">Create a service listing and let companies find and hire you directly — no bidding required!</p>
            <a href="{{ route('marketplace.freelancer.create-gig') }}"
               class="inline-flex items-center gap-2 px-6 py-3 text-white font-bold rounded-xl transition hover:opacity-90"
               style="background:#2D6CDF;">
                + Create Your First Service
            </a>
        </div>
    @else
        {{-- Stats bar --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @php
                $totalOrders = $gigs->sum('orders_count');
                $activeGigs  = $gigs->where('status','active')->count();
                $avgRating   = $gigs->where('total_reviews','>',0)->avg('average_rating');
                $totalViews  = $gigs->sum('views_count');
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-2xl font-extrabold" style="color:#2D6CDF;">{{ $activeGigs }}</div>
                <div class="text-xs text-gray-500 mt-1">Active Services</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-2xl font-extrabold text-gray-900">{{ $totalOrders }}</div>
                <div class="text-xs text-gray-500 mt-1">Total Orders</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-2xl font-extrabold" style="color:#E37400;">{{ $avgRating ? number_format($avgRating,1) : '—' }}</div>
                <div class="text-xs text-gray-500 mt-1">Avg Rating</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-2xl font-extrabold text-gray-900">{{ number_format($totalViews) }}</div>
                <div class="text-xs text-gray-500 mt-1">Total Views</div>
            </div>
        </div>

        {{-- Gigs list --}}
        <div class="space-y-4">
            @foreach($gigs as $gig)
                @php
                    $catStyles = [
                        'web_development'    => ['bg'=>'#2D6CDF','icon'=>'💻'],
                        'mobile_development' => ['bg'=>'#2D6CDF','icon'=>'📱'],
                        'design'             => ['bg'=>'#2D6CDF','icon'=>'🎨'],
                        'writing'            => ['bg'=>'#E37400','icon'=>'✍️'],
                        'marketing'          => ['bg'=>'#1E8E3E','icon'=>'📣'],
                        'data_science'       => ['bg'=>'#2D6CDF','icon'=>'📊'],
                        'ai_ml'              => ['bg'=>'#2D6CDF','icon'=>'🤖'],
                        'devops'             => ['bg'=>'#737373','icon'=>'⚙️'],
                    ];
                    $cs = $catStyles[$gig->category] ?? ['bg'=>'#2D6CDF','icon'=>'💼'];
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-row">
                    {{-- Icon column --}}
                    <div class="w-20 flex-shrink-0 flex items-center justify-center text-3xl"
                         style="background:{{ $cs['bg'] }}; min-height:100px;">
                        {{ $cs['icon'] }}
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 p-4 flex flex-col sm:flex-row items-start sm:items-center gap-3 min-w-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                      style="background:{{ $gig->status==='active'?'#EDFAF2':'#F0F0EE' }};color:{{ $gig->status==='active'?'#1E8E3E':'#3D3D3D' }};">
                                    {{ $gig->status === 'active' ? '● Active' : ($gig->status === 'paused' ? '⏸ Paused' : '📝 Draft') }}
                                </span>
                                @if($gig->is_featured)
                                    <span class="text-xs px-2 py-0.5 rounded-full" style="background:#FFF8EC;color:#E37400;">⭐ Featured</span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-gray-900 text-sm leading-snug" style="white-space:normal;">{{ $gig->title }}</h3>
                            <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-500 flex-wrap">
                                <span class="font-medium text-green-700">₹{{ number_format($gig->starting_price) }}</span>
                                <span>•</span>
                                <span style="color:#E37400;">★ {{ number_format($gig->average_rating,1) }}</span>
                                <span>({{ $gig->total_reviews }})</span>
                                <span>•</span>
                                <span>{{ $gig->orders_count }} orders</span>
                                <span>•</span>
                                <span>{{ number_format($gig->views_count) }} views</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 flex-wrap flex-shrink-0">
                            <a href="{{ route('marketplace.gig.show', $gig) }}" target="_blank"
                               class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                                Preview
                            </a>
                            <a href="{{ route('marketplace.freelancer.edit-gig', $gig) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-medium text-white transition hover:opacity-90"
                               style="background:#2D6CDF;">
                                Edit
                            </a>
                            <form action="{{ route('marketplace.freelancer.toggle-gig', $gig) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium border transition"
                                        style="{{ $gig->status==='active' ? 'border-color:#E37400;color:#E37400;background:#FFF8EC;' : 'border-color:#1E8E3E;color:#1E8E3E;background:#EDFAF2;' }}">
                                    {{ $gig->status === 'active' ? '⏸ Pause' : '▶ Activate' }}
                                </button>
                            </form>
                            <form action="{{ route('marketplace.freelancer.delete-gig', $gig) }}" method="POST"
                                  onsubmit="return confirm('Delete this service? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium border border-red-200 text-red-600 hover:bg-red-50 transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
</div>
@endsection
