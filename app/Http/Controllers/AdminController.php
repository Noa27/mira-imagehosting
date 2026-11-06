<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index', [
            'users' => User::withCount('images')->withSum('images', 'size')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|alpha_dash|unique:users,name',
            'password' => 'required|string|min:6',
            'is_admin' => 'nullable|boolean',
        ]);

        User::create([...$data, 'is_admin' => $request->boolean('is_admin')]);

        return back()->with('status', __('Compte :name créé', ['name' => $data['name']]));
    }

    public function password(Request $request, User $user)
    {
        $request->validate(['password' => 'required|string|min:6']);
        $user->update(['password' => $request->password]);

        return back()->with('status', __('Mot de passe de :name changé', ['name' => $user->name]));
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->is($request->user()), 422, __('Tu ne peux pas supprimer ton propre compte'));

        $user->images->each->delete();
        $user->delete();

        return back()->with('status', __('Compte :name supprimé', ['name' => $user->name]));
    }
}
