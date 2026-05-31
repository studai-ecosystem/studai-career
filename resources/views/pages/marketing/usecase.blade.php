@extends('layouts.site')

@php
    $base = rtrim(config('site.url'), '/');
    $products = config('products');
@endphp

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $base],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Use cases', 'item' => route('use-cases')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $usecase['name'], 'item' => route('use-case', $slug)],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('cta_title', 'Your next role is closer than you think')
@section('cta_sub', 'Start free and let your agent do the applying.')

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="crumb rv"><a href="{{ route('use-cases') }}">Use cases</a> / <span>{{ $usecase['name'] }}</span></span>
            <span class="eyebrow rv" style="margin-top:18px">{{ $usecase['eyebrow'] }}</span>
            <h1 class="rv" data-d="1">{{ $usecase['title'] }}</h1>
            <p class="lede rv" data-d="2">{{ $usecase['lede'] }}</p>
            <div class="phero__cta rv" data-d="3">
                <a href="{{ route('register') }}" class="btn btn-primary">Get started free @include('partials.icon', ['name' => 'arrow'])</a>
            </div>
        </div>
    </section>

    <section class="sec sec--mist">
        <div class="wrap">
            <div class="head">
                <span class="eyebrow rv">Sound familiar?</span>
                <h2 class="rv" data-d="1">The challenges we take off your plate</h2>
            </div>
            <div class="grid3">
                @foreach($usecase['pains'] as $i => $pain)
                    <div class="card rv" data-d="{{ $i + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => 'target'])</div>
                        <p style="margin-top:0;font-size:17px;color:var(--ink);font-weight:600">{{ $pain }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sec">
        <div class="wrap">
            <div class="head">
                <span class="eyebrow rv">How StudAI Hire helps</span>
                <h2 class="rv" data-d="1">A path built around you</h2>
            </div>
            <div class="grid3">
                @foreach($usecase['gains'] as $i => $g)
                    <div class="card rv" data-d="{{ $i + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => 'check'])</div>
                        <h3>{{ $g['title'] }}</h3>
                        <p>{{ $g['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if(!empty($usecase['products']))
    <section class="sec sec--mist">
        <div class="wrap">
            <div class="head">
                <span class="eyebrow rv">Your toolkit</span>
                <h2 class="rv" data-d="1">The modules you’ll lean on</h2>
            </div>
            <div class="grid3">
                @foreach($usecase['products'] as $i => $pkey)
                    @php $p = $products[$pkey] ?? null; @endphp
                    @if($p)
                        <a href="{{ route('product', $pkey) }}" class="card rv" data-d="{{ $i + 1 }}">
                            <div class="card__ic">@include('partials.icon', ['name' => $p['icon']])</div>
                            <h3>{{ $p['name'] }}</h3>
                            <p>{{ $p['lede'] }}</p>
                            <span class="card__link">Learn more @include('partials.icon', ['name' => 'arrow'])</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
