<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                // Logic: Check if the user has the 'admin' role
                'is_admin' => $request->user() ? $request->user()->hasRole('admin') : false,
                'permissions' => $request->user() ? $request->user()->getAllPermissions()->pluck('name') : [],
                'roles' => $request->user() ? $request->user()->getRoleNames() : [],
            ],
            'csrf_token' => csrf_token(),
            'locale' => app()->getLocale(),
            'translations' => function () {
                return [
                    'app'        => trans()->get('app'),
                    'auth'       => trans()->get('auth'),
                    'messages'   => trans()->get('messages'),
                    'pagination' => trans()->get('pagination'),
                    'passwords'  => trans()->get('passwords'),
                    'validation' => trans()->get('validation'),
                ];
            },
        ];
    }
}
