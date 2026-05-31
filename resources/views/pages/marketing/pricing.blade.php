@extends('layouts.site')

@php $pricing = config('pricing'); @endphp

@push('styles')
<style>
    .plans{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:60px;align-items:start;}
    .plan{position:relative;background:#fff;border:1px solid var(--line);border-radius:var(--r-lg);padding:34px 30px;transition:.34s cubic-bezier(.16,1,.3,1);}
    .plan:hover{transform:translateY(-6px);box-shadow:var(--shadow);}
    .plan--hot{border:0;color:#fff;background:var(--grad);box-shadow:var(--shadow-xl);}
    .plan__badge{position:absolute;top:-13px;left:50%;transform:translateX(-50%);background:#0B0B14;color:#fff;font-size:12px;font-weight:700;padding:6px 16px;border-radius:var(--pill);letter-spacing:.03em;}
    .plan h3{font-size:21px;}
    .plan--hot h3{color:#fff;}
    .plan__price{display:flex;align-items:baseline;gap:8px;margin:18px 0 4px;}
    .plan__price b{font-size:46px;font-weight:800;letter-spacing:-.04em;}
    .plan__price span{font-size:15px;color:var(--ink-3);font-weight:500;}
    .plan--hot .plan__price span{color:rgba(255,255,255,.8);}
    .plan__tag{color:var(--ink-2);font-size:15px;font-weight:450;}
    .plan--hot .plan__tag{color:rgba(255,255,255,.9);}
    .plan__list{list-style:none;padding:0;margin:24px 0 28px;display:grid;gap:12px;}
    .plan__list li{display:flex;gap:11px;align-items:flex-start;font-size:15px;color:var(--ink-2);font-weight:450;line-height:1.5;}
    .plan--hot .plan__list li{color:#fff;}
    .plan__list svg{width:18px;height:18px;flex:0 0 18px;margin-top:2px;color:var(--green);}
    .plan--hot .plan__list svg{color:#fff;}
    .plan .btn{width:100%;justify-content:center;}
    .plan--hot .btn-primary{background:#fff;color:var(--violet);}
    @media(max-width:980px){.plans{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">Pricing</span>
            <h1 class="rv" data-d="1">Simple plans, <span class="grad-text">serious results</span></h1>
            <p class="lede rv" data-d="2">{{ $pricing['meta']['lede'] }}</p>
        </div>
    </section>

    <section class="sec" style="padding-top:24px">
        <div class="wrap">
            <div class="plans">
                @foreach($pricing['plans'] as $i => $plan)
                    <div class="plan rv {{ $plan['accent'] ? 'plan--hot' : '' }}" data-d="{{ $i + 1 }}">
                        @if($plan['accent'])<span class="plan__badge">Most popular</span>@endif
                        <h3>{{ $plan['name'] }}</h3>
                        <div class="plan__price"><b>{{ $plan['price'] }}</b><span>{{ $plan['period'] }}</span></div>
                        <p class="plan__tag">{{ $plan['tagline'] }}</p>
                        <ul class="plan__list">
                            @foreach($plan['features'] as $f)
                                <li>@include('partials.icon', ['name' => 'check']) {{ $f }}</li>
                            @endforeach
                        </ul>
                        @if($plan['name'] === 'Teams')
                            <a href="{{ route('contact') }}" class="btn btn-glass">{{ $plan['cta'] }}</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary">{{ $plan['cta'] }}</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
