<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class LanguageManager
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $defaultLocale = env('APPLANG', 'en');

        try {
            if (Auth::check()) {
                // User is authenticated
                $userLanguage = Auth::user()->language;
                app()->setLocale($userLanguage);
                Session::put('locale', $userLanguage);

                if (Session::get('language.code') !== $userLanguage) {
                    $language = Language::where('code', $userLanguage)->first();
                    Session::put('language', $language);
                }

            } else {
                // User is not authenticated (guest)
                $sessionLocale = Session::get('landing_locale', $defaultLocale);
                app()->setLocale($sessionLocale);

                if (Session::get('language.code') !== $sessionLocale) {
                    $language = Language::where('code', $sessionLocale)->first();
                    Session::put('language', $language);
                }
            }
        } catch (\Throwable $th) {
            // Fallback in case of any error (e.g., database not ready)
            app()->setLocale($defaultLocale);
        }

        return $next($request);
    }
}
