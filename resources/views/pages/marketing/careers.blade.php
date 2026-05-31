@extends('layouts.site')

@php
    $perks = [
        ['i' => 'rocket', 't' => 'Mission that matters', 'd' => 'Build the platform that puts millions of careers on autopilot.'],
        ['i' => 'brain',  't' => 'Frontier AI work', 'd' => 'Ship autonomous agents that act in the real world, for real people.'],
        ['i' => 'users',  't' => 'Small team, big ownership', 'd' => 'Your work is visible, your impact is direct, your voice counts.'],
        ['i' => 'spark',  't' => 'Grow fast', 'd' => 'Learn relentlessly alongside people who care about craft.'],
    ];
@endphp

@section('cta_title', 'Don’t see your role? Reach out anyway.')
@section('cta_sub', 'We’re always keen to meet exceptional people building the future of work.')

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">Careers</span>
            <h1 class="rv" data-d="1">Build the future of <span class="grad-text">hiring</span></h1>
            <p class="lede rv" data-d="2">We’re a small, ambitious team building India’s first complete autonomous AI hiring platform. If that excites you, let’s talk.</p>
            <div class="phero__cta rv" data-d="3">
                <a href="mailto:{{ config('site.brand.email') }}" class="btn btn-primary">Get in touch @include('partials.icon', ['name' => 'arrow'])</a>
            </div>
        </div>
    </section>

    <section class="sec">
        <div class="wrap">
            <div class="head">
                <span class="eyebrow rv">Why join</span>
                <h2 class="rv" data-d="1">Work that moves people forward</h2>
            </div>
            <div class="grid2">
                @foreach($perks as $i => $p)
                    <div class="card rv" data-d="{{ ($i % 2) + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => $p['i']])</div>
                        <h3>{{ $p['t'] }}</h3>
                        <p>{{ $p['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
