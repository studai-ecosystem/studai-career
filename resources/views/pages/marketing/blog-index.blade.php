@extends('layouts.site')

@php
    $blog = config('blog');
    $posts = collect($blog['posts'])->sortByDesc('date');
    $featured = $posts->first();
    $featuredSlug = $posts->keys()->first();
    $rest = $posts->slice(1);
@endphp

@section('cta_title', 'Stop reading about it. Start living it.')
@section('cta_sub', 'Let your AI agent run the job search while you focus on what matters.')

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">The blog</span>
            <h1 class="rv" data-d="1">Careers, <span class="grad-text">on autopilot</span></h1>
            <p class="lede rv" data-d="2">{{ $blog['meta']['lede'] }}</p>
        </div>
    </section>

    <section class="sec">
        <div class="wrap">
            @if($featured)
                <a href="{{ route('blog.show', $featuredSlug) }}" class="card rv" style="display:grid;grid-template-columns:1fr;gap:8px;padding:44px 40px">
                    <span class="eyebrow" style="margin-bottom:6px">{{ $featured['category'] }} · Featured</span>
                    <h2 style="font-size:clamp(28px,4vw,44px);letter-spacing:-.04em;line-height:1.06">{{ $featured['title'] }}</h2>
                    <p style="font-size:18px;margin-top:14px;max-width:70ch">{{ $featured['excerpt'] }}</p>
                    <span class="card__link" style="margin-top:20px">Read the article @include('partials.icon', ['name' => 'arrow'])</span>
                </a>
            @endif

            <div class="grid3">
                @foreach($rest as $slug => $post)
                    <a href="{{ route('blog.show', $slug) }}" class="card rv" data-d="{{ ($loop->index % 3) + 1 }}">
                        <span class="eyebrow" style="margin-bottom:14px">{{ $post['category'] }}</span>
                        <h3>{{ $post['title'] }}</h3>
                        <p>{{ $post['excerpt'] }}</p>
                        <span class="card__link">Read more @include('partials.icon', ['name' => 'arrow'])</span>
                        <p style="margin-top:18px;font-size:13px;color:var(--ink-3)">{{ \Illuminate\Support\Carbon::parse($post['date'])->format('M j, Y') }} · {{ $post['read'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
