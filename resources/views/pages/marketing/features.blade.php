@extends('layouts.site')

@php
    $products = config('products');
    $seo = $seo ?? [];
@endphp

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">The platform</span>
            <h1 class="rv" data-d="1">One OS for your <span class="grad-text">entire career</span></h1>
            <p class="lede rv" data-d="2">Six AI modules that work as one — searching, applying, preparing and negotiating, so you can stop applying and start arriving.</p>
            <div class="phero__cta rv" data-d="3">
                <a href="{{ route('register') }}" class="btn btn-primary">Get started free @include('partials.icon', ['name' => 'arrow'])</a>
                <a href="{{ route('pricing') }}" class="btn btn-glass">See pricing</a>
            </div>
        </div>
    </section>

    <section class="sec">
        <div class="wrap">
            <div class="grid3">
                @foreach($products as $slug => $p)
                    <a href="{{ route('product', $slug) }}" class="card rv" data-d="{{ ($loop->index % 3) + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => $p['icon']])</div>
                        <h3>{{ $p['name'] }}</h3>
                        <p>{{ $p['lede'] }}</p>
                        <span class="card__link">Explore @include('partials.icon', ['name' => 'arrow'])</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
