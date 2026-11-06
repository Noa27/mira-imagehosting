@extends('layout')

@section('content')
    <style>
        .login-title { margin: 8vh 0 34px; text-align: center; font: italic 900 clamp(3rem, 12vw, 5.5rem)/1 var(--display); letter-spacing: 3px; color: #ffd23f; transform: skew(-8deg); text-shadow: 3px 3px 0 #ff5a1f, 6px 6px 0 #c4176f, 9px 9px 0 #4b0f5c, 0 0 40px rgba(255, 79, 216, .45); }
        .login { max-width: 380px; margin: 0 auto; }
        .login .btn { width: 100%; margin-top: 4px; }
    </style>

    <div class="login-title">{{ strtoupper(config('app.name')) }}</div>
    <form class="card login {{ $setup ? 'c-orange' : 'c-red' }}" method="post" action="{{ $setup ? route('setup') : route('login') }}">
        @csrf
        <h2>{{ $setup ? __('Créer') : __('Connexion') }}</h2>
        @if ($setup)
            <p class="muted">{{ __("Aucun compte pour l'instant, crée le compte admin.") }}</p>
        @endif
        <p><input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Nom') }}" autofocus required></p>
        <p><input type="password" name="password" placeholder="{{ __('Mot de passe') }}" required></p>
        @if ($errors->any())<p class="error">{{ $errors->first() }}</p>@endif
        <button class="btn">{{ $setup ? __('Créer') : __('Connexion') }}</button>
    </form>
@endsection
