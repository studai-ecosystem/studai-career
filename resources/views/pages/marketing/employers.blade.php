@extends('layouts.site')

@php $scout = config('products.scout'); @endphp

@section('cta_title', 'Hire the best people, on autopilot')
@section('cta_sub', 'See how S.C.O.U.T. screens, ranks and shortlists candidates for your team.')

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">For employers</span>
            <h1 class="rv" data-d="1">Recruiting, <span class="grad-text">on autopilot</span></h1>
            <p class="lede rv" data-d="2">{{ $scout['lede'] }}</p>
            <div class="phero__cta rv" data-d="3">
                <a href="{{ route('contact') }}" class="btn btn-primary">Talk to us @include('partials.icon', ['name' => 'arrow'])</a>
                <a href="{{ route('product', 'scout') }}" class="btn btn-glass">Explore S.C.O.U.T.</a>
            </div>
        </div>
    </section>

    <section class="sec sec--mist">
        <div class="wrap">
            <div class="head">
                <span class="eyebrow rv">Why S.C.O.U.T.</span>
                <h2 class="rv" data-d="1">Spend time with the right people</h2>
            </div>
            <div class="grid3">
                @foreach($scout['highlights'] as $i => $h)
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
                <span class="eyebrow rv">Capabilities</span>
                <h2 class="rv" data-d="1">An autonomous applicant tracking system</h2>
            </div>
            <div class="grid2">
                @foreach($scout['features'] as $i => $f)
                    <div class="card rv" data-d="{{ ($i % 2) + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => $f['icon']])</div>
                        <h3>{{ $f['title'] }}</h3>
                        <p>{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
