@extends('layouts.site')

@php
    $faq = config('faq');
    $base = rtrim(config('site.url'), '/');
@endphp

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faq['items'])->map(fn ($i) => [
        '@type' => 'Question',
        'name' => $i['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $i['a']],
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@push('styles')
<style>
    .faq{max-width:820px;margin:0 auto;padding:0 28px;}
    .faq__item{border-bottom:1px solid var(--line);}
    .faq__q{width:100%;text-align:left;background:none;border:0;cursor:pointer;font-family:var(--font);
        display:flex;align-items:center;justify-content:space-between;gap:20px;padding:26px 0;
        font-size:19px;font-weight:700;letter-spacing:-.02em;color:var(--ink);}
    .faq__q svg{width:22px;height:22px;flex:0 0 22px;color:var(--violet);transition:transform .3s;}
    .faq__item.open .faq__q svg{transform:rotate(45deg);}
    .faq__a{max-height:0;overflow:hidden;transition:max-height .35s ease;}
    .faq__a p{color:var(--ink-2);font-size:17px;line-height:1.7;padding:0 0 26px;font-weight:450;max-width:68ch;}
</style>
@endpush

@section('content')
    <section class="phero">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">FAQ</span>
            <h1 class="rv" data-d="1">Questions? <span class="grad-text">Answered.</span></h1>
            <p class="lede rv" data-d="2">{{ $faq['meta']['lede'] }}</p>
        </div>
    </section>

    <section class="sec" style="padding-top:24px">
        <div class="faq rv">
            @foreach($faq['items'] as $item)
                <div class="faq__item">
                    <button type="button" class="faq__q" onclick="this.parentElement.classList.toggle('open');const a=this.nextElementSibling;a.style.maxHeight=a.style.maxHeight?'':a.scrollHeight+'px';">
                        {{ $item['q'] }}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    </button>
                    <div class="faq__a"><p>{{ $item['a'] }}</p></div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
