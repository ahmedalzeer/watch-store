<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            abort(401, 'يجب تسجيل الدخول أولاً');
        }

        if (!auth()->user()->hasAnyRole($roles)) {
            $rolesList = implode(' أو ', $roles);
            abort(403, "يجب أن تكون لديك أحد الأدوار التالية: {$rolesList}");
        }

        return $next($request);
    }
}
