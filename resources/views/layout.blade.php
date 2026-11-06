<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --bg: #07060d;
            --surface: #17161d;
            --raised: #211f2a;
            --well: #0e0d13;
            --line: #2c2a35;
            --line-strong: #3d3a48;
            --text: #e9e6f2;
            --muted: #9a95a8;
            --amber: #ffb627;
            --amber-dark: #a96d00;
            --red: #ff3b30;
            --orange: #ff7a1a;
            --cream: #f5dcae;
            --pink: #ff4fd8;
            --display: 'Orbitron', system-ui, sans-serif;
            --mono: 'Share Tech Mono', ui-monospace, monospace;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: var(--bg); color: var(--text); font: 15px/1.5 system-ui, sans-serif; }
        a { color: var(--amber); }
        .stars { position: fixed; inset: 0; pointer-events: none; z-index: -2; }
        .stars i { position: absolute; width: 2px; height: 2px; border-radius: 50%; background: #fff; animation: blink 3s infinite; }
        .floor { position: fixed; left: -50%; right: -50%; bottom: -10vh; height: 50vh; z-index: -1; pointer-events: none; transform: perspective(300px) rotateX(62deg); transform-origin: bottom; background-image: linear-gradient(var(--pink) 2px, transparent 2px), linear-gradient(90deg, var(--pink) 2px, transparent 2px); background-size: 60px 60px; opacity: .16; animation: road 1.6s linear infinite; }
        body.warp .floor { animation-duration: .15s; opacity: .45; }
        @keyframes road { to { background-position: 0 60px; } }
        @keyframes blink { 50% { opacity: .2; } }

        .top { position: sticky; top: 0; z-index: 10; background: rgba(7, 6, 13, .82); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 2px solid var(--line); }
        .bar { max-width: 1100px; margin: 0 auto; padding: 12px 16px; display: flex; align-items: center; gap: 22px; }
        .brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .brand-name { font: italic 900 26px/1 var(--display); letter-spacing: 2px; color: #ffd23f; transform: skew(-8deg); text-shadow: 2px 2px 0 #ff5a1f, 4px 4px 0 #c4176f, 0 0 20px rgba(255, 79, 216, .4); }
        .mark { width: 36px; height: 36px; border-radius: 9px; display: grid; place-items: center; background: #1d1b24; border: 2px solid var(--line-strong); color: #fffbe0; }
        .mark svg { filter: drop-shadow(0 0 4px #ffd23f); }
        .nav { display: flex; gap: 6px; }
        .nav a { white-space: nowrap; padding: 7px 14px; border-radius: 8px; font: 700 12px var(--display); letter-spacing: 1.5px; text-transform: uppercase; text-decoration: none; color: var(--muted); border: 1px solid transparent; transition: color .15s, border-color .15s; }
        .nav a:hover { color: var(--text); border-color: var(--line-strong); }
        .nav a.on { color: #120d05; background: var(--amber); box-shadow: 0 3px 0 var(--amber-dark); }
        .right { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .lang-switch { display: flex; padding: 3px; border: 1px solid var(--line-strong); border-radius: 8px; background: var(--well); }
        .lang-switch a, .lang-switch span { padding: 3px 9px; border-radius: 5px; font: 700 11px var(--display); letter-spacing: 1px; text-decoration: none; color: var(--muted); }
        .lang-switch span { color: #120d05; background: var(--amber); }
        .lang-switch a:hover { color: var(--text); }
        .user { display: flex; align-items: center; gap: 8px; font: 15px var(--mono); color: var(--text); }
        .avatar { width: 32px; height: 32px; border-radius: 8px; display: grid; place-items: center; font: 700 14px var(--mono); text-transform: uppercase; color: var(--amber); background: var(--well); border: 2px solid #5a3d0a; }
        .icon-btn { width: 36px; height: 36px; border-radius: 8px; display: grid; place-items: center; border: 2px solid var(--line-strong); background: transparent; color: var(--muted); cursor: pointer; transition: color .15s, border-color .15s, box-shadow .15s; }
        .icon-btn:hover { color: var(--red); border-color: var(--red); box-shadow: 0 0 12px rgba(255, 59, 48, .4); }
        .guest-lang { position: absolute; top: 16px; right: 16px; }
        @media (max-width: 640px) {
            .bar { gap: 10px; }
            .brand-name, .user { display: none; }
            .nav { gap: 4px; }
            .nav a { padding: 6px 9px; font-size: 11px; letter-spacing: 1px; }
            .right { gap: 8px; }
        }

        main { max-width: 1100px; margin: 0 auto; padding: 26px 16px 60px; }
        .card { --c: var(--amber); background: var(--surface); border: 3px solid var(--line-strong); border-radius: 14px; padding: 20px; margin-bottom: 22px; box-shadow: inset 0 0 0 2px #0c0b10, 0 0 0 3px #0c0b10, 0 16px 40px rgba(0, 0, 0, .5); }
        .c-red { --c: var(--red); }
        .c-orange { --c: var(--orange); }
        .c-pink { --c: var(--pink); }
        h2 { display: inline-block; margin: 0 0 16px; padding: 3px 14px; border-radius: 4px; background: var(--c, var(--amber)); color: #0b0a0f; font: 700 12px/1.6 var(--display); letter-spacing: 3px; text-transform: uppercase; }
        .digits { font-family: var(--mono); color: var(--c, var(--amber)); text-shadow: 0 0 8px var(--c, var(--amber)); }
        input[type=text], input[type=password], textarea { width: 100%; padding: 10px 12px; border: 2px solid var(--line-strong); border-radius: 8px; background: var(--well); color: var(--text); font: 15px var(--mono); }
        input:focus, textarea:focus { outline: none; border-color: var(--amber); box-shadow: 0 0 12px rgba(255, 182, 39, .25); }
        input::placeholder { color: #6c6779; }
        input[type=checkbox] { accent-color: var(--amber); }
        textarea { font-size: 13px; color: var(--cream); resize: vertical; }
        .btn { display: inline-block; padding: 10px 18px; border: 0; border-radius: 8px; background: var(--amber); color: #120d05; font: 900 13px var(--display); letter-spacing: 2px; text-transform: uppercase; cursor: pointer; text-decoration: none; box-shadow: 0 4px 0 var(--amber-dark); transition: transform .08s, box-shadow .08s, filter .15s; }
        .btn:hover { filter: brightness(1.08); }
        .btn:active { transform: translateY(3px); box-shadow: 0 1px 0 var(--amber-dark); }
        .btn.small { padding: 6px 11px; font-size: 10px; letter-spacing: 1.5px; box-shadow: 0 3px 0 var(--amber-dark); }
        .btn.ghost { background: transparent; color: var(--amber); border: 2px solid var(--amber); box-shadow: none; }
        .btn.ghost:hover { background: var(--amber); color: #120d05; filter: none; }
        .btn.danger { background: transparent; color: var(--red); border: 2px solid var(--red); box-shadow: none; }
        .btn.danger:hover { background: var(--red); color: #fff; filter: none; box-shadow: 0 0 14px rgba(255, 59, 48, .5); }
        .flash { padding: 10px 14px; margin-bottom: 18px; border-radius: 8px; background: var(--well); border: 2px solid #5a3d0a; color: var(--amber); font: 15px var(--mono); text-shadow: 0 0 6px rgba(255, 182, 39, .4); }
        .flash::before { content: '> '; }
        .error { color: var(--red); font: 14px var(--mono); }
        .muted { color: var(--muted); font-size: 14px; }
        code { background: var(--well); color: var(--cream); padding: 2px 6px; border-radius: 4px; font: 13px var(--mono); word-break: break-all; }
    </style>
</head>
<body>
    <div class="stars" id="stars"></div>
    <div class="floor"></div>
    @auth
        <header class="top">
            <div class="bar">
                <a class="brand" href="{{ route('home') }}">
                    <span class="mark">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true">
                            <path d="M12 13v8M12 13 5 5M12 13l7-8"/><circle cx="12" cy="13" r="2.6" fill="currentColor" stroke="none"/>
                        </svg>
                    </span>
                    <span class="brand-name">{{ strtoupper(config('app.name')) }}</span>
                </a>
                <nav class="nav">
                    <a href="{{ route('home') }}" @class(['on' => request()->routeIs('home')])>{{ __('Mes images') }}</a>
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.index') }}" @class(['on' => request()->routeIs('admin.*')])>{{ __('Panel') }}</a>
                    @endif
                </nav>
                <div class="right">
                    @include('lang-switch')
                    <span class="user">
                        <span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                        <span class="uname">{{ auth()->user()->name }}</span>
                    </span>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="icon-btn" title="{{ __('Déconnexion') }}" aria-label="{{ __('Déconnexion') }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <path d="M16 17l5-5-5-5"/>
                                <path d="M21 12H9"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>
    @else
        <div class="guest-lang">@include('lang-switch')</div>
    @endauth
    <main>
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
    <script>
        for (let i = 0; i < 60; i++) {
            const star = document.createElement('i');
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 70 + '%';
            star.style.opacity = .2 + Math.random() * .6;
            star.style.animationDelay = Math.random() * 3 + 's';
            document.getElementById('stars').append(star);
        }
    </script>
</body>
</html>
