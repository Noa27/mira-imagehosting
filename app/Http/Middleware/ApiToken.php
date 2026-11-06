<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->input('token');
        $user = $token ? User::where('api_token', hash('sha256', $token))->first() : null;

        abort_unless($user, 401, __('Token invalide'));
        Auth::setUser($user);

        return $next($request);
    }
}
