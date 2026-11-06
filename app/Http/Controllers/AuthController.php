<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show()
    {
        return view('login', ['setup' => User::doesntExist()]);
    }

    public function setup(Request $request)
    {
        abort_if(User::exists(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:50|alpha_dash',
            'password' => 'required|string|min:6',
        ]);

        Auth::login(User::create([...$data, 'is_admin' => true]), true);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, true)) {
            return back()->withErrors(['name' => __('Identifiants incorrects')])->onlyInput('name');
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
