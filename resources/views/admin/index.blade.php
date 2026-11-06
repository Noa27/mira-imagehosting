@extends('layout')

@section('content')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid var(--line); vertical-align: middle; }
        th { color: var(--muted); font-weight: 500; font-size: 13px; }
        .inline { display: flex; gap: 6px; align-items: center; }
        .inline input { width: 150px; padding: 5px 8px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr auto auto; gap: 10px; align-items: center; }
        @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } .scroll { overflow-x: auto; } }
    </style>

    <form class="card" method="post" action="{{ route('admin.store') }}">
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

    <div class="card scroll">
        <h2>{{ __('Comptes') }}</h2>
        <table>
            <tr><th>{{ __('Nom') }}</th><th>Images</th><th>{{ __('Taille') }}</th><th>{{ __('Mot de passe') }}</th><th></th></tr>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }} @if ($user->is_admin)<span class="muted">(admin)</span>@endif</td>
                    <td>{{ $user->images_count }}</td>
                    <td style="white-space: nowrap;">{{ number_format(($user->images_sum_size ?? 0) / 1048576, 1, __(','), ' ') }} {{ __('Mo') }}</td>
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
