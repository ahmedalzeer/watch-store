<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminDashboardAnalyticsController extends Controller
{
    /**
     * Get comprehensive admin dashboard statistics
     */
    public function getStatistics(Request $request)
    {
        $period = $request->query('period', '30'); // days

        return response()->json([
            'system_stats' => $this->calculateSystemStatistics($period),
            'store_stats' => $this->getStoresStatistics(),
            'user_stats' => $this->getUsersStatistics(),
            'charts' => $this->getSystemChartData($period),
            'recent_activities' => $this->getSystemActivities(),
            'issues' => $this->getSystemIssues(),
            'performance' => $this->getSystemPerformance(),
        ]);
    }

    /**
     * Calculate system-wide statistics
     */
    private function calculateSystemStatistics($period): array
    {
        $fromDate = Carbon::now()->subDays($period);

        $totalOrders = Order::where('created_at', '>=', $fromDate)->count();
        $totalRevenue = OrderItem::where('created_at', '>=', $fromDate)
            ->sum(DB::raw('quantity * price'));

        $totalStores = Store::count();
        $activeStores = Store::where('is_active', true)->count();

        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();

        $totalUsers = User::count();
        $vendorCount = User::role('vendor')->count();
        $customerCount = User::role('customer')->count();

        $totalReviews = ProductReview::count();
        $averageRating = ProductReview::avg('rating') ?? 0;

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => number_format($totalRevenue, 2),
            'total_stores' => $totalStores,
            'active_stores' => $activeStores,
            'inactive_stores' => $totalStores - $activeStores,
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'total_users' => $totalUsers,
            'vendor_count' => $vendorCount,
            'customer_count' => $customerCount,
            'total_reviews' => $totalReviews,
            'average_rating' => number_format($averageRating, 1),
        ];
    }

    /**
     * Get per-store statistics
     */
    private function getStoresStatistics(): array
    {
        $stores = Store::withCount(['products', 'orders'])
            ->with(['user'])
            ->limit(10)
            ->get();

        return $stores->map(fn($store) => [
            'id' => $store->id,
            'name' => $store->name,
            'owner' => $store->user?->name,
            'status' => $store->is_active ? 'نشط' : 'معطل',
            'products_count' => $store->products_count,
            'orders_count' => $store->orders_count,
            'revenue' => $this->getStoreRevenue($store),
        ])->toArray();
    }

    /**
     * Get revenue for specific store
     */
    private function getStoreRevenue(Store $store): string
    {
        $revenue = OrderItem::where('store_id', $store->id)
            ->sum(DB::raw('quantity * price'));
        return number_format($revenue, 2);
    }

    /**
     * Get users statistics
     */
    private function getUsersStatistics(): array
    {
        return [
            'total_users' => User::count(),
            'super_admins' => User::role('super_admin')->count(),
            'admins' => User::role('admin')->count(),
            'moderators' => User::role('moderator')->count(),
            'vendors' => User::role('vendor')->count(),
            'customers' => User::role('customer')->count(),
            'verified_emails' => User::whereNotNull('email_verified_at')->count(),
            'unverified_emails' => User::whereNull('email_verified_at')->count(),
        ];
    }

    /**
     * Get system-wide chart data
     */
    private function getSystemChartData($period): array
    {
        $fromDate = Carbon::now()->subDays($period);
        $dates = collect();

        for ($i = $period; $i >= 0; $i--) {
            $dates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        // System revenue
        $revenueData = [];
        foreach ($dates as $date) {
            $revenue = OrderItem::whereDate('created_at', $date)
                ->sum(DB::raw('quantity * price'));
            $revenueData[] = (float)$revenue;
        }

        // System orders
        $ordersData = [];
        foreach ($dates as $date) {
            $count = Order::whereDate('created_at', $date)->count();
            $ordersData[] = $count;
        }

        // User registration trends
        $usersData = [];
        foreach ($dates as $date) {
            $count = User::whereDate('created_at', $date)->count();
            $usersData[] = $count;
        }

        // Store activity
        $storesActivity = Store::with('user')
            ->latest('updated_at')
            ->limit(10)
            ->get(['id', 'name', 'user_id', 'is_active']);

        return [
            'revenue_chart' => [
                'labels' => $dates->map(fn($d) => date('m/d', strtotime($d)))->toArray(),
                'data' => $revenueData,
            ],
            'orders_chart' => [
                'labels' => $dates->map(fn($d) => date('m/d', strtotime($d)))->toArray(),
                'data' => $ordersData,
            ],
            'users_chart' => [
                'labels' => $dates->map(fn($d) => date('m/d', strtotime($d)))->toArray(),
                'data' => $usersData,
            ],
            'stores_activity' => $storesActivity,
        ];
    }

    /**
     * Get system activities
     */
    private function getSystemActivities(): array
    {
        $recentOrders = Order::latest()->limit(5)->get(['id', 'status', 'created_at']);

        $recentStores = Store::with('user')
            ->latest()
            ->limit(5)
            ->get(['id', 'name', 'user_id', 'is_active', 'created_at']);

        $recentUsers = User::latest()
            ->limit(5)
            ->get(['id', 'name', 'email', 'created_at']);

        return [
            'recent_orders' => $recentOrders,
            'recent_stores' => $recentStores,
            'recent_users' => $recentUsers,
        ];
    }

    /**
     * Get system-wide issues
     */
    private function getSystemIssues(): array
    {
        $issues = [];

        // Check for inactive stores
        $inactiveStores = Store::where('is_active', false)->count();
        if ($inactiveStores > 0) {
            $issues[] = [
                'severity' => 'warning',
                'title' => 'متاجر معطلة',
                'message' => "هناك {$inactiveStores} متجر معطل في النظام",
                'action' => 'عرض المتاجر',
                'link' => '/admin/stores',
            ];
        }

        // Check for pending orders
        $pendingOrders = Order::where('status', 'pending')->count();
        if ($pendingOrders > 0) {
            $issues[] = [
                'severity' => 'info',
                'title' => 'طلبيات معلقة',
                'message' => "هناك {$pendingOrders} طلبية قيد الانتظار",
                'action' => 'عرض الطلبيات',
                'link' => '/admin/orders',
            ];
        }

        // Check for unverified users
        $unverified = User::whereNull('email_verified_at')->count();
        if ($unverified > 0) {
            $issues[] = [
                'severity' => 'warning',
                'title' => 'مستخدمون بدون تحقق',
                'message' => "{$unverified} مستخدم لم يتحقق من بريده الإلكتروني",
                'action' => 'عرض المستخدمين',
                'link' => '/admin/users',
            ];
        }

        // Check system health
        if (!$this->isSystemHealthy()) {
            $issues[] = [
                'severity' => 'error',
                'title' => 'تحذير صحة النظام',
                'message' => 'قد يواجه النظام بعض المشاكل',
                'action' => 'التفاصيل',
                'link' => '/admin/system-health',
            ];
        }

        return $issues;
    }

    /**
     * Get system performance metrics
     */
    private function getSystemPerformance(): array
    {
        return [
            'database_size' => $this->getDatabaseSize(),
            'active_users' => $this->getActiveUsersCount(),
            'system_health' => $this->getSystemHealth(),
            'cache_status' => $this->getCacheStatus(),
            'api_response_time' => $this->getAverageResponseTime(),
        ];
    }

    /**
     * Get database size
     */
    private function getDatabaseSize(): string
    {
        $size = DB::select('SELECT SUM(data_length) as size FROM INFORMATION_SCHEMA.TABLES')[0]->size ?? 0;
        return $this->formatBytes($size);
    }

    /**
     * Get active users count
     */
    private function getActiveUsersCount(): int
    {
        return User::where('updated_at', '>=', Carbon::now()->subHours(1))->count();
    }

    /**
     * Get system health score
     */
    private function getSystemHealth(): int
    {
        $health = 100;

        // Check database
        if (!$this->isDatabaseHealthy()) $health -= 20;

        // Check storage
        if (!$this->isStorageHealthy()) $health -= 15;

        // Check pending tasks
        if ($this->hasPendingTasks()) $health -= 10;

        return max(0, $health);
    }

    /**
     * Get cache status
     */
    private function getCacheStatus(): array
    {
        try {
            cache()->put('health_check', true, 1);
            $status = cache()->get('health_check');

            return [
                'status' => $status ? 'working' : 'failing',
                'message' => $status ? 'Cache working properly' : 'Cache issues detected',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get average API response time
     */
    private function getAverageResponseTime(): string
    {
        // This would require middleware tracking
        return 'N/A';
    }

    /**
     * Check if database is healthy
     */
    private function isDatabaseHealthy(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if storage is healthy
     */
    private function isStorageHealthy(): bool
    {
        $path = storage_path();
        return is_writable($path);
    }

    /**
     * Check if there are pending tasks
     */
    private function hasPendingTasks(): bool
    {
        return Order::where('status', 'pending')->exists();
    }

    /**
     * Check system health
     */
    private function isSystemHealthy(): bool
    {
        return $this->isDatabaseHealthy() && $this->isStorageHealthy();
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get system logs
     */
    public function getLogs(Request $request)
    {
        $type = $request->query('type', 'all');
        $limit = $request->query('limit', 50);

        $logPath = storage_path('logs/laravel.log');
        if (!file_exists($logPath)) {
            return response()->json(['logs' => []]);
        }

        $logs = [];
        $lines = array_reverse(file($logPath));

        foreach ($lines as $line) {
            if ($type === 'all' || strpos($line, $type) !== false) {
                $logs[] = trim($line);
                if (count($logs) >= $limit) break;
            }
        }

        return response()->json([
            'logs' => $logs,
            'total' => count($logs),
        ]);
    }

    /**
     * Export full system database
     */
    public function exportDatabase(Request $request)
    {
        try {
            $filename = 'system_backup_' . date('Y-m-d_H-i-s') . '.sql';

            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');

            $tmpFile = storage_path('exports/' . $filename);
            if (!is_dir(storage_path('exports'))) {
                mkdir(storage_path('exports'), 0755, true);
            }

            $command = sprintf(
                'mysqldump -h %s -u %s -p%s %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbUser),
                escapeshellarg($dbPassword),
                escapeshellarg($dbName),
                escapeshellarg($tmpFile)
            );

            Log::info('Full system database export requested', [
                'user_id' => auth()->id(),
                'timestamp' => now(),
            ]);

            if (file_exists($tmpFile)) {
                return response()->download($tmpFile, $filename)->deleteFileAfterSend(true);
            }

            return response()->json(['error' => 'Export failed'], 500);
        } catch (\Exception $e) {
            Log::error('Database export failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
