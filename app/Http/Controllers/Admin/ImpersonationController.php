<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationController extends Controller
{
    /**
     * Impersonate a user (only Super Admin can do this)
     */
    public function impersonate(Request $request, User $user)
    {
        // Check if user has permission to impersonate
        if (!auth()->user()->hasPermissionTo('impersonate_users')) {
            abort(403, 'لا توجد صلاحية للدخول كمستخدم آخر');
        }

        // Store the original admin user ID in session
        Session::put('impersonating_by', auth()->id());
        Session::put('impersonation_start', now());

        // Log the impersonation
        \Log::info('User ' . auth()->user()->id . ' impersonating ' . $user->id);

        // Login as the user
        Auth::login($user, remember: false);

        return redirect('/')->with('success', "أنت تستعرض الآن كـ: {$user->name}");
    }

    /**
     * Stop impersonating and return to admin account
     */
    public function stopImpersonate(Request $request)
    {
        // Check if we're currently impersonating
        if (!Session::has('impersonating_by')) {
            return redirect('/')->with('error', 'لم يتم العثور على جلسة استعراض نشطة');
        }

        $originalAdminId = Session::get('impersonating_by');
        $originalAdmin = User::find($originalAdminId);

        // Log the end of impersonation
        \Log::info('User ' . $originalAdminId . ' stopped impersonating');

        // Forget the impersonation session
        Session::forget('impersonating_by');
        Session::forget('impersonation_start');

        // Login back as the original admin
        Auth::login($originalAdmin, remember: false);

        return redirect('/admin/dashboard')->with('success', 'تم العودة إلى حسابك الأصلي');
    }

    /**
     * Get impersonation status
     */
    public function getImpersonationStatus(Request $request)
    {
        if (Session::has('impersonating_by')) {
            $impersonatingBy = User::find(Session::get('impersonating_by'));
            $startTime = Session::get('impersonation_start');

            return response()->json([
                'impersonating' => true,
                'impersonating_by' => $impersonatingBy,
                'current_user' => auth()->user(),
                'start_time' => $startTime,
                'elapsed_time' => $startTime ? now()->diffInMinutes($startTime) : 0,
            ]);
        }

        return response()->json(['impersonating' => false]);
    }

    /**
     * List all users for impersonation
     */
    public function listUsers(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermissionTo('impersonate_users')) {
            abort(403, 'لا توجد صلاحية');
        }

        // Get users with filters
        $query = User::query();

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->role($request->role);
        }

        // Filter by search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        // Exclude current user
        $query->where('id', '!=', auth()->id());

        $users = $query->with('roles')
            ->paginate(20);

        return response()->json($users);
    }
}
