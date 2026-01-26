<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreLocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get store from subdomain
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        $store = \App\Models\Store::where('subdomain', $subdomain)->first();

        // Set locale from store's primary language or session
        if ($store) {
            $locale = session('locale', $store->primary_language ?? config('app.fallback_locale'));
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
