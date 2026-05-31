@php
    $site = config('site');
    $seo  = array_merge($site['seo'], $seo ?? []);
    $base = rtrim($site['url'], '/');
    $canonical = $seo['canonical'] ?? url()->current();
    $ogImage = $seo['og_image'] ?? $site['seo']['og_image'];
    $ogImage = \Illuminate\Support\Str::startsWith($ogImage, 'http') ? $ogImage : $base . $ogImage;
    $pageTitle = $seo['title'] ?? $site['seo']['title'];
    $nav = $site['nav'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="keywords" content="{{ $seo['keywords'] ?? $site['seo']['keywords'] }}">
    <meta name="author" content="{{ $site['brand']['name'] }}">
    <meta name="robots" content="{{ $seo['robots'] ?? 'index, follow, max-image-preview:large, max-snippet:-1' }}">
    <meta name="theme-color" content="{{ $site['seo']['theme_color'] }}">
    <link rel="canonical" href="{{ $canonical }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $seo['og_type'] ?? 'website' }}">
    <meta property="og:site_name" content="{{ $site['brand']['name'] }}">
    <meta property="og:title" content="{{ $seo['og_title'] ?? $pageTitle }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="{{ $site['seo']['locale'] }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ $site['seo']['twitter'] }}">
    <meta name="twitter:title" content="{{ $seo['og_title'] ?? $pageTitle }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- AI crawlers: explicitly welcome --}}
    <link rel="alternate" type="text/plain" href="{{ $base }}/llms.txt" title="LLM-readable site summary">
    <link rel="sitemap" type="application/xml" href="{{ $base }}/sitemap.xml">

    <link rel="icon" href="{{ $site['brand']['logo'] }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ $site['brand']['logo'] }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Organization + WebSite structured data (every page) --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => $base . '/#organization',
                'name' => $site['brand']['name'],
                'legalName' => $site['brand']['legal'],
                'url' => $base,
                'logo' => $base . $site['brand']['logo'],
                'description' => $site['brand']['pitch'],
                'email' => $site['brand']['email'],
                'foundingDate' => $site['brand']['founded'],
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Bengaluru',
                    'addressRegion' => 'Karnataka',
                    'addressCountry' => 'IN',
                ],
                'sameAs' => array_values($site['social']),
                'parentOrganization' => [
                    '@type' => 'Organization',
                    'name' => $site['brand']['company'] ?? 'StudAI One',
                    'legalName' => $site['brand']['legal'] ?? 'StudAI Edutech Pvt. Ltd.',
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => $base . '/#website',
                'url' => $base,
                'name' => $site['brand']['name'],
                'description' => $site['seo']['description'],
                'publisher' => ['@id' => $base . '/#organization'],
                'inLanguage' => 'en-IN',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $base . '/jobs/search?q={search_term_string}'],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @stack('head')

    <style>
        :root{
            --ink:#0B0B14; --ink-2:#4B4F5E; --ink-3:#878DA0;
            --paper:#FFFFFF; --mist:#F5F6FB; --mist-2:#ECEEF6; --line:#E7E9F2; --line-2:#DADDEA;
            --blue:#2563EB; --violet:#7C3AED; --pink:#EC4899; --cyan:#22D3EE; --green:#0E9F6E;
            --grad:linear-gradient(110deg,#2563EB 0%,#7C3AED 48%,#EC4899 100%);
            --grad-soft:linear-gradient(135deg,#EEF3FF,#F7EEFE 55%,#FFEEF6);
            --shadow-sm:0 1px 2px rgba(13,18,40,.06),0 2px 6px rgba(13,18,40,.05);
            --shadow:0 24px 50px -22px rgba(20,24,60,.28),0 8px 18px -12px rgba(20,24,60,.16);
            --shadow-xl:0 60px 120px -40px rgba(28,18,80,.45),0 24px 60px -30px rgba(28,18,80,.30);
            --r:20px; --r-lg:28px; --r-xl:40px; --pill:999px;
            --font:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
            --max:1200px;
        }
        *{box-sizing:border-box;}
        html{scroll-behavior:smooth;-webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility;}
        body{margin:0;font-family:var(--font);color:var(--ink);background:var(--paper);
            font-size:17px;line-height:1.6;letter-spacing:-.011em;overflow-x:hidden;}
        a{color:inherit;text-decoration:none;}
        img{max-width:100%;display:block;}
        ::selection{background:#E9DEFF;color:var(--violet);}
        .wrap{max-width:var(--max);margin:0 auto;padding:0 28px;}
        h1,h2,h3,h4{margin:0;font-weight:800;letter-spacing:-.04em;line-height:1.05;color:var(--ink);}
        p{margin:0;}
        .grad-text{background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent;}
        .eyebrow{display:inline-flex;align-items:center;gap:9px;font-size:13px;font-weight:700;letter-spacing:.04em;
            text-transform:uppercase;color:var(--violet);}
        .eyebrow::before{content:"";width:22px;height:2px;border-radius:2px;background:var(--grad);}
        .lede{color:var(--ink-2);font-size:20px;line-height:1.6;font-weight:450;letter-spacing:-.014em;}

        .btn{display:inline-flex;align-items:center;gap:10px;font-family:var(--font);font-size:16px;font-weight:700;
            letter-spacing:-.01em;border:0;cursor:pointer;padding:15px 28px;border-radius:var(--pill);
            transition:.26s cubic-bezier(.34,1.4,.5,1);white-space:nowrap;}
        .btn svg{width:18px;height:18px;}
        .btn-primary{color:#fff;background:var(--grad);background-size:160% 160%;
            box-shadow:0 10px 30px -8px rgba(124,58,237,.6),0 2px 6px rgba(37,99,235,.4);}
        .btn-primary:hover{transform:translateY(-2px) scale(1.015);background-position:100% 0;}
        .btn-ghost{background:transparent;color:var(--ink);}
        .btn-ghost .arr{transition:transform .26s;} .btn-ghost:hover{color:var(--violet);} .btn-ghost:hover .arr{transform:translateX(5px);}
        .btn-glass{background:rgba(255,255,255,.7);backdrop-filter:blur(10px);border:1px solid var(--line-2);color:var(--ink);box-shadow:var(--shadow-sm);}
        .btn-glass:hover{transform:translateY(-2px);border-color:var(--ink-3);}

        .rv{opacity:0;transform:translateY(34px);transition:opacity .9s cubic-bezier(.16,1,.3,1),transform .9s cubic-bezier(.16,1,.3,1);}
        .rv.in{opacity:1;transform:none;}
        .rv[data-d="1"]{transition-delay:.08s;}.rv[data-d="2"]{transition-delay:.16s;}.rv[data-d="3"]{transition-delay:.24s;}.rv[data-d="4"]{transition-delay:.32s;}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
        @keyframes floatB{0%,100%{transform:translateY(0)}50%{transform:translateY(12px)}}
        @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
        @keyframes shimmer{to{background-position:200% center}}
        @keyframes scrollx{to{transform:translateX(-50%)}}
        @media(prefers-reduced-motion:reduce){.rv{opacity:1;transform:none;transition:none;}*{animation:none!important;}}

        /* NAV */
        .nav{position:sticky;top:0;z-index:80;transition:.35s;}
        .nav::after{content:"";position:absolute;inset:0;z-index:-1;background:rgba(255,255,255,.72);
            backdrop-filter:saturate(180%) blur(20px);-webkit-backdrop-filter:saturate(180%) blur(20px);
            opacity:0;border-bottom:1px solid transparent;transition:.35s;}
        .nav.scrolled::after{opacity:1;border-bottom-color:var(--line);}
        .nav__in{display:flex;align-items:center;gap:28px;height:72px;}
        .brand{display:flex;align-items:center;gap:11px;font-weight:800;font-size:19px;letter-spacing:-.03em;}
        .brand img{width:32px;height:32px;border-radius:9px;}
        .brand b{background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent;}
        .nav__links{display:flex;align-items:center;gap:2px;margin-left:6px;}
        .nav__links > a,.nav__item > button{font:inherit;background:none;border:0;cursor:pointer;color:var(--ink-2);
            font-size:15px;font-weight:600;padding:9px 14px;border-radius:11px;transition:.2s;display:inline-flex;align-items:center;gap:5px;}
        .nav__links > a:hover,.nav__item:hover > button{color:var(--ink);background:var(--mist);}
        .nav__item{position:relative;}
        .nav__item > button svg{width:14px;height:14px;transition:transform .25s;}
        .nav__item:hover > button svg{transform:rotate(180deg);}
        .nav__menu{position:absolute;top:calc(100% + 10px);left:0;width:340px;background:#fff;border:1px solid var(--line);
            border-radius:18px;box-shadow:var(--shadow);padding:10px;opacity:0;visibility:hidden;transform:translateY(8px);
            transition:.24s;display:grid;gap:2px;}
        .nav__item:hover .nav__menu{opacity:1;visibility:visible;transform:none;}
        .nav__menu a{display:flex;flex-direction:column;gap:2px;padding:11px 13px;border-radius:12px;transition:.18s;}
        .nav__menu a:hover{background:var(--mist);}
        .nav__menu b{font-size:14.5px;font-weight:700;color:var(--ink);letter-spacing:-.01em;}
        .nav__menu span{font-size:12.5px;color:var(--ink-3);font-weight:500;}
        .nav__cta{margin-left:auto;display:flex;align-items:center;gap:8px;}
        .nav__signin{font-weight:700;font-size:15px;padding:10px 16px;border-radius:var(--pill);transition:.2s;}
        .nav__signin:hover{background:var(--mist);}
        .burger{display:none;margin-left:auto;width:44px;height:44px;border:1px solid var(--line-2);background:#fff;border-radius:13px;align-items:center;justify-content:center;cursor:pointer;}
        .burger svg{width:21px;height:21px;}
        .mobile{display:none;background:#fff;border-bottom:1px solid var(--line);padding:14px 28px 24px;max-height:80vh;overflow:auto;}
        .mobile a{display:block;padding:13px 6px;font-weight:600;color:var(--ink-2);border-bottom:1px solid var(--mist-2);}
        .mobile .mh{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-3);padding:18px 6px 4px;}
        .mobile .btn{width:100%;justify-content:center;margin-top:16px;}

        /* PAGE HERO (inner pages) */
        .phero{position:relative;text-align:center;padding:96px 0 72px;overflow:hidden;}
        .phero__bg{position:absolute;inset:-20% -10% auto;height:560px;z-index:-1;pointer-events:none;}
        .phero__bg .blob{position:absolute;border-radius:50%;filter:blur(72px);opacity:.5;}
        .phero__bg .b1{width:460px;height:460px;left:8%;top:-60px;background:radial-gradient(circle,#7C9DFF,transparent 65%);animation:float 9s ease-in-out infinite;}
        .phero__bg .b2{width:480px;height:480px;right:6%;top:-30px;background:radial-gradient(circle,#E8A6FF,transparent 65%);animation:floatB 11s ease-in-out infinite;}
        .phero__grid{position:absolute;inset:0 0 auto;height:560px;z-index:-1;
            background-image:linear-gradient(rgba(11,11,20,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(11,11,20,.035) 1px,transparent 1px);
            background-size:46px 46px;mask-image:radial-gradient(circle at 50% 24%,#000,transparent 70%);}
        .phero h1{font-size:clamp(40px,6.4vw,78px);letter-spacing:-.05em;line-height:.98;max-width:16ch;margin:24px auto 0;}
        .phero .lede{max-width:620px;margin:24px auto 0;font-size:clamp(17px,2.2vw,22px);}
        .phero__cta{display:flex;gap:14px;justify-content:center;margin-top:34px;flex-wrap:wrap;}
        .crumb{display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--ink-3);}
        .crumb a:hover{color:var(--violet);}

        section{position:relative;}
        .sec{padding:104px 0;}
        .sec--mist{background:linear-gradient(180deg,#fff,var(--mist) 14%,var(--mist) 86%,#fff);}
        .head{max-width:760px;margin:0 auto;text-align:center;}
        .head h2{font-size:clamp(32px,5vw,58px);margin-top:18px;letter-spacing:-.045em;line-height:1.02;}
        .head .lede{margin-top:20px;}

        /* generic cards grid */
        .grid3{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:60px;}
        .grid2{display:grid;grid-template-columns:repeat(2,1fr);gap:22px;margin-top:60px;}
        .card{position:relative;background:#fff;border:1px solid var(--line);border-radius:var(--r-lg);padding:32px 30px;
            overflow:hidden;transition:.34s cubic-bezier(.16,1,.3,1);}
        .card::before{content:"";position:absolute;inset:0;background:var(--grad-soft);opacity:0;transition:.34s;z-index:0;}
        .card:hover{transform:translateY(-6px);box-shadow:var(--shadow);border-color:transparent;}
        .card:hover::before{opacity:.5;}
        .card > *{position:relative;z-index:1;}
        .card__ic{width:54px;height:54px;border-radius:15px;display:flex;align-items:center;justify-content:center;
            margin-bottom:20px;color:#fff;background:var(--grad);box-shadow:0 12px 26px -10px rgba(40,30,90,.45);}
        .card__ic svg{width:25px;height:25px;}
        .card h3{font-size:20px;letter-spacing:-.03em;}
        .card p{color:var(--ink-2);font-size:15.5px;line-height:1.6;margin-top:11px;font-weight:450;}
        .card__link{display:inline-flex;align-items:center;gap:7px;margin-top:16px;font-size:14.5px;font-weight:700;color:var(--violet);}
        .card__link svg{width:16px;height:16px;transition:transform .25s;} .card:hover .card__link svg{transform:translateX(4px);}

        /* CTA band */
        .cta{padding:104px 0;}
        .cta__box{position:relative;overflow:hidden;border-radius:48px;padding:92px 48px;text-align:center;background:var(--grad);box-shadow:var(--shadow-xl);}
        .cta__box .glow{position:absolute;border-radius:50%;filter:blur(60px);}
        .cta__box .g1{width:400px;height:400px;background:rgba(255,255,255,.3);left:-80px;top:-120px;}
        .cta__box .g2{width:460px;height:460px;background:rgba(34,211,238,.35);right:-100px;bottom:-160px;}
        .cta__box h2{position:relative;color:#fff;font-size:clamp(34px,5.2vw,60px);letter-spacing:-.05em;line-height:1.04;}
        .cta__box p{position:relative;color:rgba(255,255,255,.9);font-size:19px;max-width:560px;margin:22px auto 0;font-weight:450;}
        .cta__a{position:relative;display:flex;gap:14px;justify-content:center;margin-top:38px;flex-wrap:wrap;}
        .cta__a .btn-primary{background:#fff;color:var(--violet);}
        .cta__a .btn-ghost{color:#fff;border:1.5px solid rgba(255,255,255,.55);padding:15px 28px;border-radius:var(--pill);}
        .cta__a .btn-ghost:hover{color:#fff;background:rgba(255,255,255,.14);}

        /* FOOTER */
        .ft{border-top:1px solid var(--line);padding:78px 0 44px;background:var(--mist);}
        .ft__grid{display:grid;grid-template-columns:1.6fr repeat(4,1fr);gap:40px;}
        .ft__brand .brand{margin-bottom:18px;}
        .ft__brand p{color:var(--ink-2);font-size:15px;max-width:300px;line-height:1.6;font-weight:450;}
        .ft__soc{display:flex;gap:10px;margin-top:20px;}
        .ft__soc a{width:38px;height:38px;border-radius:11px;border:1px solid var(--line-2);background:#fff;display:flex;align-items:center;justify-content:center;color:var(--ink-2);transition:.2s;}
        .ft__soc a:hover{color:#fff;background:var(--grad);border-color:transparent;transform:translateY(-2px);}
        .ft__soc svg{width:18px;height:18px;}
        .ft__col h4{font-size:12.5px;font-weight:800;color:var(--ink-3);letter-spacing:.06em;text-transform:uppercase;margin:0 0 18px;}
        .ft__col a{display:block;color:var(--ink-2);font-size:14.5px;font-weight:500;padding:7px 0;transition:.2s;}
        .ft__col a:hover{color:var(--violet);transform:translateX(3px);}
        .ft__base{margin-top:56px;padding-top:26px;border-top:1px solid var(--line-2);display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;font-size:14px;color:var(--ink-3);font-weight:500;}

        @media(max-width:980px){
            .grid3,.grid2{grid-template-columns:1fr;}
            .ft__grid{grid-template-columns:1fr 1fr;} .ft__brand{grid-column:1/-1;}
        }
        @media(max-width:760px){
            .nav__links,.nav__cta .nav__signin{display:none;} .nav__cta .btn{display:none;}
            .nav__cta{margin-left:0;} .burger{display:flex;}
            .sec{padding:72px 0;} .phero{padding:64px 0 48px;}
            .cta__box{padding:60px 26px;border-radius:34px;} .cta{padding:72px 0;}
            .wrap{padding:0 20px;}
            .ft__grid{grid-template-columns:1fr;gap:28px;}
        }
        [x-cloak]{display:none!important;}
    </style>
    @stack('styles')
</head>
<body x-data="{ mobile:false }">

    <header class="nav" id="nav">
        <div class="wrap nav__in">
            <a href="{{ route('home') }}" class="brand">
                <img src="{{ $site['brand']['logo'] }}" alt="{{ $site['brand']['name'] }} logo">
                <span>{{ $site['brand']['wordmark'][0] }}<b>{{ $site['brand']['wordmark'][1] }}</b>{{ $site['brand']['wordmark'][2] }}</span>
            </a>
            <nav class="nav__links" aria-label="Primary">
                @foreach(['product','solutions'] as $group)
                    <div class="nav__item">
                        <button type="button">{{ $nav[$group]['label'] }}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="nav__menu">
                            @foreach($nav[$group]['links'] as $l)
                                <a href="{{ isset($l['param']) ? route($l['route'], $l['param']) : route($l['route']) }}">
                                    <b>{{ $l['label'] }}</b><span>{{ $l['desc'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                @foreach($nav['links'] as $l)
                    <a href="{{ route($l['route']) }}">{{ $l['label'] }}</a>
                @endforeach
            </nav>
            <div class="nav__cta">
                <a href="{{ route($nav['sign_in']['route']) }}" class="nav__signin">{{ $nav['sign_in']['label'] }}</a>
                <a href="{{ route($nav['sign_up']['route']) }}" class="btn btn-primary">{{ $nav['sign_up']['label'] }}</a>
            </div>
            <button class="burger" @click="mobile=!mobile" aria-label="Toggle menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
        </div>
        <div class="mobile" x-show="mobile" x-cloak>
            <div class="mh">Product</div>
            @foreach($nav['product']['links'] as $l)<a href="{{ route($l['route'], $l['param']) }}">{{ $l['label'] }}</a>@endforeach
            <div class="mh">Use cases</div>
            @foreach($nav['solutions']['links'] as $l)<a href="{{ isset($l['param']) ? route($l['route'], $l['param']) : route($l['route']) }}">{{ $l['label'] }}</a>@endforeach
            <div class="mh">Company</div>
            @foreach($nav['links'] as $l)<a href="{{ route($l['route']) }}">{{ $l['label'] }}</a>@endforeach
            <a href="{{ route($nav['sign_in']['route']) }}">{{ $nav['sign_in']['label'] }}</a>
            <a href="{{ route($nav['sign_up']['route']) }}" class="btn btn-primary">{{ $nav['sign_up']['label'] }}</a>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Closing CTA (shared, can be hidden with @section('no_cta')) --}}
    @hasSection('no_cta')
    @else
    <section class="cta">
        <div class="wrap">
            <div class="cta__box rv">
                <span class="glow g1"></span><span class="glow g2"></span>
                <h2>@yield('cta_title', 'Let your agent go get the job.')</h2>
                <p>@yield('cta_sub', 'Join thousands building their careers on autopilot. Free to start — no credit card required.')</p>
                <div class="cta__a">
                    <a href="{{ route('register') }}" class="btn btn-primary">Get started free
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-ghost">Talk to us</a>
                </div>
            </div>
        </div>
    </section>
    @endif

    <footer class="ft">
        <div class="wrap">
            <div class="ft__grid">
                <div class="ft__brand">
                    <a href="{{ route('home') }}" class="brand">
                        <img src="{{ $site['brand']['logo'] }}" alt="{{ $site['brand']['name'] }} logo">
                        <span>{{ $site['brand']['wordmark'][0] }}<b>{{ $site['brand']['wordmark'][1] }}</b>{{ $site['brand']['wordmark'][2] }}</span>
                    </a>
                    <p>{{ $site['footer']['tagline'] }}</p>
                    <div class="ft__soc">
                        <a href="{{ $site['social']['twitter'] }}" aria-label="Twitter" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-7 8 8.2 12h-6.5l-5-7-5.8 7H2l7.5-8.6L1.6 2h6.6l4.6 6.4L18.9 2Zm-1.1 18h1.8L7.3 4H5.4l12.4 16Z"/></svg></a>
                        <a href="{{ $site['social']['linkedin'] }}" aria-label="LinkedIn" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5A2.5 2.5 0 1 0 5 8.5a2.5 2.5 0 0 0-.02-5ZM3 9h4v12H3V9Zm6 0h3.8v1.6h.05c.53-1 1.83-2.1 3.77-2.1 4.03 0 4.78 2.65 4.78 6.1V21H17v-5.6c0-1.33-.02-3.05-1.86-3.05-1.86 0-2.14 1.45-2.14 2.95V21H9V9Z"/></svg></a>
                        <a href="{{ $site['social']['instagram'] }}" aria-label="Instagram" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a>
                        <a href="{{ $site['social']['youtube'] }}" aria-label="YouTube" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 12s0-3.2-.4-4.7a2.5 2.5 0 0 0-1.8-1.8C19.3 5 12 5 12 5s-7.3 0-8.8.5a2.5 2.5 0 0 0-1.8 1.8C1 8.8 1 12 1 12s0 3.2.4 4.7a2.5 2.5 0 0 0 1.8 1.8C4.7 19 12 19 12 19s7.3 0 8.8-.5a2.5 2.5 0 0 0 1.8-1.8C23 15.2 23 12 23 12Zm-13 3V9l5 3-5 3Z"/></svg></a>
                    </div>
                </div>
                @foreach($site['footer']['columns'] as $col)
                    <div class="ft__col">
                        <h4>{{ $col['heading'] }}</h4>
                        @foreach($col['links'] as $l)
                            <a href="{{ isset($l['param']) ? route($l['route'], $l['param']) : route($l['route']) }}">{{ $l['label'] }}</a>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="ft__base">
                <span>{{ $site['footer']['legal'] }}</span>
                <span>{{ $site['brand']['tagline'] }}</span>
            </div>
        </div>
    </footer>

    <script>
        const nav = document.getElementById('nav');
        const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 8);
        onScroll(); window.addEventListener('scroll', onScroll, { passive:true });
        const io = new IntersectionObserver((es) => {
            es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
        }, { threshold:0.12, rootMargin:'0px 0px -60px 0px' });
        document.querySelectorAll('.rv').forEach(el => io.observe(el));
    </script>
    @stack('scripts')
</body>
</html>
