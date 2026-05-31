<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') — StudAI Hire</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --brand: #2D6CDF;
            --brand-dark: #1B57C4;
            --surface: #F7F7F5;
            --text: #0C0C0C;
            --text-muted: #737373;
        }
        body {
            font-family: 'DM Sans', system-ui, -apple-system, sans-serif;
            background: var(--surface);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .error-container {
            text-align: center;
            max-width: 480px;
        }
        .error-code {
            font-family: 'Instrument Serif', Georgia, serif;
            font-size: clamp(5rem, 15vw, 8rem);
            font-weight: 400;
            color: var(--brand);
            line-height: 1;
            margin-bottom: 0.25rem;
        }
        .error-heading {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .error-message {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .error-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--brand);
            color: #fff;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9375rem;
            transition: background 0.2s;
        }
        .error-action:hover { background: var(--brand-dark); }
        .error-action:active { transform: none; }
        .error-action svg { width: 1.125rem; height: 1.125rem; }
        .brand-link {
            display: block;
            margin-top: 3rem;
            color: var(--text-muted);
            font-size: 0.8125rem;
            text-decoration: none;
        }
        .brand-link span { color: var(--brand); font-weight: 600; }

        @media (prefers-color-scheme: dark) {
            :root {
                --surface: #0A0A0A;
                --text: #F2F2F0;
                --text-muted: #8F8F8C;
                --brand: #2D6CDF;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">@yield('code')</div>
        <h1 class="error-heading">@yield('heading')</h1>
        <p class="error-message">@yield('message')</p>
        <a href="@yield('action_url', '/')" class="error-action">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            @yield('action_text', 'Go Home')
        </a>
        <a href="/" class="brand-link"><span>StudAI</span> Hire</a>
    </div>
    @stack('scripts')
</body>
</html>
