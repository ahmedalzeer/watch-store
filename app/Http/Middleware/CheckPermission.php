<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            abort(401, 'يجب تسجيل الدخول أولاً');
        }

        if (!auth()->user()->hasPermissionTo($permission)) {
            abort(403, "لا توجد صلاحية: {$permission}");
        }

        return $next($request);
    }
}
