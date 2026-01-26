<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardController extends AdminBaseController
{
    public function index(Request $request)
    {
        // Prepare dashboard statistics
        $statistics = [
            'total_users' => User::count(),
            'total_stores' => Store::count(),
            'active_stores' => Store::where('is_active', true)->count(),
            'total_products' => Product::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'roles_breakdown' => $this->getRolesBreakdown(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get breakdown of users by role
     */
    private function getRolesBreakdown(): array
    {
        $breakdown = [];
        $roles = Role::all();

        foreach ($roles as $role) {
            $breakdown[$role->name] = $role->users()->count();
        }

        return $breakdown;
    }
}
