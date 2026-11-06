@extends('layout')

@section('content')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 11px 8px; border-bottom: 1px solid var(--line); vertical-align: middle; }
        th { color: var(--muted); font: 700 10px var(--display); letter-spacing: 2px; text-transform: uppercase; }
        td.name { font: 16px var(--mono); }
        .badge { margin-left: 6px; padding: 1px 6px; border: 1px solid var(--pink); border-radius: 4px; color: var(--pink); font: 700 9px var(--display); letter-spacing: 1.5px; vertical-align: middle; }
        .inline { display: flex; gap: 6px; align-items: center; }
        .inline input { width: 150px; padding: 6px 9px; font-size: 14px; }
        label { display: flex; align-items: center; gap: 6px; font: 700 11px var(--display); letter-spacing: 1.5px; text-transform: uppercase; color: var(--muted); }
        .grid { display: grid; grid-template-columns: 1fr 1fr auto auto; gap: 10px; align-items: center; }
        @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } .scroll { overflow-x: auto; } }
    </style>

    <form class="card c-red" method="post" action="{{ route('admin.store') }}">
        @csrf
        <h2>{{ __('Nouveau compte') }}</h2>
        <div class="grid">
            <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Nom') }}" required>
            <input type="password" name="password" placeholder="{{ __('Mot de passe') }}" required>
            <label><input type="checkbox" name="is_admin" value="1"> Admin</label>
            <button class="btn">{{ __('Créer') }}</button>
        </div>
        @if ($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif
    </form>

    <div class="card c-orange scroll">
        <h2>{{ __('Comptes') }}</h2>
        <table>
            <tr><th>{{ __('Nom') }}</th><th>Images</th><th>{{ __('Taille') }}</th><th>{{ __('Mot de passe') }}</th><th></th></tr>
            @foreach ($users as $user)
                <tr>
                    <td class="name">{{ $user->name }}@if ($user->is_admin)<span class="badge">ADMIN</span>@endif</td>
                    <td class="digits">{{ $user->images_count }}</td>
                    <td class="digits" style="white-space: nowrap;">{{ number_format(($user->images_sum_size ?? 0) / 1048576, 1, __(','), ' ') }} {{ __('Mo') }}</td>
                    <td>
                        <form class="inline" method="post" action="{{ route('admin.password', $user) }}">
                            @csrf @method('put')
                            <input type="password" name="password" placeholder="{{ __('Nouveau') }}" required>
                            <button class="btn small ghost">OK</button>
                        </form>
                    </td>
                    <td>
                        @unless ($user->is(auth()->user()))
                            <form method="post" action="{{ route('admin.destroy', $user) }}" onsubmit="return confirm(@js(__('Supprimer :name et toutes ses images ?', ['name' => $user->name])))">
                                @csrf @method('delete')
                                <button class="btn small danger">{{ __('Supprimer') }}</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
