@extends('layouts.site')

@php $usecases = config('usecases'); @endphp

@section('cta_title', 'Whatever your stage, your agent is ready')
@section('cta_sub', 'Start free and let StudAI Hire run the job search for you.')

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">Use cases</span>
            <h1 class="rv" data-d="1">Built for <span class="grad-text">every career moment</span></h1>
            <p class="lede rv" data-d="2">Wherever you are — first job, big switch, or hiring a team — StudAI Hire meets you there and does the heavy lifting.</p>
        </div>
    </section>

    <section class="sec">
        <div class="wrap">
            <div class="grid3">
                @foreach($usecases as $slug => $uc)
                    <a href="{{ route('use-case', $slug) }}" class="card rv" data-d="{{ ($loop->index % 3) + 1 }}">
                        <div class="card__ic">@include('partials.icon', ['name' => 'users'])</div>
                        <h3>{{ $uc['name'] }}</h3>
                        <p>{{ $uc['lede'] }}</p>
                        <span class="card__link">See how @include('partials.icon', ['name' => 'arrow'])</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
