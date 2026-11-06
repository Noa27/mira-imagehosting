<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public const LOCALES = ['fr', 'en'];

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->hasSession() ? $request->session()->get('locale') : null;

        app()->setLocale(in_array($locale, self::LOCALES, true) ? $locale : $request->getPreferredLanguage(self::LOCALES));

        return $next($request);
    }
}
