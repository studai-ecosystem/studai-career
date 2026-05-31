@props([
    'title' => null,
    'heading' => null,    // optional page heading rendered above slot
])

@php
    $user = auth()->user();
    $isEmployer = $user?->isEmployer();

    // ── Navigation model (role-aware). Routes mirror the legacy layout. ──────
    $candidateNav = [
        'Main' => [
            ['route' => 'dashboard',           'pattern' => 'dashboard',       'label' => 'Dashboard',   'icon' => 'grid'],
            ['route' => 'jobs.search',          'pattern' => 'jobs.*',          'label' => 'Job Search',  'icon' => 'search'],
            ['route' => 'agent.dashboard',      'pattern' => 'agent.*',         'label' => 'AI Agent',    'icon' => 'bolt',  'badge' => 3],
        ],
        'Career Tools' => [
            ['route' => 'resume.index',         'pattern' => 'resume.*',        'label' => 'Resume Builder', 'icon' => 'doc'],
            ['route' => 'interview.index',      'pattern' => 'interview.*',     'label' => 'Interview Lab',  'icon' => 'mic'],
            ['route' => 'career-coach.index',   'pattern' => 'career-coach.*',  'label' => 'Career Coach',   'icon' => 'compass'],
            ['route' => 'negotiation.dashboard','pattern' => 'negotiation.*',   'label' => 'Negotiation',    'icon' => 'chat'],
        ],
        'More' => [
            ['route' => 'marketplace.index',     'pattern' => 'marketplace.*',   'label' => 'Marketplace',   'icon' => 'bag'],
            ['route' => 'gamification.dashboard','pattern' => 'gamification.*',  'label' => 'Achievements',  'icon' => 'trophy'],
            ['route' => 'profile.edit',          'pattern' => 'profile.*',       'label' => 'Settings',      'icon' => 'gear'],
        ],
    ];

    $employerNav = [
        'Main' => [
            ['route' => 'employer.dashboard',        'pattern' => 'employer.dashboard',   'label' => 'Dashboard',  'icon' => 'grid'],
            ['route' => 'employer.jobs.index',       'pattern' => 'employer.jobs.*',      'label' => 'Job Posts',  'icon' => 'briefcase', 'badge' => 7],
            ['route' => 'employer.applicants.index', 'pattern' => 'employer.applicants.*','label' => 'Applicants', 'icon' => 'users'],
        ],
        'S.C.O.U.T. AI' => [
            ['route' => 'employer.scout.dashboard',          'pattern' => 'employer.scout.dashboard',         'label' => 'Scout Overview', 'icon' => 'target'],
            ['route' => 'employer.scout.shortlisting',       'pattern' => 'employer.scout.shortlisting',      'label' => 'Shortlisting',   'icon' => 'checklist'],
            ['route' => 'employer.scout.candidate-matching', 'pattern' => 'employer.scout.candidate-matching','label' => 'Matching',       'icon' => 'sparkles'],
            ['route' => 'employer.scout.predictive',         'pattern' => 'employer.scout.predictive',        'label' => 'Predictive',     'icon' => 'chart'],
        ],
        'More' => [
            ['route' => 'employer.interviews.index', 'pattern' => 'employer.interviews.*','label' => 'Interviews',      'icon' => 'calendar'],
            ['route' => 'employer.profile.show',     'pattern' => 'employer.profile.*',   'label' => 'Company Profile', 'icon' => 'building'],
            ['route' => 'employer.analytics',        'pattern' => 'employer.analytics',   'label' => 'Analytics',       'icon' => 'bars'],
        ],
    ];

    $nav = $isEmployer ? $employerNav : $candidateNav;

    $homeRoute = $isEmployer ? 'employer.dashboard' : 'dashboard';
    $pageTitle = $title ?? $heading ?? 'StudAI Hire';

    $userName    = $user->name ?? 'Guest';
    $userInitial = strtoupper(mb_substr($userName, 0, 1));
    $planLabel   = $isEmployer ? 'S.C.O.U.T. Pro' : ($user?->subscription_plan ?? 'Free plan');
    $botName     = $isEmployer ? 'S.C.O.U.T. Copilot' : 'Career Copilot';
    $newLabel    = $isEmployer ? 'Post a job' : 'Find jobs';
    $newRoute    = $isEmployer
        ? (\Illuminate\Support\Facades\Route::has('employer.jobs.create') ? route('employer.jobs.create') : (\Illuminate\Support\Facades\Route::has('employer.jobs.index') ? route('employer.jobs.index') : '#'))
        : (\Illuminate\Support\Facades\Route::has('jobs.search') ? route('jobs.search') : '#');

    // Inline outline-icon set (24×24 stroke). Keeps the sidebar lightweight.
    $icons = [
        'grid'      => '<rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/>',
        'search'    => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3" stroke-linecap="round"/>',
        'bolt'      => '<path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z" stroke-linejoin="round"/>',
        'doc'       => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z" stroke-linejoin="round"/><path d="M14 3v5h5M9 13h6M9 17h5" stroke-linecap="round"/>',
        'mic'       => '<rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3" stroke-linecap="round"/>',
        'compass'   => '<circle cx="12" cy="12" r="9"/><path d="M16 8l-2.5 5.5L8 16l2.5-5.5L16 8z" stroke-linejoin="round"/>',
        'chat'      => '<path d="M21 11.5a8 8 0 0 1-11.5 7.2L4 20l1.3-4.6A8 8 0 1 1 21 11.5z" stroke-linejoin="round"/>',
        'bag'       => '<path d="M6 7h12l-1 13H7L6 7z" stroke-linejoin="round"/><path d="M9 7a3 3 0 0 1 6 0" stroke-linecap="round"/>',
        'trophy'    => '<path d="M8 4h8v5a4 4 0 0 1-8 0V4z" stroke-linejoin="round"/><path d="M8 6H5.5a2 2 0 0 0 2 4M16 6h2.5a2 2 0 0 1-2 4M9.5 20h5M12 13v4" stroke-linecap="round"/>',
        'gear'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 13a7.6 7.6 0 0 0 0-2l1.8-1.3-2-3.4-2.1 1a7.6 7.6 0 0 0-1.7-1l-.3-2.3h-4l-.3 2.3a7.6 7.6 0 0 0-1.7 1l-2.1-1-2 3.4L4.6 11a7.6 7.6 0 0 0 0 2l-1.8 1.3 2 3.4 2.1-1a7.6 7.6 0 0 0 1.7 1l.3 2.3h4l.3-2.3a7.6 7.6 0 0 0 1.7-1l2.1 1 2-3.4z" stroke-linejoin="round"/>',
        'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5.5A2 2 0 0 1 10 3.5h4a2 2 0 0 1 2 2V7M3 12.5h18" stroke-linecap="round"/>',
        'users'     => '<circle cx="9" cy="8" r="3"/><path d="M3.5 20a5.5 5.5 0 0 1 11 0M16 5.5a3 3 0 0 1 0 6M20.5 20a5.5 5.5 0 0 0-3.5-5.1" stroke-linecap="round"/>',
        'target'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5" fill="currentColor" stroke="none"/>',
        'checklist' => '<path d="M10 6h10M10 12h10M10 18h10" stroke-linecap="round"/><path d="M4 5.5l1.2 1.2L7.5 4.3M4 11.5l1.2 1.2 2.3-2.4M4 17.5l1.2 1.2 2.3-2.4" stroke-linecap="round" stroke-linejoin="round"/>',
        'sparkles'  => '<path d="M12 3l1.6 4.4L18 9l-4.4 1.6L12 15l-1.6-4.4L6 9l4.4-1.6L12 3z" stroke-linejoin="round"/><path d="M18 14l.8 2 2 .8-2 .8-.8 2-.8-2-2-.8 2-.8.8-2z" stroke-linejoin="round"/>',
        'chart'     => '<path d="M4 20V4M4 20h16" stroke-linecap="round"/><path d="M8 16v-5M12 16V8M16 16v-8" stroke-linecap="round"/>',
        'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 9.5h18M8 3v4M16 3v4" stroke-linecap="round"/>',
        'building'  => '<rect x="4" y="3" width="16" height="18" rx="1.6"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2M10 21v-3h4v3" stroke-linecap="round"/>',
        'bars'      => '<path d="M4 20V4M4 20h16" stroke-linecap="round"/><path d="M8 16v-6M12 16v-9M16 16v-4" stroke-linecap="round"/>',
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} · {{ config('app.name', 'StudAI Hire') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    @stack('styles')
    <style>
        [x-cloak]{display:none !important}

        /* ════════ APP SHELL — Google / Zoho clean SaaS (token-driven) ════════ */
        .ap{
            --ap-pri:var(--color-accent);
            --ap-pri-d:var(--color-accent-hover);
            --ap-pri-dd:var(--color-accent-text);
            --ap-tint:var(--color-accent-subtle);
            --ap-muted:var(--color-accent-muted);
            --ap-bg:var(--color-canvas);
            --ap-card:var(--color-surface);
            --ap-card-2:var(--color-surface-raised);
            --ap-hover:var(--color-surface-raised);
            --ap-line:var(--color-border);
            --ap-line-2:var(--color-border-strong);
            --ap-txt:var(--color-ink-1);
            --ap-txt-2:var(--color-ink-2);
            --ap-txt-3:var(--color-ink-3);
            --ap-amber:var(--color-warning);
            --ap-green:var(--color-success);
            --ap-r:14px;--ap-r-sm:10px;--ap-r-lg:20px;--ap-pill:999px;
            --ap-sh-1:0 1px 2px rgba(60,64,67,.10),0 1px 3px rgba(60,64,67,.06);
            --ap-sh-2:0 1px 3px rgba(60,64,67,.12),0 4px 8px rgba(60,64,67,.06);
            --ap-sh-3:0 4px 10px rgba(60,64,67,.14),0 12px 28px rgba(60,64,67,.10);
        }

        /* sidebar */
        .ap-side{position:fixed;top:0;bottom:0;left:0;width:var(--sidebar-width);z-index:40;
            display:flex;flex-direction:column;background:var(--ap-card);border-right:1px solid var(--ap-line);
            padding:16px 14px 14px;transition:transform .2s ease}
        .ap-brand{display:flex;align-items:center;gap:11px;padding:4px 8px 16px;text-decoration:none}
        .ap-mark{width:40px;height:40px;border-radius:11px;background:var(--ap-card);border:1px solid var(--ap-line);
            box-shadow:var(--ap-sh-1);display:grid;place-items:center;flex:0 0 40px;overflow:hidden}
        .ap-mark svg{width:18px;height:28px;display:block}
        .ap-wm{display:inline-flex;align-items:flex-end;gap:1px;font-weight:800;font-size:23px;
            letter-spacing:-.04em;color:var(--ap-txt);line-height:1}
        .ap-wm .tie{width:11px;height:30px;margin:0 .5px -1px;display:block}
        .ap-sub{font-size:10.5px;color:var(--ap-txt-3);font-weight:600;letter-spacing:.02em;margin-top:3px;display:block}

        .ap-nav{flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:2px;padding:0 2px}
        .ap-nav::-webkit-scrollbar{width:0}
        .ap-lbl{font-size:10.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
            color:var(--ap-txt-3);padding:14px 12px 6px;margin:0}
        .ap-item{display:flex;align-items:center;gap:12px;padding:9px 12px;border-radius:var(--ap-r-sm);
            color:var(--ap-txt-2);font-weight:500;font-size:13.5px;text-decoration:none;position:relative;transition:.14s}
        .ap-item svg{width:18px;height:18px;flex:0 0 18px;stroke-width:1.9}
        .ap-item:hover{background:var(--ap-hover);color:var(--ap-txt)}
        .ap-item.on{background:var(--ap-tint);color:var(--ap-pri-dd);font-weight:600}
        .ap-item.on svg{color:var(--ap-pri)}
        .ap-item .badge{margin-left:auto;font-size:10.5px;font-weight:700;background:var(--ap-pri);color:#fff;
            min-width:18px;height:18px;padding:0 5px;border-radius:9px;display:grid;place-items:center}

        .ap-promo{margin:10px 4px 8px;background:var(--ap-tint);border:1px solid var(--ap-muted);border-radius:var(--ap-r);padding:14px}
        .ap-promo h5{font-size:12.5px;font-weight:700;color:var(--ap-pri-dd);display:flex;align-items:center;gap:7px;margin:0}
        .ap-promo p{font-size:11.5px;color:var(--ap-txt-2);margin:5px 0 10px;line-height:1.45}
        .ap-promo a{display:block;text-align:center;text-decoration:none;background:var(--ap-pri);color:#fff;font-weight:600;
            font-size:12px;padding:8px;border-radius:var(--ap-r-sm);transition:.16s}
        .ap-promo a:hover{background:var(--ap-pri-d)}

        .ap-user{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:var(--ap-r-sm);
            cursor:pointer;border:1px solid var(--ap-line);background:var(--ap-card);width:100%;text-align:left}
        .ap-user:hover{background:var(--ap-hover)}
        .ap-user .av{width:32px;height:32px;border-radius:50%;flex:0 0 32px;display:grid;place-items:center;
            font-weight:700;font-size:12.5px;color:#fff;background:var(--ap-pri)}
        .ap-user .nm{font-size:12.5px;font-weight:600;line-height:1.25;color:var(--ap-txt);display:block}
        .ap-user .pl{font-size:11px;color:var(--ap-txt-3);display:block}
        .ap-user .chev{margin-left:auto;color:var(--ap-txt-3)}
        .ap-umenu{position:absolute;bottom:64px;left:14px;right:14px;background:var(--ap-card);
            border:1px solid var(--ap-line);border-radius:var(--ap-r-sm);box-shadow:var(--ap-sh-3);padding:6px;z-index:50}
        .ap-umenu a,.ap-umenu button{display:block;width:100%;text-align:left;padding:9px 11px;border-radius:8px;
            font-size:13px;color:var(--ap-txt-2);background:transparent;border:0;cursor:pointer;text-decoration:none}
        .ap-umenu a:hover,.ap-umenu button:hover{background:var(--ap-hover);color:var(--ap-txt)}

        /* main + topbar */
        .ap-main{margin-left:var(--sidebar-width);min-height:100vh;background:var(--ap-bg);transition:margin .2s ease}
        .ap-top{position:sticky;top:0;z-index:30;display:flex;align-items:center;gap:18px;
            padding:12px 24px;background:color-mix(in srgb,var(--ap-bg) 86%,transparent);
            backdrop-filter:blur(10px);border-bottom:1px solid var(--ap-line)}
        .ap-burger{display:none;width:38px;height:38px;border-radius:9px;border:1px solid transparent;background:transparent;
            cursor:pointer;color:var(--ap-txt-2);place-items:center}
        .ap-burger:hover{background:var(--ap-hover);color:var(--ap-txt)}
        .ap-crumb{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ap-txt-3);font-weight:500;min-width:0}
        .ap-crumb b{color:var(--ap-txt);font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .ap-crumb svg{width:14px;height:14px;flex:0 0 14px}
        .ap-search{flex:1;max-width:520px;margin:0 auto 0 8px;position:relative}
        .ap-search input{width:100%;border:1px solid var(--ap-line-2);background:var(--ap-card);border-radius:var(--ap-pill);
            padding:10px 16px 10px 42px;font-family:inherit;font-size:13.5px;color:var(--ap-txt);transition:.16s;box-shadow:var(--ap-sh-1)}
        .ap-search input::placeholder{color:var(--ap-txt-3)}
        .ap-search input:focus{outline:none;border-color:var(--ap-pri);box-shadow:0 0 0 3px var(--ap-tint)}
        .ap-search svg{position:absolute;left:15px;top:50%;transform:translateY(-50%);width:17px;height:17px;color:var(--ap-txt-3)}
        .ap-search kbd{position:absolute;right:12px;top:50%;transform:translateY(-50%);font-family:var(--font-mono);
            font-size:10.5px;color:var(--ap-txt-3);background:var(--ap-card-2);border:1px solid var(--ap-line);border-radius:6px;padding:2px 6px}
        .ap-tools{display:flex;align-items:center;gap:8px;margin-left:auto}
        .ap-ic{width:38px;height:38px;border-radius:50%;border:1px solid transparent;background:transparent;
            display:grid;place-items:center;cursor:pointer;color:var(--ap-txt-2);position:relative;transition:.14s;text-decoration:none}
        .ap-ic:hover{background:var(--ap-hover);color:var(--ap-txt)}
        .ap-ic svg{width:19px;height:19px}
        .ap-ic .ping{position:absolute;top:8px;right:9px;width:7px;height:7px;border-radius:50%;background:var(--color-error);border:2px solid var(--ap-bg)}
        .ap-new{display:inline-flex;align-items:center;gap:8px;background:var(--ap-pri);color:#fff;border:0;font-family:inherit;
            font-weight:600;font-size:13px;padding:9px 16px;border-radius:var(--ap-pill);cursor:pointer;text-decoration:none;
            box-shadow:0 1px 2px rgba(45,108,223,.4);transition:.16s}
        .ap-new:hover{background:var(--ap-pri-d);box-shadow:0 2px 6px rgba(45,108,223,.45)}
        .ap-new svg{width:17px;height:17px}

        .ap-content{padding:24px 24px 120px;max-width:var(--content-max-width);width:100%;margin:0 auto}
        .ap-pageheading{font-size:24px;font-weight:800;letter-spacing:-.02em;color:var(--ap-txt);margin:0 0 18px}

        /* scrim */
        .ap-scrim{position:fixed;inset:0;z-index:35;background:rgba(15,27,51,.42);backdrop-filter:blur(1px)}

        /* ════════ AI assistant ════════ */
        .ap-fab{position:fixed;right:26px;bottom:26px;z-index:80;width:58px;height:58px;border-radius:50%;
            background:var(--ap-pri);border:0;cursor:pointer;display:grid;place-items:center;color:#fff;
            box-shadow:0 6px 18px rgba(45,108,223,.5);transition:.2s}
        .ap-fab:hover{background:var(--ap-pri-d);transform:scale(1.05)}
        .ap-fab svg{width:25px;height:25px}
        .ap-fab .nub{position:absolute;top:-4px;right:-4px;background:var(--ap-amber);color:#fff;font-size:10px;font-weight:800;
            width:20px;height:20px;border-radius:50%;display:grid;place-items:center;border:2px solid var(--ap-bg)}
        .ap-chat{position:fixed;right:26px;bottom:26px;z-index:90;width:392px;max-width:calc(100vw - 36px);
            height:600px;max-height:calc(100vh - 60px);background:var(--ap-card);border:1px solid var(--ap-line-2);
            border-radius:var(--ap-r-lg);box-shadow:var(--ap-sh-3);display:flex;flex-direction:column;overflow:hidden;
            transform:translateY(16px) scale(.97);opacity:0;pointer-events:none;transform-origin:bottom right;transition:.2s}
        .ap-chat.open{transform:none;opacity:1;pointer-events:auto}
        .ap-ch{display:flex;align-items:center;gap:12px;padding:16px 18px;border-bottom:1px solid var(--ap-line);background:var(--ap-tint)}
        .ap-ch .bot{width:38px;height:38px;border-radius:11px;background:var(--ap-pri);display:grid;place-items:center;color:#fff;flex:0 0 38px}
        .ap-ch .bot svg{width:20px;height:20px}
        .ap-ch .who b{font-size:14px;font-weight:700;display:block;color:var(--ap-txt)}
        .ap-ch .who span{font-size:11.5px;color:var(--ap-green);font-weight:600;display:flex;align-items:center;gap:5px}
        .ap-ch .who span i{width:7px;height:7px;border-radius:50%;background:var(--ap-green)}
        .ap-ch .x{margin-left:auto;width:32px;height:32px;border-radius:8px;border:0;background:transparent;cursor:pointer;
            color:var(--ap-txt-2);display:grid;place-items:center}
        .ap-ch .x:hover{background:rgba(0,0,0,.05)}
        .ap-thread{flex:1;overflow-y:auto;padding:18px;display:flex;flex-direction:column;gap:14px;background:var(--ap-card-2)}
        .ap-msg{display:flex;gap:9px;max-width:86%}
        .ap-msg .ma{width:28px;height:28px;border-radius:8px;flex:0 0 28px;display:grid;place-items:center;font-size:11px;font-weight:700}
        .ap-msg.bot .ma{background:var(--ap-pri);color:#fff}
        .ap-msg.me{align-self:flex-end;flex-direction:row-reverse}
        .ap-msg.me .ma{background:var(--ap-txt);color:#fff}
        .ap-bub{padding:11px 14px;border-radius:14px;font-size:13px;line-height:1.5;box-shadow:var(--ap-sh-1)}
        .ap-msg.bot .ap-bub{background:var(--ap-card);border:1px solid var(--ap-line);border-top-left-radius:5px;color:var(--ap-txt)}
        .ap-msg.me .ap-bub{background:var(--ap-pri);color:#fff;border-top-right-radius:5px}
        .ap-bub b{font-weight:700}
        .ap-chips{display:flex;gap:8px;flex-wrap:wrap;padding:0 18px 12px;background:var(--ap-card-2)}
        .ap-chip{font-size:12px;font-weight:600;color:var(--ap-pri-dd);background:var(--ap-card);border:1px solid var(--ap-muted);
            border-radius:var(--ap-pill);padding:7px 13px;cursor:pointer;transition:.14s}
        .ap-chip:hover{background:var(--ap-tint)}
        .ap-cin{display:flex;align-items:center;gap:9px;padding:13px 14px;border-top:1px solid var(--ap-line);background:var(--ap-card)}
        .ap-cin input{flex:1;border:1px solid var(--ap-line-2);border-radius:var(--ap-pill);padding:11px 16px;font-family:inherit;font-size:13px;color:var(--ap-txt);background:var(--ap-card)}
        .ap-cin input:focus{outline:none;border-color:var(--ap-pri);box-shadow:0 0 0 3px var(--ap-tint)}
        .ap-cin .send{width:40px;height:40px;border-radius:50%;border:0;background:var(--ap-pri);color:#fff;cursor:pointer;display:grid;place-items:center;flex:0 0 40px;transition:.14s}
        .ap-cin .send:hover{background:var(--ap-pri-d)}
        .ap-cin .send svg{width:18px;height:18px}
        .ap-typing{display:flex;gap:4px;align-items:center;padding:11px 14px;background:var(--ap-card);border:1px solid var(--ap-line);border-radius:14px;border-top-left-radius:5px;width:fit-content}
        .ap-typing i{width:7px;height:7px;border-radius:50%;background:var(--ap-txt-3);animation:ap-bounce 1.2s infinite}
        .ap-typing i:nth-child(2){animation-delay:.15s}.ap-typing i:nth-child(3){animation-delay:.3s}
        @keyframes ap-bounce{0%,60%,100%{transform:translateY(0);opacity:.5}30%{transform:translateY(-5px);opacity:1}}

        /* responsive */
        @media(max-width:1024px){
            .ap-side{transform:translateX(-100%)}
            .ap-side.open{transform:translateX(0);box-shadow:var(--ap-sh-3)}
            .ap-main{margin-left:0}
            .ap-burger{display:grid}
        }
        @media(max-width:680px){
            .ap-search{display:none}
            .ap-content{padding:18px 14px 110px}
            .ap-new span{display:none}
        }
    </style>
</head>
<body class="bg-canvas text-ink-1">
<div class="ap" x-data="{ mobileNav:false, userMenu:false, chatOpen:false }">

    {{-- ════════════ SIDEBAR ════════════ --}}
    <aside class="ap-side" :class="{ open: mobileNav }">
        {{-- Brand: tie logo + wordmark --}}
        <a href="{{ route($homeRoute) }}" class="ap-brand">
            <span class="ap-mark">
                <svg viewBox="0 0 24 60" fill="none" aria-hidden="true">
                    <path d="M5 3 L19 3 L15 13.5 L9 13.5 Z" fill="#F4C20D"/>
                    <path d="M9 13.5 L15 13.5 L18 27.5 L12 56.5 L6 27.5 Z" fill="#2D6CDF"/>
                    <path d="M12 16.5 L12 52.5" stroke="#fff" stroke-width="1.4" opacity=".30"/>
                </svg>
            </span>
            <span>
                <span class="ap-wm">h<svg class="tie" viewBox="0 0 24 60" fill="none" aria-hidden="true"><path d="M5 3 L19 3 L15 13.5 L9 13.5 Z" fill="#F4C20D"/><path d="M9 13.5 L15 13.5 L18 27.5 L12 56.5 L6 27.5 Z" fill="#2D6CDF"/><path d="M12 16.5 L12 52.5" stroke="#fff" stroke-width="1.4" opacity=".30"/></svg>re</span>
                <span class="ap-sub">by StudAI One</span>
            </span>
        </a>

        {{-- Nav --}}
        <nav class="ap-nav">
            @foreach ($nav as $section => $items)
                <p class="ap-lbl">{{ $section }}</p>
                @foreach ($items as $item)
                    @php $active = request()->routeIs($item['pattern']); @endphp
                    <a
                        href="{{ \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '#' }}"
                        class="ap-item {{ $active ? 'on' : '' }}"
                        @if ($active) aria-current="page" @endif
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">{!! $icons[$item['icon']] ?? $icons['grid'] !!}</svg>
                        <span>{{ $item['label'] }}</span>
                        @if (! empty($item['badge']))
                            <span class="badge">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            @endforeach
        </nav>

        {{-- Upgrade (candidates only) --}}
        @if (! $isEmployer && \Illuminate\Support\Facades\Route::has('subscriptions.pricing'))
            <div class="ap-promo">
                <h5>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z" stroke-linejoin="round"/></svg>
                    Upgrade to Pro
                </h5>
                <p>Unlock the autonomous agent, unlimited AI credits &amp; priority matching.</p>
                <a href="{{ route('subscriptions.pricing') }}">Go Pro · ₹499/mo</a>
            </div>
        @endif

        {{-- User card + menu --}}
        @auth
            <div style="position:relative">
                <div x-show="userMenu" x-cloak @click.outside="userMenu=false" class="ap-umenu">
                    @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
                        <a href="{{ route('profile.edit') }}">Profile &amp; settings</a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('payments.index'))
                        <a href="{{ route('payments.index') }}">Billing</a>
                    @endif
                    <button type="button" @click="$store?.theme?.toggle?.()">Toggle theme</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Sign out</button>
                    </form>
                </div>
                <button type="button" class="ap-user" @click="userMenu=!userMenu">
                    <span class="av">{{ $userInitial }}</span>
                    <span>
                        <span class="nm">{{ \Illuminate\Support\Str::limit($userName, 16) }}</span>
                        <span class="pl">{{ $planLabel }}</span>
                    </span>
                    <svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 9l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endauth
    </aside>

    {{-- ════════════ MAIN ════════════ --}}
    <div class="ap-main">

        {{-- Top bar --}}
        <header class="ap-top">
            <button type="button" class="ap-burger" @click="mobileNav=!mobileNav" aria-label="Toggle navigation">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 5h14M3 10h14M3 15h14" stroke-linecap="round"/></svg>
            </button>

            <div class="ap-crumb">
                <span>{{ $isEmployer ? 'Employer' : 'Workspace' }}</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <b>{{ $heading ?? $pageTitle }}</b>
            </div>

            <div class="ap-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3" stroke-linecap="round"/></svg>
                <input id="ap-srch" type="search" placeholder="{{ $isEmployer ? 'Search candidates, jobs…' : 'Search jobs, companies, skills…' }}" aria-label="Search">
                <kbd>⌘K</kbd>
            </div>

            <div class="ap-tools">
                @isset($actions)
                    <div class="flex items-center gap-2">{{ $actions }}</div>
                @endisset

                <a href="{{ $newRoute }}" class="ap-new">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                    <span>{{ $newLabel }}</span>
                </a>

                @if (\Illuminate\Support\Facades\Route::has('notifications.all'))
                    <a href="{{ route('notifications.all') }}" class="ap-ic" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="ping"></span>
                    </a>
                @endif

                <button type="button" class="ap-ic" @click="chatOpen=true" aria-label="Help and AI assistant">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 0 1 4.5 1.5c0 1.7-2.5 1.8-2.5 3.5M12 17.5h.01" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </header>

        {{-- Page content --}}
        <main class="ap-content">
            @if ($heading)
                <h1 class="ap-pageheading">{{ $heading }}</h1>
            @endif
            {{ $slot }}
        </main>
    </div>

    {{-- Mobile scrim --}}
    <div x-show="mobileNav" x-cloak @click="mobileNav=false" class="ap-scrim" style="display:none"></div>

    {{-- ════════════ AI ASSISTANT ════════════ --}}
    <button type="button" class="ap-fab" x-show="!chatOpen" @click="chatOpen=true" aria-label="Open AI assistant">
        <span class="nub">1</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M21 11.5a8 8 0 0 1-11.5 7.2L4 20l1.3-4.6A8 8 0 1 1 21 11.5z" stroke-linejoin="round"/><circle cx="9" cy="11.5" r="1" fill="currentColor" stroke="none"/><circle cx="12.5" cy="11.5" r="1" fill="currentColor" stroke="none"/><circle cx="16" cy="11.5" r="1" fill="currentColor" stroke="none"/></svg>
    </button>

    <div class="ap-chat" :class="{ open: chatOpen }" x-cloak>
        <div class="ap-ch">
            <span class="bot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="7" width="16" height="12" rx="3"/><path d="M12 7V4M8 13h.01M16 13h.01M9 16.5h6" stroke-linecap="round"/></svg></span>
            <div class="who"><b>{{ $botName }}</b><span><i></i> Online</span></div>
            <button type="button" class="x" @click="chatOpen=false" aria-label="Close"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg></button>
        </div>
        <div class="ap-thread" id="ap-thread"></div>
        <div class="ap-chips" id="ap-chips"></div>
        <form class="ap-cin" onsubmit="apSend();return false;">
            <input id="ap-cinput" type="text" placeholder="Ask {{ $botName }}…" autocomplete="off">
            <button type="submit" class="send" aria-label="Send"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
        </form>
    </div>
</div>

{{-- Single global toast system --}}
<x-ui.toast-container />

@livewireScripts
@stack('scripts')

<script>
(function(){
    const isEmployer = @json((bool) $isEmployer);
    const botName = @json($botName);
    const userInitial = @json($userInitial);
    const thread = document.getElementById('ap-thread');
    const chipsBox = document.getElementById('ap-chips');
    const input = document.getElementById('ap-cinput');
    if (!thread) return;

    const chips = isEmployer
        ? ['Best-fit candidates today', 'Summarise the pipeline', 'Draft an interview invite', 'Who should I shortlist?']
        : ['What should I improve?', 'Find jobs that match me', 'Prep me for an interview', 'How are my applications?'];

    function esc(s){return String(s).replace(/[&<>]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]));}

    function bubble(who, html){
        const m = document.createElement('div');
        m.className = 'ap-msg ' + who;
        m.innerHTML = '<span class="ma">' + (who==='bot'?'AI':esc(userInitial)) + '</span><div class="ap-bub">' + html + '</div>';
        thread.appendChild(m);
        thread.scrollTop = thread.scrollHeight;
        return m;
    }

    function reply(q){
        q = q.toLowerCase();
        if (isEmployer){
            if (q.includes('shortlist')||q.includes('candidate')||q.includes('fit')) return 'I ranked your pipeline — <b>3 candidates</b> score above 90% on the must-have skills. Want me to move them to <b>Interview</b> and draft invites?';
            if (q.includes('pipeline')||q.includes('summar')) return 'Pipeline snapshot: <b>24 new</b>, 18 screening, 9 interviewing, 3 offers. Time-to-hire is trending <b>down 3 days</b> this week.';
            if (q.includes('invite')||q.includes('email')||q.includes('draft')) return 'Drafted a warm interview invite with two proposed slots and a calendar link. Review it in <b>Interviews</b> before sending.';
            return "I'm <b>" + esc(botName) + "</b> — I can shortlist candidates, summarise your pipeline, and draft outreach. What would you like to do?";
        }
        if (q.includes('improve')||q.includes('resume')||q.includes('profile')) return 'Two quick wins: add <b>measurable impact</b> to your top role, and 3 missing keywords from your target jobs. That lifts your match score by ~<b>8%</b>.';
        if (q.includes('job')||q.includes('match')||q.includes('find')) return 'I found <b>4 strong matches</b> today (92–94% fit). Want me to auto-apply to the top one with a tailored resume?';
        if (q.includes('interview')||q.includes('prep')) return "Let's run a mock round. I'll ask 5 role-specific questions and score your answers on clarity, structure & impact. Ready?";
        if (q.includes('application')||q.includes('status')) return 'You have <b>12 active applications</b> — 7 progressing to interview. One offer is awaiting your response.';
        return "I'm <b>" + esc(botName) + "</b>, your career copilot. I can improve your resume, find matches, and prep you for interviews. Ask me anything.";
    }

    function renderChips(){
        chipsBox.innerHTML = '';
        chips.forEach(c => {
            const b = document.createElement('button');
            b.type = 'button'; b.className = 'ap-chip'; b.textContent = c;
            b.onclick = () => ask(c);
            chipsBox.appendChild(b);
        });
    }

    function ask(q){
        bubble('me', esc(q));
        const t = document.createElement('div');
        t.className = 'ap-msg bot';
        t.innerHTML = '<span class="ma">AI</span><div class="ap-typing"><i></i><i></i><i></i></div>';
        thread.appendChild(t); thread.scrollTop = thread.scrollHeight;
        setTimeout(() => { t.remove(); bubble('bot', reply(q)); }, 950);
    }

    window.apSend = function(){
        const v = (input.value||'').trim();
        if (!v) return;
        input.value = '';
        ask(v);
    };

    // greeting
    bubble('bot', 'Hi! I\'m <b>' + esc(botName) + '</b>. ' + (isEmployer ? 'I can help you shortlist, screen and reach out to candidates.' : 'I can review your profile, find matching jobs and prep you for interviews.'));
    renderChips();

    // ⌘K / Ctrl+K focuses search
    document.addEventListener('keydown', e => {
        if ((e.metaKey||e.ctrlKey) && e.key.toLowerCase()==='k'){ e.preventDefault(); document.getElementById('ap-srch')?.focus(); }
    });
})();
</script>
</body>
</html>
