<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermissionManagementController extends Controller
{
    /**
     * Display permissions and roles management page
     */
    public function index(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermissionTo('manage_permissions')) {
            abort(403, 'لا توجد صلاحية لإدارة الصلاحيات');
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($item) {
            // Group permissions by type
            if (str_contains($item->name, 'user')) return 'إدارة المستخدمين';
            if (str_contains($item->name, 'store')) return 'إدارة المتاجر';
            if (str_contains($item->name, 'product')) return 'إدارة المنتجات';
            if (str_contains($item->name, 'order')) return 'إدارة الطلبات';
            if (str_contains($item->name, 'payment')) return 'إدارة الدفع';
            if (str_contains($item->name, 'banner') || str_contains($item->name, 'brand') || str_contains($item->name, 'category')) return 'إدارة المحتوى';
            return 'صلاحيات النظام';
        });

        return Inertia::render('Admin/Permissions/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request, Role $role)
    {
        // Check permission
        if (!auth()->user()->hasPermissionTo('manage_permissions')) {
            abort(403, 'لا توجد صلاحية');
        }

        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        // Log the change
        \Log::info("Role '{$role->name}' permissions updated by user " . auth()->id());

        return redirect()->back()->with('success', "تم تحديث صلاحيات الدور '{$role->name}' بنجاح");
    }

    /**
     * View user roles and permissions
     */
    public function viewUserPermissions(User $user)
    {
        // Check permission
        if (!auth()->user()->hasPermissionTo('view_users')) {
            abort(403, 'لا توجد صلاحية');
        }

        $userRoles = $user->roles()->get();
        $userPermissions = $user->getAllPermissions();
        $allRoles = Role::all();
        $allPermissions = Permission::all();

        return Inertia::render('Admin/Permissions/UserPermissions', [
            'user' => $user,
            'userRoles' => $userRoles,
            'userPermissions' => $userPermissions,
            'allRoles' => $allRoles,
            'allPermissions' => $allPermissions,
        ]);
    }

    /**
     * Update user roles
     */
    public function updateUserRoles(Request $request, User $user)
    {
        // Check permission
        if (!auth()->user()->hasPermissionTo('manage_user_roles')) {
            abort(403, 'لا توجد صلاحية');
        }

        // Don't allow changing super admin roles
        if ($user->hasRole('super_admin') && auth()->user()->id !== $user->id) {
            abort(403, 'لا يمكن تعديل أدوار Super Admin');
        }

        $validated = $request->validate([
            'roles' => 'array|required',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->syncRoles($validated['roles']);

        // Log the change
        \Log::info("User {$user->id} roles updated to: " . implode(', ', $validated['roles']) . " by " . auth()->id());

        return redirect()->back()->with('success', "تم تحديث أدوار المستخدم '{$user->name}' بنجاح");
    }

    /**
     * Assign direct permissions to user
     */
    public function assignDirectPermissions(Request $request, User $user)
    {
        // Check permission
        if (!auth()->user()->hasPermissionTo('manage_permissions')) {
            abort(403, 'لا توجد صلاحية');
        }

        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user->syncPermissions($validated['permissions'] ?? []);

        // Log the change
        \Log::info("User {$user->id} direct permissions updated by " . auth()->id());

        return redirect()->back()->with('success', "تم تحديث الصلاحيات المباشرة للمستخدم '{$user->name}' بنجاح");
    }

    /**
     * Create new role
     */
    public function createRole(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermissionTo('manage_permissions')) {
            abort(403, 'لا توجد صلاحية');
        }

        $validated = $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $role->syncPermissions($validated['permissions'] ?? []);

        // Log the change
        \Log::info("New role '{$role->name}' created by " . auth()->id());

        return redirect()->back()->with('success', "تم إنشاء الدور '{$role->name}' بنجاح");
    }

    /**
     * Delete role
     */
    public function deleteRole(Role $role)
    {
        // Check permission
        if (!auth()->user()->hasPermissionTo('manage_permissions')) {
            abort(403, 'لا توجد صلاحية');
        }

        // Prevent deleting system roles
        $systemRoles = ['super_admin', 'admin', 'moderator', 'vendor', 'customer'];
        if (in_array($role->name, $systemRoles)) {
            return redirect()->back()->with('error', "لا يمكن حذف الأدوار النظامية");
        }

        $roleName = $role->name;
        $role->delete();

        // Log the change
        \Log::info("Role '{$roleName}' deleted by " . auth()->id());

        return redirect()->back()->with('success', "تم حذف الدور '{$roleName}' بنجاح");
    }

    /**
     * Get permissions summary for dashboard
     */
    public function permissionsSummary(Request $request)
    {
        $summary = [
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'total_users' => User::count(),
            'roles_breakdown' => Role::withCount('users')->get()->keyBy('name')->transform(fn($role) => $role->users_count),
            'top_permissions' => Permission::withCount('roles', 'users')
                ->orderBy('roles_count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json($summary);
    }
}
