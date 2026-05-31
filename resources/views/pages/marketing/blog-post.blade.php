@extends('layouts.site')

@php
    $base = rtrim(config('site.url'), '/');
    $site = config('site');
    $dateIso = \Illuminate\Support\Carbon::parse($post['date'])->toIso8601String();
    $dateHuman = \Illuminate\Support\Carbon::parse($post['date'])->format('F j, Y');
    $related = collect(config('blog.posts'))->except($slug)->sortByDesc('date')->take(3);
@endphp

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post['title'],
    'description' => $post['excerpt'],
    'image' => $base . $site['seo']['og_image'],
    'datePublished' => $dateIso,
    'dateModified' => $dateIso,
    'author' => ['@type' => 'Organization', 'name' => $post['author']],
    'publisher' => ['@type' => 'Organization', 'name' => $site['brand']['name'], 'logo' => ['@type' => 'ImageObject', 'url' => $base . $site['brand']['logo']]],
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('blog.show', $slug)],
    'articleSection' => $post['category'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@push('styles')
<style>
    .article{max-width:760px;margin:0 auto;padding:0 28px;}
    .article__h{text-align:center;padding:80px 0 40px;}
    .article__h h1{font-size:clamp(34px,5.2vw,58px);letter-spacing:-.045em;line-height:1.04;max-width:20ch;margin:20px auto 0;}
    .article__meta{display:flex;gap:10px;align-items:center;justify-content:center;margin-top:24px;font-size:14px;color:var(--ink-3);font-weight:500;}
    .article__meta b{color:var(--ink-2);font-weight:600;}
    .prose h2{font-size:28px;letter-spacing:-.03em;margin:48px 0 4px;}
    .prose p{color:var(--ink-2);font-size:18.5px;line-height:1.75;margin-top:18px;font-weight:450;}
    .prose .lead{font-size:21px;color:var(--ink);font-weight:500;margin-bottom:8px;}
</style>
@endpush

@section('cta_title', 'Put these ideas to work')
@section('cta_sub', 'Let your AI agent run the search while you focus on what matters.')

@section('content')
    <article>
        <div class="article">
            <div class="article__h">
                <span class="crumb rv"><a href="{{ route('blog') }}">Blog</a> / <span>{{ $post['category'] }}</span></span>
                <h1 class="rv" data-d="1">{{ $post['title'] }}</h1>
                <div class="article__meta rv" data-d="2">
                    <b>{{ $post['author'] }}</b> · <span>{{ $dateHuman }}</span> · <span>{{ $post['read'] }}</span>
                </div>
            </div>
            <div class="prose rv" data-d="2">
                <p class="lead">{{ $post['excerpt'] }}</p>
                @foreach($post['body'] as $block)
                    <h2>{{ $block['h'] }}</h2>
                    @foreach($block['p'] as $para)
                        <p>{{ $para }}</p>
                    @endforeach
                @endforeach
            </div>
        </div>

        <section class="sec sec--mist" style="margin-top:88px">
            <div class="wrap">
                <div class="head">
                    <span class="eyebrow rv">Keep reading</span>
                    <h2 class="rv" data-d="1">More from the blog</h2>
                </div>
                <div class="grid3">
                    @foreach($related as $rslug => $rp)
                        <a href="{{ route('blog.show', $rslug) }}" class="card rv" data-d="{{ ($loop->index % 3) + 1 }}">
                            <span class="eyebrow" style="margin-bottom:14px">{{ $rp['category'] }}</span>
                            <h3>{{ $rp['title'] }}</h3>
                            <p>{{ $rp['excerpt'] }}</p>
                            <span class="card__link">Read more @include('partials.icon', ['name' => 'arrow'])</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    </article>
@endsection
