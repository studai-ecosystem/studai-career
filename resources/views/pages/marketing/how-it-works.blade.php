@extends('layouts.site')

@php
    $steps = [
        ['t' => 'Tell it what you want', 'd' => 'Set your target roles, locations, pay expectations and the kind of company you want to join. Once.'],
        ['t' => 'Your agent goes to work', 'd' => 'It scans the market continuously, matches roles to your profile and tailors a unique application for each one.'],
        ['t' => 'Prepare while it applies', 'd' => 'Practice with Interview AI and sharpen your resume in Studio while applications go out in the background.'],
        ['t' => 'Land interviews', 'd' => 'Interviews arrive in your inbox. You decide which to take — the agent keeps everything organised.'],
        ['t' => 'Negotiate and win', 'd' => 'When the offer comes, Negotiation Coach hands you a data-backed script to close on your terms.'],
    ];
@endphp

@section('cta_title', 'See it work for you')
@section('cta_sub', 'Set your goals once and let StudAI Hire run your career on autopilot.')

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">How it works</span>
            <h1 class="rv" data-d="1">Set it once. <span class="grad-text">Then arrive.</span></h1>
            <p class="lede rv" data-d="2">StudAI Hire turns the exhausting job hunt into a goal you set and an agent that delivers.</p>
        </div>
    </section>

    <section class="sec">
        <div class="wrap">
            <div class="grid3">
                @foreach($steps as $i => $s)
                    <div class="card rv" data-d="{{ ($i % 3) + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => 'spark'])</div>
                        <h3>{{ $s['t'] }}</h3>
                        <p>{{ $s['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
