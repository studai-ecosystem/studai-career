@extends('layouts.site')

@php
    $values = [
        ['i' => 'rocket', 't' => 'Autonomy over busywork', 'd' => 'We believe your time is for living, not for copy-pasting applications. Software should do the work.'],
        ['i' => 'users',  't' => 'Built for India, for everyone', 'd' => 'From first-job students to seasoned professionals, we’re building access for the country’s ambition.'],
        ['i' => 'shield', 't' => 'Trust by design', 'd' => 'An agent that acts for you must earn your trust. Privacy, control and transparency come first.'],
        ['i' => 'spark',  't' => 'Relentlessly useful', 'd' => 'Every feature has to move you forward — toward an interview, an offer, a better career.'],
    ];
@endphp

@section('cta_title', 'Join the autonomous career movement')
@section('cta_sub', 'Be part of India’s first complete autonomous AI hiring platform.')

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">Our story</span>
            <h1 class="rv" data-d="1">Careers should run <span class="grad-text">on autopilot</span></h1>
            <p class="lede rv" data-d="2">We started StudAI Hire with a simple conviction: applying to jobs shouldn’t be a second full-time job. So we built India’s first complete autonomous AI hiring platform — an agent that works for you.</p>
        </div>
    </section>

    <section class="sec">
        <div class="wrap">
            <div class="head">
                <span class="eyebrow rv">What we believe</span>
                <h2 class="rv" data-d="1">The principles behind the platform</h2>
            </div>
            <div class="grid2">
                @foreach($values as $i => $v)
                    <div class="card rv" data-d="{{ ($i % 2) + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => $v['i']])</div>
                        <h3>{{ $v['t'] }}</h3>
                        <p>{{ $v['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
