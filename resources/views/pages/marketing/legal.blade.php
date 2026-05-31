@extends('layouts.site')

@php
    $base = rtrim(config('site.url'), '/');
    $updated = \Illuminate\Support\Carbon::parse($doc['updated'])->format('F j, Y');
@endphp

@push('styles')
<style>
    .legal{max-width:820px;margin:0 auto;padding:0 28px;}
    .legal__intro{color:var(--ink-2);font-size:19px;line-height:1.7;margin:20px 0 0;font-weight:450;}
    .legal__sec{margin-top:48px;}
    .legal__sec h2{font-size:24px;letter-spacing:-.025em;}
    .legal__sec ul{margin:16px 0 0;padding:0;list-style:none;}
    .legal__sec li{position:relative;padding:9px 0 9px 30px;color:var(--ink-2);font-size:16.5px;line-height:1.65;border-bottom:1px solid var(--mist-2);font-weight:450;}
    .legal__sec li::before{content:"";position:absolute;left:4px;top:17px;width:8px;height:8px;border-radius:50%;background:var(--grad);}
    .legal__updated{display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--ink-3);background:var(--mist);padding:7px 14px;border-radius:var(--pill);margin-top:18px;}
</style>
@endpush

@section('no_cta')
@section('content')
    <section class="phero" style="padding-bottom:24px">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">Legal</span>
            <h1 class="rv" data-d="1">{{ $doc['title'] }}</h1>
            <span class="legal__updated rv" data-d="2">@include('partials.icon', ['name' => 'check']) Last updated {{ $updated }}</span>
        </div>
    </section>

    <section class="sec" style="padding-top:24px">
        <div class="legal">
            <p class="legal__intro rv">{{ $doc['intro'] }}</p>
            @foreach($doc['sections'] as $sec)
                <div class="legal__sec rv">
                    <h2>{{ $sec['heading'] }}</h2>
                    <ul>
                        @foreach($sec['body'] as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
            <p class="legal__intro" style="margin-top:48px;font-size:15px;color:var(--ink-3)">
                Questions about this policy? Email <a href="mailto:{{ config('site.brand.support') }}" style="color:var(--violet);font-weight:600">{{ config('site.brand.support') }}</a>.
            </p>
        </div>
    </section>
@endsection
