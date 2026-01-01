<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\User;
use App\Http\Requests\Admin\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends AdminBaseController
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::query()
                ->with('roles:id,name')
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->latest()
                ->paginate(10)
                ->withQueryString()
                ->through(fn($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->roles->pluck('name')->first(),
                    'profile_photo_url' => $user->profile_photo_url,
                ]),
            'filters' => $request->only(['search']),
            'available_roles' => Role::all(['id', 'name']),
        ]);
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        if ($request->role) {
            $user->assignRole($request->role);
        }

        if ($request->avatar_path) {
            $fullPath = storage_path('app/public/' . $request->avatar_path);
            if (file_exists($fullPath)) {
                $user->addMedia($fullPath)->toMediaCollection('avatars');
            }
        }

        return redirect()->back();
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        unset($data['role']);

        $user->update($data);

        if ($request->role) {
            $user->syncRoles($request->role);
        }

        if ($request->filled('avatar_path')) {
            $fullPath = storage_path('app/public/' . $request->avatar_path);
            if (file_exists($fullPath)) {
                $user->clearMediaCollection('avatars');
                $user->addMedia($fullPath)->toMediaCollection('avatars');
            }
        }

        return redirect()->back();
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back();
    }
}
