@extends('layout')

@section('content')
    <form class="card" method="post" action="{{ $setup ? route('setup') : route('login') }}" style="max-width: 360px; margin: 12vh auto 0;">
        @csrf
        <h2>{{ config('app.name') }}</h2>
        @if ($setup)
            <p class="muted">{{ __("Aucun compte pour l'instant, crée le compte admin.") }}</p>
        @endif
        <p><input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Nom') }}" autofocus required></p>
        <p><input type="password" name="password" placeholder="{{ __('Mot de passe') }}" required></p>
        @if ($errors->any())<p class="error">{{ $errors->first() }}</p>@endif
        <button class="btn">{{ $setup ? __('Créer') : __('Connexion') }}</button>
    </form>
@endsection
