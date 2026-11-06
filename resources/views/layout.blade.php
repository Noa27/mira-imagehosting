<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <style>
        :root {
            --bg: #f3e8da;
            --surface: #fffaf3;
            --raised: #f3e4d0;
            --line: #e7d5bf;
            --line-strong: #d4b896;
            --text: #3a2618;
            --muted: #8a6a52;
            --accent: #a5582a;
            --accent-hover: #86441e;
            --accent-soft: #e8a868;
            --on-accent: #fffaf3;
            --danger: #b23b2a;
            --danger-bg: #fbe3dc;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--text); font: 15px/1.5 system-ui, sans-serif; }
        a { color: var(--accent); }
        .top { position: sticky; top: 0; z-index: 10; background: rgba(243, 232, 218, .85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid var(--line); }
        .bar { max-width: 1100px; margin: 0 auto; padding: 12px 16px; display: flex; align-items: center; gap: 22px; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); font: 600 22px/1 Georgia, 'Iowan Old Style', 'Times New Roman', serif; letter-spacing: .3px; }
        .mark { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; color: var(--accent-soft); background: var(--text); }
        .nav { display: flex; gap: 4px; }
        .nav a { white-space: nowrap; padding: 7px 13px; border-radius: 999px; font-size: 14px; text-decoration: none; color: var(--muted); transition: background .15s, color .15s; }
        .nav a:hover { color: var(--text); background: var(--raised); }
        .nav a.on { color: var(--on-accent); background: var(--accent); }
        .right { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .lang-switch { display: flex; padding: 3px; border: 1px solid var(--line); border-radius: 999px; background: var(--surface); }
        .lang-switch a, .lang-switch span { padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; letter-spacing: .8px; text-decoration: none; color: var(--muted); }
        .lang-switch span { color: var(--on-accent); background: var(--accent); }
        .lang-switch a:hover { color: var(--text); }
        .user { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text); }
        .avatar { width: 32px; height: 32px; border-radius: 50%; display: grid; place-items: center; font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--text); background: var(--accent-soft); }
        .icon-btn { width: 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; border: 1px solid var(--line); background: transparent; color: var(--muted); cursor: pointer; transition: color .15s, border-color .15s, background .15s; }
        .icon-btn:hover { color: var(--danger); border-color: var(--danger); background: var(--danger-bg); }
        .guest-lang { position: absolute; top: 16px; right: 16px; }
        @media (max-width: 640px) {
            .bar { gap: 12px; }
            .brand-name, .user { display: none; }
            .nav a { padding: 6px 10px; }
            .right { gap: 8px; }
        }
        main { max-width: 1100px; margin: 0 auto; padding: 24px 16px 48px; }
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        h2 { margin: 0 0 12px; font-size: 17px; color: var(--text); }
        input[type=text], input[type=password], textarea { width: 100%; padding: 9px 11px; border: 1px solid var(--line-strong); border-radius: 8px; background: #fff; color: var(--text); font: inherit; }
        input:focus, textarea:focus { outline: none; border-color: var(--accent); }
        input::placeholder { color: var(--muted); }
        input[type=checkbox] { accent-color: var(--accent); }
        textarea { font-family: ui-monospace, monospace; font-size: 13px; resize: vertical; }
        .btn { display: inline-block; padding: 9px 16px; border: 1px solid transparent; border-radius: 8px; background: var(--accent); color: var(--on-accent); font: inherit; font-weight: 600; cursor: pointer; text-decoration: none; transition: background .15s, color .15s, border-color .15s; }
        .btn:hover { background: var(--accent-hover); }
        .btn.small { padding: 5px 10px; font-size: 13px; }
        .btn.ghost { background: transparent; color: var(--accent); border-color: var(--line-strong); }
        .btn.ghost:hover { border-color: var(--accent); }
        .btn.danger { background: var(--danger-bg); color: var(--danger); border-color: #efc0b3; }
        .btn.danger:hover { background: var(--danger); color: #fff; }
        .flash { padding: 10px 14px; border-radius: 8px; background: var(--raised); border-left: 3px solid var(--accent); margin-bottom: 16px; }
        .error { color: var(--danger); font-size: 14px; }
        .muted { color: var(--muted); font-size: 14px; }
        code { background: var(--raised); color: #6b3a1c; padding: 1px 5px; border-radius: 4px; font-size: 13px; word-break: break-all; }
    </style>
</head>
<body>
    @auth
        <header class="top">
            <div class="bar">
                <a class="brand" href="{{ route('home') }}">
                    <span class="mark">
                        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                            <ellipse cx="12" cy="12" rx="7" ry="10" transform="rotate(35 12 12)" fill="currentColor"/>
                            <path d="M8.6 18.6c2.6-1.9 1.6-4.6 3.4-6.6s4.4-1.7 4.6-6.4" stroke="var(--text)" stroke-width="1.7" fill="none" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="brand-name">{{ config('app.name') }}</span>
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
</body>
</html>
