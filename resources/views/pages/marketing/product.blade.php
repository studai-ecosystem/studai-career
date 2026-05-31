@extends('layouts.site')

@php
    $base = rtrim(config('site.url'), '/');
    $products = config('products');
@endphp

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product['name'] . ' — StudAI Hire',
    'description' => $product['meta_desc'],
    'brand' => ['@type' => 'Brand', 'name' => 'StudAI Hire'],
    'url' => route('product', $slug),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $base],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Product', 'item' => $base . '/product'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $product['name'], 'item' => route('product', $slug)],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('cta_title', 'Ready to put ' . $product['name'] . ' to work?')
@section('cta_sub', 'Start free and let StudAI Hire run your career on autopilot.')

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="crumb rv"><a href="{{ route('home') }}">Home</a> / <span>{{ $product['name'] }}</span></span>
            <span class="eyebrow rv" style="margin-top:18px">{{ $product['eyebrow'] }}</span>
            <h1 class="rv" data-d="1">{!! $product['title'] !!}</h1>
            <p class="lede rv" data-d="2">{{ $product['lede'] }}</p>
            <div class="phero__cta rv" data-d="3">
                <a href="{{ route('register') }}" class="btn btn-primary">Get started free
                    @include('partials.icon', ['name' => 'arrow'])
                </a>
                <a href="{{ route('how-it-works') }}" class="btn btn-glass">See how it works</a>
            </div>
        </div>
    </section>

    <section class="sec sec--mist">
        <div class="wrap">
            <div class="grid3">
                @foreach($product['highlights'] as $i => $h)
                    <div class="card rv" data-d="{{ $i + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => 'check'])</div>
                        <h3>{{ $h['title'] }}</h3>
                        <p>{{ $h['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sec">
        <div class="wrap">
            <div class="head">
                <span class="eyebrow rv">What it does</span>
                <h2 class="rv" data-d="1">Built to move your career forward</h2>
            </div>
            <div class="grid2">
                @foreach($product['features'] as $i => $f)
                    <div class="card rv" data-d="{{ ($i % 2) + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => $f['icon']])</div>
                        <h3>{{ $f['title'] }}</h3>
                        <p>{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sec sec--mist">
        <div class="wrap">
            <div class="head">
                <span class="eyebrow rv">How it works</span>
                <h2 class="rv" data-d="1">Three steps to autopilot</h2>
            </div>
            <div class="grid3">
                @foreach($product['steps'] as $i => $s)
                    <div class="card rv" data-d="{{ $i + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => 'spark'])</div>
                        <h3>{{ $s['title'] }}</h3>
                        <p>{{ $s['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if(!empty($product['related']))
    <section class="sec">
        <div class="wrap">
            <div class="head">
                <span class="eyebrow rv">Works better together</span>
                <h2 class="rv" data-d="1">Explore the rest of the OS</h2>
            </div>
            <div class="grid3">
                @foreach($product['related'] as $i => $rel)
                    @php $r = $products[$rel] ?? null; @endphp
                    @if($r)
                        <a href="{{ route('product', $rel) }}" class="card rv" data-d="{{ $i + 1 }}">
                            <div class="card__ic">@include('partials.icon', ['name' => $r['icon']])</div>
                            <h3>{{ $r['name'] }}</h3>
                            <p>{{ $r['lede'] }}</p>
                            <span class="card__link">Learn more @include('partials.icon', ['name' => 'arrow'])</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
