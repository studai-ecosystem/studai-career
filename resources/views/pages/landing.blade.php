<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page['brand']['name'] }} — {{ $page['brand']['tagline'] }}</title>
    <meta name="description" content="{{ $page['hero']['lede'] }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $p = $page;
        $accent = [
            'blue'   => ['#2563EB', '#EEF3FF', 'linear-gradient(135deg,#2563EB,#7C3AED)'],
            'green'  => ['#0E9F6E', '#E7F8F1', 'linear-gradient(135deg,#0E9F6E,#22D3EE)'],
            'violet' => ['#7C3AED', '#F3EEFE', 'linear-gradient(135deg,#7C3AED,#EC4899)'],
            'amber'  => ['#D97706', '#FEF4E6', 'linear-gradient(135deg,#F59E0B,#EC4899)'],
            'slate'  => ['#334155', '#EEF1F6', 'linear-gradient(135deg,#334155,#64748B)'],
        ];
    @endphp

    <style>
        /* ════ TOKENS ══════════════════════════════════════════════════════ */
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
            font-size:17px;line-height:1.6;letter-spacing:-.011em;overflow-x:hidden;padding-top:64px;}
        /* Shared marketing header (partials.marketing-header) scrolled state */
        .nav-scrolled{background:#fff !important;box-shadow:0 1px 0 rgba(0,0,0,.04) !important;border-bottom:1px solid var(--line) !important;}
        a{color:inherit;text-decoration:none;}
        img{max-width:100%;display:block;}
        ::selection{background:#E9DEFF;color:var(--violet);}
        .wrap{max-width:var(--max);margin:0 auto;padding:0 28px;}
        h1,h2,h3{margin:0;font-weight:800;letter-spacing:-.04em;line-height:1.02;color:var(--ink);}
        .grad-text{background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent;}
        .eyebrow{display:inline-flex;align-items:center;gap:9px;font-size:13px;font-weight:700;letter-spacing:.04em;
            text-transform:uppercase;color:var(--violet);}
        .eyebrow::before{content:"";width:22px;height:2px;border-radius:2px;background:var(--grad);}
        .lede{color:var(--ink-2);font-size:20px;line-height:1.6;font-weight:450;letter-spacing:-.014em;}

        /* ════ BUTTONS ═════════════════════════════════════════════════════ */
        .btn{display:inline-flex;align-items:center;gap:10px;font-family:var(--font);font-size:16px;font-weight:700;
            letter-spacing:-.01em;border:0;cursor:pointer;padding:15px 28px;border-radius:var(--pill);
            transition:.26s cubic-bezier(.34,1.4,.5,1);white-space:nowrap;}
        .btn svg{width:18px;height:18px;}
        .btn-primary{color:#fff;background:var(--grad);background-size:160% 160%;
            box-shadow:0 10px 30px -8px rgba(124,58,237,.6),0 2px 6px rgba(37,99,235,.4);}
        .btn-primary:hover{transform:translateY(-2px) scale(1.015);background-position:100% 0;
            box-shadow:0 18px 44px -10px rgba(124,58,237,.7);}
        .btn-ghost{background:transparent;color:var(--ink);}
        .btn-ghost .arr{transition:transform .26s;}
        .btn-ghost:hover{color:var(--violet);}
        .btn-ghost:hover .arr{transform:translateX(5px);}
        .btn-glass{background:rgba(255,255,255,.7);backdrop-filter:blur(10px);border:1px solid var(--line-2);
            color:var(--ink);box-shadow:var(--shadow-sm);}
        .btn-glass:hover{transform:translateY(-2px);border-color:var(--ink-3);}

        /* ════ REVEAL / MOTION ════════════════════════════════════════════ */
        .rv{opacity:0;transform:translateY(34px);transition:opacity .9s cubic-bezier(.16,1,.3,1),transform .9s cubic-bezier(.16,1,.3,1);}
        .rv.in{opacity:1;transform:none;}
        .rv[data-d="1"]{transition-delay:.08s;} .rv[data-d="2"]{transition-delay:.16s;}
        .rv[data-d="3"]{transition-delay:.24s;} .rv[data-d="4"]{transition-delay:.32s;}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
        @keyframes floatB{0%,100%{transform:translateY(0)}50%{transform:translateY(12px)}}
        @keyframes spin{to{transform:rotate(360deg)}}
        @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
        @keyframes shimmer{to{background-position:200% center}}
        @media(prefers-reduced-motion:reduce){.rv{opacity:1;transform:none;transition:none;} *{animation:none!important;}}

        /* ════ NAV ═════════════════════════════════════════════════════════ */
        .nav{position:sticky;top:0;z-index:60;transition:.35s;}
        .nav::after{content:"";position:absolute;inset:0;z-index:-1;background:rgba(255,255,255,.72);
            backdrop-filter:saturate(180%) blur(20px);-webkit-backdrop-filter:saturate(180%) blur(20px);
            opacity:0;border-bottom:1px solid transparent;transition:.35s;}
        .nav.scrolled::after{opacity:1;border-bottom-color:var(--line);}
        .nav__in{display:flex;align-items:center;gap:30px;height:72px;}
        .brand{display:flex;align-items:center;gap:11px;font-weight:800;font-size:19px;letter-spacing:-.03em;}
        .brand img{width:32px;height:32px;border-radius:9px;}
        .brand b{background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent;}
        .nav__links{display:flex;gap:4px;margin-left:6px;}
        .nav__links a{color:var(--ink-2);font-size:15px;font-weight:600;padding:9px 15px;border-radius:11px;transition:.2s;}
        .nav__links a:hover{color:var(--ink);background:var(--mist);}
        .nav__cta{margin-left:auto;display:flex;align-items:center;gap:8px;}
        .nav__signin{font-weight:700;font-size:15px;padding:10px 16px;border-radius:var(--pill);transition:.2s;}
        .nav__signin:hover{background:var(--mist);}
        .burger{display:none;margin-left:auto;width:44px;height:44px;border:1px solid var(--line-2);background:#fff;
            border-radius:13px;align-items:center;justify-content:center;cursor:pointer;}
        .burger svg{width:21px;height:21px;}
        .mobile{display:none;background:#fff;border-bottom:1px solid var(--line);padding:14px 28px 24px;}
        .mobile a{display:block;padding:13px 6px;font-weight:600;color:var(--ink-2);border-bottom:1px solid var(--mist-2);}
        .mobile .btn{width:100%;justify-content:center;margin-top:16px;}

        /* ════ HERO ════════════════════════════════════════════════════════ */
        .hero{position:relative;text-align:center;padding:74px 0 0;overflow:hidden;}
        .hero__bg{position:absolute;inset:-10% -20% auto;height:760px;z-index:-2;pointer-events:none;}
        .blob{position:absolute;border-radius:50%;filter:blur(70px);opacity:.55;}
        .blob.b1{width:520px;height:520px;left:6%;top:-80px;background:radial-gradient(circle,#7C9DFF,transparent 65%);animation:float 9s ease-in-out infinite;}
        .blob.b2{width:560px;height:560px;right:4%;top:-40px;background:radial-gradient(circle,#E8A6FF,transparent 65%);animation:floatB 11s ease-in-out infinite;}
        .blob.b3{width:440px;height:440px;left:42%;top:120px;background:radial-gradient(circle,#FFB6D9,transparent 66%);animation:float 13s ease-in-out infinite;opacity:.4;}
        .hero__grid{position:absolute;inset:0 0 auto;height:760px;z-index:-1;pointer-events:none;
            background-image:linear-gradient(rgba(11,11,20,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(11,11,20,.035) 1px,transparent 1px);
            background-size:46px 46px;mask-image:radial-gradient(circle at 50% 28%,#000,transparent 72%);}
        .hero__chip{display:inline-flex;align-items:center;gap:10px;background:rgba(255,255,255,.8);backdrop-filter:blur(10px);
            border:1px solid var(--line);box-shadow:var(--shadow-sm);border-radius:var(--pill);padding:8px 18px 8px 10px;
            font-size:14px;font-weight:600;color:var(--ink-2);}
        .hero__chip .pip{width:9px;height:9px;border-radius:50%;background:var(--grad);box-shadow:0 0 0 4px rgba(124,58,237,.14);}
        .hero__chip b{background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:800;}
        h1.hero__title{margin:26px auto 0;font-size:clamp(46px,8.4vw,104px);letter-spacing:-.052em;line-height:.96;max-width:14ch;}
        h1.hero__title .accent{display:block;background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent;
            background-size:200% auto;animation:shimmer 6s linear infinite;}
        .hero__lede{max-width:600px;margin:28px auto 0;font-size:clamp(17px,2.2vw,22px);}
        .hero__actions{display:flex;gap:14px;justify-content:center;margin-top:38px;flex-wrap:wrap;}
        .hero__trust{margin-top:22px;font-size:14px;color:var(--ink-3);font-weight:600;display:inline-flex;align-items:center;gap:9px;}
        .hero__trust svg{width:17px;height:17px;color:var(--green);}

        /* hero showcase mockup */
        .showcase{position:relative;margin:64px auto 0;max-width:1020px;padding:0 28px;}
        .showcase__win{position:relative;background:#fff;border:1px solid var(--line);border-radius:22px;
            box-shadow:var(--shadow-xl);overflow:hidden;z-index:2;}
        .win__bar{display:flex;align-items:center;gap:8px;padding:15px 18px;border-bottom:1px solid var(--line);background:#FBFBFE;}
        .win__dot{width:11px;height:11px;border-radius:50%;}
        .win__url{margin:0 auto;font-size:12.5px;font-weight:600;color:var(--ink-3);background:#fff;border:1px solid var(--line);
            padding:6px 18px;border-radius:var(--pill);}
        .app{display:grid;grid-template-columns:200px 1fr;min-height:430px;}
        .app__side{border-right:1px solid var(--line);padding:20px 16px;background:#FBFBFE;}
        .app__brand{display:flex;align-items:center;gap:9px;font-weight:800;font-size:15px;margin-bottom:22px;}
        .app__brand i{width:26px;height:26px;border-radius:8px;background:var(--grad);display:block;}
        .app__nav{display:grid;gap:5px;}
        .app__nav a{display:flex;align-items:center;gap:11px;font-size:13.5px;font-weight:600;color:var(--ink-3);
            padding:10px 12px;border-radius:11px;}
        .app__nav a.on{background:#fff;color:var(--ink);box-shadow:var(--shadow-sm);}
        .app__nav a.on .ic{background:var(--grad);}
        .app__nav .ic{width:18px;height:18px;border-radius:6px;background:var(--mist-2);flex:none;}
        .app__main{padding:24px 26px;}
        .app__h{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;}
        .app__h h4{font-size:18px;letter-spacing:-.03em;}
        .app__live{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:var(--green);
            background:#E7F8F1;padding:6px 13px;border-radius:var(--pill);}
        .app__live .d{width:7px;height:7px;border-radius:50%;background:var(--green);animation:pulse 1.8s infinite;}
        .job{display:flex;align-items:center;gap:14px;background:#fff;border:1px solid var(--line);border-radius:14px;
            padding:14px 16px;box-shadow:var(--shadow-sm);}
        .job + .job{margin-top:10px;}
        .job__lg{width:38px;height:38px;border-radius:10px;flex:none;display:flex;align-items:center;justify-content:center;
            color:#fff;font-weight:800;font-size:14px;}
        .job__t b{font-size:14.5px;font-weight:700;display:block;letter-spacing:-.01em;}
        .job__t span{font-size:12.5px;color:var(--ink-3);font-weight:500;}
        .job__tag{margin-left:auto;font-size:11.5px;font-weight:800;padding:6px 12px;border-radius:var(--pill);}
        /* floating cards */
        .fc{position:absolute;background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow);
            padding:14px 16px;z-index:3;display:flex;align-items:center;gap:12px;}
        .fc__ic{width:38px;height:38px;border-radius:11px;flex:none;display:flex;align-items:center;justify-content:center;color:#fff;}
        .fc__ic svg{width:19px;height:19px;}
        .fc b{font-size:14px;font-weight:700;display:block;letter-spacing:-.01em;}
        .fc span{font-size:12px;color:var(--ink-3);font-weight:600;}
        .fc.fc1{left:-34px;top:90px;animation:float 7s ease-in-out infinite;}
        .fc.fc2{right:-30px;top:240px;animation:floatB 8.5s ease-in-out infinite;}
        .fc.fc3{left:6%;bottom:-26px;animation:float 10s ease-in-out infinite;}

        /* ════ MARQUEE LOGOS ═══════════════════════════════════════════════ */
        .logos{padding:88px 0 30px;}
        .logos__label{text-align:center;font-size:13px;font-weight:700;color:var(--ink-3);text-transform:uppercase;letter-spacing:.08em;}
        .marquee{margin-top:30px;-webkit-mask-image:linear-gradient(90deg,transparent,#000 12%,#000 88%,transparent);
            mask-image:linear-gradient(90deg,transparent,#000 12%,#000 88%,transparent);overflow:hidden;}
        .marquee__track{display:flex;gap:64px;width:max-content;animation:scrollx 28s linear infinite;}
        .marquee:hover .marquee__track{animation-play-state:paused;}
        .marquee__track span{font-size:26px;font-weight:800;letter-spacing:-.04em;color:#B9BECE;transition:.3s;flex:none;}
        .marquee__track span:hover{color:var(--ink-2);}
        @keyframes scrollx{to{transform:translateX(-50%)}}

        /* ════ SECTION SHELL ═══════════════════════════════════════════════ */
        section{position:relative;}
        .sec{padding:120px 0;}
        .sec--mist{background:linear-gradient(180deg,#fff,var(--mist) 14%,var(--mist) 86%,#fff);}
        .head{max-width:760px;margin:0 auto;text-align:center;}
        .head h2{font-size:clamp(34px,5.2vw,64px);margin-top:18px;letter-spacing:-.045em;line-height:1.02;}
        .head .lede{margin-top:22px;}

        /* ════ STATEMENT (cinematic editorial) ════════════════════════════ */
        .stmt{padding:130px 0;text-align:center;position:relative;overflow:hidden;}
        .stmt::before{content:"";position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);
            width:900px;height:900px;background:var(--grad-soft);border-radius:50%;filter:blur(40px);z-index:-1;opacity:.7;}
        .stmt h2{font-size:clamp(38px,7vw,86px);letter-spacing:-.05em;line-height:1.0;max-width:16ch;margin:18px auto 0;}
        .stmt p{max-width:620px;margin:26px auto 0;font-size:clamp(17px,2.2vw,22px);color:var(--ink-2);font-weight:450;}
        .stmt__chips{display:flex;flex-wrap:wrap;justify-content:center;gap:12px;margin-top:36px;}
        .stmt__chips span{background:rgba(255,255,255,.8);backdrop-filter:blur(8px);border:1px solid var(--line);
            box-shadow:var(--shadow-sm);border-radius:var(--pill);padding:11px 22px;font-size:15px;font-weight:700;letter-spacing:-.01em;}

        /* ════ PILLARS (full-bleed alternating, cinematic) ════════════════ */
        .pillar{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;}
        .pillar + .pillar{margin-top:140px;}
        .pillar.flip .pillar__media{order:-1;}
        .pillar__tag{display:inline-flex;align-items:center;gap:9px;font-size:13px;font-weight:800;text-transform:uppercase;
            letter-spacing:.05em;padding:7px 15px;border-radius:var(--pill);}
        .pillar h3{font-size:clamp(30px,4.2vw,52px);margin-top:20px;letter-spacing:-.045em;line-height:1.02;}
        .pillar p{color:var(--ink-2);margin-top:18px;font-size:19px;line-height:1.62;font-weight:450;}
        .pillar__pts{list-style:none;padding:0;margin:30px 0 34px;display:grid;gap:15px;}
        .pillar__pts li{display:flex;align-items:flex-start;gap:13px;font-size:16.5px;font-weight:600;}
        .pillar__pts .ck{width:24px;height:24px;flex:none;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-top:1px;color:#fff;}
        .pillar__pts .ck svg{width:14px;height:14px;}
        /* pillar visuals */
        .viz{position:relative;border-radius:var(--r-xl);padding:34px;min-height:400px;overflow:hidden;
            box-shadow:var(--shadow-xl);display:flex;align-items:center;}
        .viz__deco{position:absolute;inset:0;z-index:0;opacity:.9;}
        .viz__deco .ring{position:absolute;border-radius:50%;border:1.5px dashed rgba(255,255,255,.5);}
        .viz__inner{position:relative;z-index:1;width:100%;display:grid;gap:14px;}
        .glass-card{background:rgba(255,255,255,.92);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.7);
            border-radius:18px;padding:16px 18px;box-shadow:0 18px 40px -20px rgba(20,20,60,.4);display:flex;align-items:center;gap:14px;}
        .glass-card .av{width:40px;height:40px;border-radius:12px;flex:none;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:15px;}
        .glass-card b{font-size:15px;font-weight:700;display:block;letter-spacing:-.01em;}
        .glass-card span{font-size:12.5px;color:var(--ink-3);font-weight:600;}
        .glass-card .tag{margin-left:auto;font-size:11.5px;font-weight:800;padding:6px 12px;border-radius:var(--pill);color:#fff;background:rgba(0,0,0,.18);}
        .bar{height:11px;border-radius:7px;background:rgba(255,255,255,.45);overflow:hidden;}
        .bar i{display:block;height:100%;border-radius:7px;background:#fff;}
        .viz-panel{background:rgba(255,255,255,.92);backdrop-filter:blur(10px);border-radius:18px;padding:20px;
            box-shadow:0 18px 40px -20px rgba(20,20,60,.4);}
        .viz-panel .row{display:flex;justify-content:space-between;font-size:13.5px;font-weight:700;color:var(--ink-2);margin-bottom:10px;}

        /* ════ MODULES (rich card grid) ═══════════════════════════════════ */
        .mods{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:64px;}
        .mod{position:relative;background:#fff;border:1px solid var(--line);border-radius:var(--r-lg);padding:34px 30px;
            overflow:hidden;transition:.34s cubic-bezier(.16,1,.3,1);}
        .mod::before{content:"";position:absolute;inset:0;background:var(--grad-soft);opacity:0;transition:.34s;z-index:0;}
        .mod:hover{transform:translateY(-6px);box-shadow:var(--shadow);border-color:transparent;}
        .mod:hover::before{opacity:.5;}
        .mod > *{position:relative;z-index:1;}
        .mod__ic{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;
            margin-bottom:22px;color:#fff;box-shadow:0 12px 26px -10px rgba(40,30,90,.45);}
        .mod__ic svg{width:26px;height:26px;}
        .mod h3{font-size:21px;letter-spacing:-.03em;}
        .mod p{color:var(--ink-2);font-size:15.5px;line-height:1.6;margin-top:12px;font-weight:450;}
        .mod__arrow{margin-top:18px;width:34px;height:34px;border-radius:50%;border:1px solid var(--line-2);
            display:flex;align-items:center;justify-content:center;color:var(--ink-3);transition:.3s;}
        .mod:hover .mod__arrow{background:var(--ink);border-color:var(--ink);color:#fff;transform:rotate(-45deg);}
        .mod__arrow svg{width:16px;height:16px;}

        /* ════ STEPS (how it works) ═══════════════════════════════════════ */
        .steps{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;margin-top:64px;counter-reset:s;}
        .stp{position:relative;}
        .stp__n{font-size:54px;font-weight:900;letter-spacing:-.06em;line-height:1;
            background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent;}
        .stp h3{font-size:21px;margin-top:18px;letter-spacing:-.03em;}
        .stp p{font-size:15.5px;color:var(--ink-2);margin-top:12px;line-height:1.58;font-weight:450;}
        .stp__meta{display:inline-flex;margin-top:18px;font-size:12.5px;font-weight:700;color:var(--violet);
            background:#F3EEFE;border-radius:var(--pill);padding:6px 14px;}

        /* ════ TIMELINE (day on autopilot) ═══════════════════════════════ */
        .tl{margin-top:64px;display:grid;grid-template-columns:repeat(5,1fr);position:relative;}
        .tl::before{content:"";position:absolute;top:13px;left:8%;right:8%;height:3px;border-radius:3px;background:var(--grad);}
        .tl__i{text-align:center;padding:0 12px;position:relative;}
        .tl__node{width:28px;height:28px;border-radius:50%;background:#fff;border:3px solid transparent;
            background-image:linear-gradient(#fff,#fff),var(--grad);background-origin:border-box;background-clip:padding-box,border-box;
            margin:0 auto 24px;position:relative;z-index:1;box-shadow:0 0 0 6px #fff;display:flex;align-items:center;justify-content:center;}
        .tl__node i{width:10px;height:10px;border-radius:50%;background:var(--grad);}
        .tl__phase{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;
            background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent;}
        .tl__i h3{font-size:17px;margin-top:10px;letter-spacing:-.025em;}
        .tl__i p{font-size:14px;color:var(--ink-2);margin-top:10px;line-height:1.55;font-weight:450;}

        /* ════ COMPARISON ════════════════════════════════════════════════ */
        .cmp{display:grid;grid-template-columns:1fr 1fr;gap:26px;margin-top:64px;}
        .cmp__col{border-radius:var(--r-xl);padding:42px 38px;}
        .cmp__old{background:#fff;border:1px solid var(--line);}
        .cmp__new{position:relative;color:#fff;overflow:hidden;background:var(--grad);box-shadow:var(--shadow);}
        .cmp__new::before{content:"";position:absolute;inset:0;background:radial-gradient(70% 90% at 80% 0,rgba(255,255,255,.22),transparent 60%);}
        .cmp__lab{position:relative;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;margin-bottom:26px;display:inline-flex;align-items:center;gap:9px;}
        .cmp__old .cmp__lab{color:var(--ink-3);}
        .cmp ul{position:relative;list-style:none;padding:0;margin:0;display:grid;gap:17px;}
        .cmp li{display:flex;align-items:flex-start;gap:14px;font-size:16px;line-height:1.5;font-weight:500;}
        .cmp__old li{color:var(--ink-2);}
        .cmp__ic{width:24px;height:24px;flex:none;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-top:1px;}
        .cmp__ic svg{width:14px;height:14px;}
        .cmp__old .cmp__ic{background:var(--mist-2);color:var(--ink-3);}
        .cmp__new .cmp__ic{background:rgba(255,255,255,.22);color:#fff;}

        /* ════ TESTIMONIALS ══════════════════════════════════════════════ */
        .quotes{display:grid;grid-template-columns:repeat(2,1fr);gap:24px;margin-top:64px;}
        .quote{position:relative;background:#fff;border:1px solid var(--line);border-radius:var(--r-lg);padding:40px 38px 32px;
            transition:.32s;overflow:hidden;}
        .quote::after{content:"";position:absolute;left:0;top:0;width:4px;height:0;background:var(--grad);transition:.4s;}
        .quote:hover{box-shadow:var(--shadow);transform:translateY(-4px);}
        .quote:hover::after{height:100%;}
        .quote__m{font-family:Georgia,serif;font-size:60px;line-height:.6;height:30px;
            background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent;}
        .quote p{font-size:20px;line-height:1.55;margin:18px 0 28px;letter-spacing:-.018em;font-weight:500;}
        .quote__w{display:flex;align-items:center;gap:15px;}
        .quote__av{width:50px;height:50px;border-radius:50%;flex:none;display:flex;align-items:center;justify-content:center;
            color:#fff;font-weight:800;font-size:16px;background:var(--grad);}
        .quote__w b{font-size:16px;font-weight:700;display:block;}
        .quote__w span{font-size:13.5px;color:var(--ink-3);font-weight:500;}

        /* ════ FAQ ═══════════════════════════════════════════════════════ */
        .faq{max-width:840px;margin:60px auto 0;}
        details.q{background:#fff;border:1px solid var(--line);border-radius:18px;margin-bottom:14px;transition:.3s;overflow:hidden;}
        details.q[open]{box-shadow:var(--shadow);border-color:transparent;}
        details.q summary{list-style:none;cursor:pointer;display:flex;align-items:center;gap:18px;padding:24px 26px;
            font-size:19px;font-weight:700;letter-spacing:-.025em;}
        details.q summary::-webkit-details-marker{display:none;}
        details.q .qm{margin-left:auto;width:32px;height:32px;flex:none;border-radius:50%;display:flex;align-items:center;justify-content:center;
            background:var(--mist);color:var(--ink-2);transition:.3s;}
        details.q .qm svg{width:16px;height:16px;transition:transform .3s;}
        details.q[open] .qm{background:var(--grad);color:#fff;}
        details.q[open] .qm svg{transform:rotate(45deg);}
        details.q .ans{padding:0 70px 26px 26px;color:var(--ink-2);font-size:16.5px;line-height:1.65;font-weight:450;}

        /* ════ CTA ═══════════════════════════════════════════════════════ */
        .cta{padding:120px 0;}
        .cta__box{position:relative;overflow:hidden;border-radius:48px;padding:100px 48px;text-align:center;
            background:var(--grad);box-shadow:var(--shadow-xl);}
        .cta__box .glow{position:absolute;border-radius:50%;filter:blur(60px);}
        .cta__box .g1{width:400px;height:400px;background:rgba(255,255,255,.3);left:-80px;top:-120px;}
        .cta__box .g2{width:460px;height:460px;background:rgba(34,211,238,.35);right:-100px;bottom:-160px;}
        .cta__box h2{position:relative;color:#fff;font-size:clamp(36px,5.6vw,68px);letter-spacing:-.05em;line-height:1.02;}
        .cta__box h2 span{display:block;color:rgba(255,255,255,.82);}
        .cta__box p{position:relative;color:rgba(255,255,255,.9);font-size:20px;max-width:560px;margin:24px auto 0;font-weight:450;}
        .cta__a{position:relative;display:flex;gap:14px;justify-content:center;margin-top:42px;flex-wrap:wrap;}
        .cta__a .btn-primary{background:#fff;color:var(--violet);box-shadow:0 14px 40px -10px rgba(0,0,0,.4);}
        .cta__a .btn-primary:hover{background:#fff;}
        .cta__a .btn-ghost{color:#fff;border:1.5px solid rgba(255,255,255,.55);padding:15px 28px;border-radius:var(--pill);}
        .cta__a .btn-ghost:hover{color:#fff;background:rgba(255,255,255,.14);}

        /* ════ FOOTER ════════════════════════════════════════════════════ */
        .ft{border-top:1px solid var(--line);padding:80px 0 44px;}
        .ft__grid{display:grid;grid-template-columns:1.5fr repeat(3,1fr);gap:44px;}
        .ft__brand .brand{margin-bottom:18px;}
        .ft__brand p{color:var(--ink-2);font-size:15.5px;max-width:290px;line-height:1.6;font-weight:450;}
        .ft__col h4{font-size:12.5px;font-weight:800;color:var(--ink-3);letter-spacing:.06em;text-transform:uppercase;margin:0 0 20px;}
        .ft__col a{display:block;color:var(--ink-2);font-size:15.5px;font-weight:500;padding:8px 0;transition:.2s;}
        .ft__col a:hover{color:var(--violet);transform:translateX(3px);}
        .ft__base{margin-top:60px;padding-top:28px;border-top:1px solid var(--line);display:flex;justify-content:space-between;
            align-items:center;gap:16px;flex-wrap:wrap;font-size:14px;color:var(--ink-3);font-weight:500;}

        /* ════ RESPONSIVE ════════════════════════════════════════════════ */
        @media(max-width:980px){
            .pillar,.cmp,.quotes{grid-template-columns:1fr;gap:44px;}
            .pillar.flip .pillar__media{order:0;}
            .pillar + .pillar{margin-top:96px;}
            .mods{grid-template-columns:repeat(2,1fr);}
            .steps{grid-template-columns:repeat(2,1fr);gap:40px 24px;}
            .app{grid-template-columns:1fr;} .app__side{display:none;}
            .tl{grid-template-columns:1fr;} .tl::before{display:none;}
            .tl__i{display:grid;grid-template-columns:auto 1fr;gap:18px;text-align:left;padding:0 0 34px;}
            .tl__node{margin:0;}
            .fc{display:none;}
            .ft__grid{grid-template-columns:1fr 1fr;} .ft__brand{grid-column:1/-1;}
        }
        @media(max-width:680px){
            body{font-size:16px;}
            .nav__links,.nav__cta .nav__signin,.nav__cta .btn{display:none;}
            .nav__cta{margin-left:0;} .burger{display:flex;}
            .sec{padding:84px 0;} .stmt{padding:96px 0;} .hero{padding:48px 0 0;}
            .mods{grid-template-columns:1fr;} .steps{grid-template-columns:1fr;}
            .showcase{margin-top:48px;padding:0 18px;} .app__main{padding:18px;}
            .cmp__col,.viz{padding:30px 24px;}
            .cta__box{padding:64px 26px;border-radius:34px;} .cta{padding:80px 0;}
            .wrap{padding:0 20px;}
            .ft__grid{grid-template-columns:1fr;gap:30px;}
        }
        [x-cloak]{display:none!important;}
    </style>
</head>
<body x-data="{ mobile:false }">

    {{-- ════ NAV (shared marketing header) ═════════════════════════════ --}}
    @include('partials.marketing-header')

    {{-- ════ HERO ══════════════════════════════════════════════════════ --}}
    <section class="hero">
        <div class="hero__bg"><span class="blob b1"></span><span class="blob b2"></span><span class="blob b3"></span></div>
        <div class="hero__grid"></div>
        <div class="wrap">
            <span class="hero__chip rv"><span class="pip"></span>{{ $p['hero']['eyebrow'] }} · <b>AI does the work</b></span>
            <h1 class="hero__title rv" data-d="1">{{ $p['hero']['title']['lead'] }}<span class="accent">{{ $p['hero']['title']['emph'] }}</span></h1>
            <p class="lede hero__lede rv" data-d="2">{{ $p['hero']['lede'] }}</p>
            <div class="hero__actions rv" data-d="3">
                <a href="{{ route($p['hero']['primary']['route']) }}" class="btn btn-primary">
                    {{ $p['hero']['primary']['label'] }}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
                <a href="{{ route($p['hero']['secondary']['route']) }}" class="btn btn-glass">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m10 9 5 3-5 3z" fill="currentColor"/></svg>
                    {{ $p['hero']['secondary']['label'] }}
                </a>
            </div>
            <div class="hero__trust rv" data-d="3">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                {{ $p['hero']['trust'] }}
            </div>
        </div>

        {{-- Cinematic product showcase --}}
        <div class="showcase rv" data-d="2">
            <div class="fc fc1">
                <span class="fc__ic" style="background:var(--grad)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h7l-1 8 10-12h-7l1-8Z"/></svg></span>
                <div><b>Applied for you</b><span>While you slept</span></div>
            </div>
            <div class="fc fc2">
                <span class="fc__ic" style="background:linear-gradient(135deg,#0E9F6E,#22D3EE)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
                <div><b>Interview booked</b><span>Prep is ready</span></div>
            </div>
            <div class="fc fc3">
                <span class="fc__ic" style="background:linear-gradient(135deg,#7C3AED,#EC4899)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg></span>
                <div><b>Offer, raised</b><span>Backed by market data</span></div>
            </div>

            <div class="showcase__win">
                <div class="win__bar">
                    <span class="win__dot" style="background:#FF5F57"></span><span class="win__dot" style="background:#FEBC2E"></span><span class="win__dot" style="background:#28C840"></span>
                    <span class="win__url">app.studaihire.com</span>
                </div>
                <div class="app">
                    <aside class="app__side">
                        <div class="app__brand"><i></i> {{ $p['brand']['name'] }}</div>
                        <nav class="app__nav">
                            <a class="on"><span class="ic"></span> Autopilot</a>
                            <a><span class="ic"></span> Job Search</a>
                            <a><span class="ic"></span> Resume Studio</a>
                            <a><span class="ic"></span> Interview AI</a>
                            <a><span class="ic"></span> Negotiation</a>
                        </nav>
                    </aside>
                    <main class="app__main">
                        <div class="app__h">
                            <h4>{{ $p['hero']['panel']['label'] }}</h4>
                            <span class="app__live"><span class="d"></span>{{ $p['hero']['panel']['status'] }}</span>
                        </div>
                        @php
                            $jobColors = ['#4285F4', '#7C3AED', '#0E9F6E'];
                            $jobData = [
                                ['G', 'Senior Software Engineer', 'Matched to your goals', 'Strong fit', '#EEF3FF', '#2563EB'],
                                ['M', 'Product Engineer', 'Application tailored', 'Applying', '#F3EEFE', '#7C3AED'],
                                ['F', 'Platform Engineer', 'Cover letter rewritten', 'Sent', '#E7F8F1', '#0E9F6E'],
                            ];
                        @endphp
                        @foreach($jobData as $i => $j)
                            <div class="job">
                                <span class="job__lg" style="background:{{ $jobColors[$i] }}">{{ $j[0] }}</span>
                                <div class="job__t"><b>{{ $j[1] }}</b><span>{{ $j[2] }}</span></div>
                                <span class="job__tag" style="background:{{ $j[4] }};color:{{ $j[5] }}">{{ $j[3] }}</span>
                            </div>
                        @endforeach
                        <div style="margin-top:16px;display:flex;align-items:center;gap:10px;font-size:13px;color:var(--ink-3);font-weight:600">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            {{ $p['hero']['panel']['footnote'] }}
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </section>

    {{-- ════ LOGOS MARQUEE ═════════════════════════════════════════════ --}}
    <section class="logos">
        <div class="wrap"><p class="logos__label rv">{{ $p['logos']['label'] }}</p></div>
        <div class="marquee rv" data-d="1">
            <div class="marquee__track">
                @foreach($p['logos']['items'] as $logo)<span>{{ $logo }}</span>@endforeach
                @foreach($p['logos']['items'] as $logo)<span>{{ $logo }}</span>@endforeach
            </div>
        </div>
    </section>

    {{-- ════ STATEMENT ═════════════════════════════════════════════════ --}}
    <section class="stmt">
        <div class="wrap">
            <span class="eyebrow rv">{{ $p['statement']['kicker'] }}</span>
            <h2 class="rv" data-d="1">{{ $p['statement']['lead'] }} <span class="grad-text">{{ $p['statement']['emph'] }}</span></h2>
            <p class="rv" data-d="2">{{ $p['statement']['sub'] }}</p>
            <div class="stmt__chips rv" data-d="3">
                @foreach($p['statement']['chips'] as $chip)<span>{{ $chip }}</span>@endforeach
            </div>
        </div>
    </section>

    {{-- ════ PILLARS ═══════════════════════════════════════════════════ --}}
    <section class="sec sec--mist">
        <div class="wrap">
            <div class="head rv">
                <span class="eyebrow">{{ $p['pillars']['eyebrow'] }}</span>
                <h2>{{ $p['pillars']['title'] }}</h2>
                <p class="lede">{{ $p['pillars']['subtitle'] }}</p>
            </div>

            <div style="margin-top:96px">
            @foreach($p['pillars']['items'] as $i => $pl)
                @php [$c, $soft, $grad] = $accent[$pl['accent']] ?? $accent['blue']; $flip = $i % 2 === 1; @endphp
                <div class="pillar {{ $flip ? 'flip' : '' }} rv">
                    <div class="pillar__copy">
                        <span class="pillar__tag" style="background:{{ $soft }};color:{{ $c }}">{{ $pl['kicker'] }}</span>
                        <h3>{{ $pl['title'] }}</h3>
                        <p>{{ $pl['desc'] }}</p>
                        <ul class="pillar__pts">
                            @foreach($pl['points'] as $pt)
                                <li><span class="ck" style="background:{{ $grad }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>{{ $pt }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ route($pl['cta']['route']) }}" class="btn btn-ghost" style="color:{{ $c }};padding-left:0">
                            {{ $pl['cta']['label'] }}
                            <svg class="arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    </div>
                    <div class="pillar__media">
                        <div class="viz" style="background:{{ $grad }}">
                            <div class="viz__deco">
                                <span class="ring" style="width:300px;height:300px;right:-80px;top:-80px"></span>
                                <span class="ring" style="width:180px;height:180px;left:-50px;bottom:-50px"></span>
                            </div>
                            <div class="viz__inner">
                                @if($pl['visual']==='agent')
                                    <div class="glass-card"><span class="av" style="background:{{ $grad }}">G</span><div><b>Senior Engineer</b><span>Matched to your goals</span></div><span class="tag">Strong fit</span></div>
                                    <div class="glass-card"><span class="av" style="background:{{ $grad }}">M</span><div><b>Product Engineer</b><span>Tailoring application…</span></div><span class="tag">Applying</span></div>
                                    <div class="glass-card"><span class="av" style="background:{{ $grad }}">F</span><div><b>Platform Engineer</b><span>Cover letter ready</span></div><span class="tag">Sent</span></div>
                                @elseif($pl['visual']==='interview')
                                    <div class="glass-card"><span class="av" style="background:{{ $grad }}">AI</span><div><b>Mock interview</b><span>“Tell me about a hard trade-off…”</span></div></div>
                                    <div class="viz-panel">
                                        <div class="row"><span>Structure</span><span>Excellent</span></div>
                                        <div class="bar" style="background:rgba(0,0,0,.07)"><i style="width:90%;background:{{ $grad }}"></i></div>
                                        <div class="row" style="margin:16px 0 10px"><span>Clarity</span><span>Strong</span></div>
                                        <div class="bar" style="background:rgba(0,0,0,.07)"><i style="width:78%;background:{{ $grad }}"></i></div>
                                    </div>
                                @else
                                    <div class="viz-panel">
                                        <div class="row" style="margin-bottom:14px"><span>Market range for this role</span></div>
                                        <div class="bar" style="height:14px;background:rgba(0,0,0,.07)"><i style="width:74%;background:{{ $grad }}"></i></div>
                                        <div class="row" style="margin-top:10px;font-size:12px;color:var(--ink-3)"><span>Below market</span><span>Top of band</span></div>
                                    </div>
                                    <div class="glass-card"><span class="av" style="background:{{ $grad }}">“</span><div><b>Your script is ready</b><span>Word-for-word, for every moment</span></div></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </section>

    {{-- ════ MODULES ═══════════════════════════════════════════════════ --}}
    <section class="sec">
        <div class="wrap">
            <div class="head rv">
                <span class="eyebrow">{{ $p['modules']['eyebrow'] }}</span>
                <h2>{{ $p['modules']['title'] }}</h2>
                <p class="lede">{{ $p['modules']['subtitle'] }}</p>
            </div>
            <div class="mods">
                @foreach($p['modules']['items'] as $i => $m)
                    @php [$c, $soft, $grad] = $accent[$m['tone']] ?? $accent['blue']; @endphp
                    <div class="mod rv" data-d="{{ ($i % 3) + 1 }}">
                        <div class="mod__ic" style="background:{{ $grad }}">
                            @switch($m['icon'])
                                @case('bolt')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h7l-1 8 10-12h-7l1-8Z"/></svg>@break
                                @case('search')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>@break
                                @case('doc')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/></svg>@break
                                @case('mic')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="2" width="6" height="12" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 19v3"/></svg>@break
                                @case('chart')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m7 14 4-4 3 3 5-6"/></svg>@break
                                @default<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8.5 8 11 4.5-2.5 8-6 8-11V5z"/></svg>
                            @endswitch
                        </div>
                        <h3>{{ $m['title'] }}</h3>
                        <p>{{ $m['desc'] }}</p>
                        <span class="mod__arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════ HOW IT WORKS ══════════════════════════════════════════════ --}}
    <section class="sec sec--mist">
        <div class="wrap">
            <div class="head rv">
                <span class="eyebrow">{{ $p['journey']['eyebrow'] }}</span>
                <h2>{{ $p['journey']['title'] }}</h2>
                <p class="lede">{{ $p['journey']['subtitle'] }}</p>
            </div>
            <div class="steps">
                @foreach($p['journey']['steps'] as $i => $s)
                    <div class="stp rv" data-d="{{ ($i % 4) + 1 }}">
                        <div class="stp__n">{{ str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) }}</div>
                        <h3>{{ $s['title'] }}</h3>
                        <p>{{ $s['desc'] }}</p>
                        <span class="stp__meta">{{ $s['meta'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════ A DAY ON AUTOPILOT ════════════════════════════════════════ --}}
    <section class="sec">
        <div class="wrap">
            <div class="head rv">
                <span class="eyebrow">{{ $p['experience']['eyebrow'] }}</span>
                <h2>{{ $p['experience']['title'] }}</h2>
                <p class="lede">{{ $p['experience']['subtitle'] }}</p>
            </div>
            <div class="tl">
                @foreach($p['experience']['timeline'] as $i => $t)
                    <div class="tl__i rv" data-d="{{ ($i % 4) + 1 }}">
                        <div class="tl__node"><i></i></div>
                        <div>
                            <div class="tl__phase">{{ $t['phase'] }}</div>
                            <h3>{{ $t['title'] }}</h3>
                            <p>{{ $t['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════ COMPARISON ════════════════════════════════════════════════ --}}
    <section class="sec sec--mist">
        <div class="wrap">
            <div class="head rv">
                <span class="eyebrow">{{ $p['comparison']['eyebrow'] }}</span>
                <h2>{{ $p['comparison']['title'] }}</h2>
            </div>
            <div class="cmp">
                <div class="cmp__col cmp__old rv">
                    <span class="cmp__lab">{{ $p['comparison']['old']['label'] }}</span>
                    <ul>
                        @foreach($p['comparison']['old']['points'] as $pt)
                            <li><span class="cmp__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg></span>{{ $pt }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="cmp__col cmp__new rv" data-d="1">
                    <span class="cmp__lab">{{ $p['comparison']['new']['label'] }}</span>
                    <ul>
                        @foreach($p['comparison']['new']['points'] as $pt)
                            <li><span class="cmp__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>{{ $pt }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ════ TESTIMONIALS ══════════════════════════════════════════════ --}}
    <section class="sec">
        <div class="wrap">
            <div class="head rv">
                <span class="eyebrow">{{ $p['testimonials']['eyebrow'] }}</span>
                <h2>{{ $p['testimonials']['title'] }}</h2>
            </div>
            <div class="quotes">
                @foreach($p['testimonials']['items'] as $i => $t)
                    <figure class="quote rv" data-d="{{ ($i % 2) + 1 }}">
                        <div class="quote__m">“</div>
                        <p>{{ $t['quote'] }}</p>
                        <figcaption class="quote__w">
                            <span class="quote__av">{{ $t['initials'] }}</span>
                            <div><b>{{ $t['name'] }}</b><span>{{ $t['role'] }}</span></div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════ FAQ ═══════════════════════════════════════════════════════ --}}
    <section class="sec sec--mist">
        <div class="wrap">
            <div class="head rv">
                <span class="eyebrow">{{ $p['faq']['eyebrow'] }}</span>
                <h2>{{ $p['faq']['title'] }}</h2>
            </div>
            <div class="faq rv">
                @foreach($p['faq']['items'] as $i => $f)
                    <details class="q" {{ $i === 0 ? 'open' : '' }}>
                        <summary>{{ $f['q'] }}<span class="qm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></span></summary>
                        <div class="ans">{{ $f['a'] }}</div>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════ CTA ═══════════════════════════════════════════════════════ --}}
    <section class="cta">
        <div class="wrap">
            <div class="cta__box rv">
                <span class="glow g1"></span><span class="glow g2"></span>
                <h2>{{ $p['cta']['title'] }}<span>{{ $p['cta']['highlight'] }}</span></h2>
                <p>{{ $p['cta']['subtitle'] }}</p>
                <div class="cta__a">
                    <a href="{{ route($p['cta']['primary']['route']) }}" class="btn btn-primary">
                        {{ $p['cta']['primary']['label'] }}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="{{ route($p['cta']['secondary']['route']) }}" class="btn btn-ghost">{{ $p['cta']['secondary']['label'] }}</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ════ FOOTER ════════════════════════════════════════════════════ --}}
    <footer class="ft">
        <div class="wrap">
            <div class="ft__grid">
                <div class="ft__brand">
                    <a href="{{ route('home') }}" class="brand">
                        <img src="{{ $p['brand']['logo'] }}" alt="{{ $p['brand']['name'] }}">
                        <span>{{ $p['brand']['wordmark'][0] }}<b>{{ $p['brand']['wordmark'][1] }}</b>{{ $p['brand']['wordmark'][2] }}</span>
                    </a>
                    <p>{{ $p['footer']['tagline'] }}</p>
                </div>
                @foreach($p['footer']['columns'] as $col)
                    <div class="ft__col">
                        <h4>{{ $col['heading'] }}</h4>
                        @foreach($col['links'] as $l)<a href="{{ route($l['route']) }}">{{ $l['label'] }}</a>@endforeach
                    </div>
                @endforeach
            </div>
            <div class="ft__base">
                <span>{{ $p['footer']['legal'] }}</span>
                <span>{{ $p['brand']['tagline'] }}</span>
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
</body>
</html>
