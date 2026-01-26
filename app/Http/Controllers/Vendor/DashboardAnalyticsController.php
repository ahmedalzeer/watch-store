<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardAnalyticsController extends Controller
{
    /**
     * Get comprehensive dashboard statistics
     */
    public function getStatistics(Request $request, Store $store = null)
    {
        // If store parameter not provided, get from request or user's first store
        if (!$store) {
            $storeId = $request->query('store_id') ?? auth()->user()->stores()->first()?->id;
            $store = Store::findOrFail($storeId);
        }

        // Verify authorization
        if ($store->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized store access'], 403);
        }

        $period = $request->query('period', '30'); // days

        return response()->json([
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'subdomain' => $store->subdomain,
                'is_active' => $store->is_active,
            ],
            'statistics' => $this->calculateStatistics($store, $period),
            'charts' => $this->getChartData($store, $period),
            'recent_activities' => $this->getRecentActivities($store),
            'issues' => $this->getStoreIssues($store),
            'performance' => $this->getPerformanceMetrics($store),
        ]);
    }

    /**
     * Calculate store statistics
     */
    private function calculateStatistics(Store $store, $period): array
    {
        $fromDate = Carbon::now()->subDays($period);

        $totalOrders = Order::whereHas('items', function ($query) use ($store) {
            $query->where('store_id', $store->id);
        })->count();

        $totalRevenue = OrderItem::where('store_id', $store->id)
            ->where('created_at', '>=', $fromDate)
            ->sum(DB::raw('quantity * price'));

        $totalProducts = Product::where('store_id', $store->id)->count();

        $activeProducts = Product::where('store_id', $store->id)
            ->where('is_active', true)
            ->count();

        $totalReviews = ProductReview::whereHas('product', function ($query) use ($store) {
            $query->where('store_id', $store->id);
        })->count();

        $averageRating = ProductReview::whereHas('product', function ($query) use ($store) {
            $query->where('store_id', $store->id);
        })->avg('rating') ?? 0;

        $pendingOrders = Order::whereHas('items', function ($query) use ($store) {
            $query->where('store_id', $store->id);
        })->where('status', 'pending')->count();

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => number_format($totalRevenue, 2),
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'inactive_products' => $totalProducts - $activeProducts,
            'total_reviews' => $totalReviews,
            'average_rating' => number_format($averageRating, 1),
            'pending_orders' => $pendingOrders,
            'conversion_rate' => $this->calculateConversionRate($store),
        ];
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData(Store $store, $period): array
    {
        $fromDate = Carbon::now()->subDays($period);
        $dates = collect();

        for ($i = $period; $i >= 0; $i--) {
            $dates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        // Revenue chart
        $revenueData = [];
        foreach ($dates as $date) {
            $revenue = OrderItem::where('store_id', $store->id)
                ->whereDate('created_at', $date)
                ->sum(DB::raw('quantity * price'));
            $revenueData[] = (float)$revenue;
        }

        // Orders chart
        $ordersData = [];
        foreach ($dates as $date) {
            $count = Order::whereHas('items', function ($query) use ($store) {
                $query->where('store_id', $store->id);
            })->whereDate('created_at', $date)->count();
            $ordersData[] = $count;
        }

        // Product performance
        $topProducts = Product::where('store_id', $store->id)
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get(['id', 'name', 'orders_count']);

        // Category distribution
        $categoryDistribution = Product::where('store_id', $store->id)
            ->groupBy('category_id')
            ->selectRaw('category_id, COUNT(*) as count')
            ->with('category')
            ->get();

        return [
            'revenue_chart' => [
                'labels' => $dates->map(fn($d) => date('m/d', strtotime($d)))->toArray(),
                'data' => $revenueData,
            ],
            'orders_chart' => [
                'labels' => $dates->map(fn($d) => date('m/d', strtotime($d)))->toArray(),
                'data' => $ordersData,
            ],
            'top_products' => $topProducts,
            'category_distribution' => $categoryDistribution,
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities(Store $store): array
    {
        $recentOrders = Order::whereHas('items', function ($query) use ($store) {
            $query->where('store_id', $store->id);
        })
            ->latest()
            ->limit(5)
            ->get(['id', 'status', 'created_at']);

        $recentReviews = ProductReview::whereHas('product', function ($query) use ($store) {
            $query->where('store_id', $store->id);
        })
            ->latest()
            ->limit(5)
            ->with('product')
            ->get(['id', 'product_id', 'rating', 'comment', 'created_at']);

        $recentProducts = Product::where('store_id', $store->id)
            ->latest()
            ->limit(5)
            ->get(['id', 'name', 'is_active', 'created_at']);

        return [
            'recent_orders' => $recentOrders,
            'recent_reviews' => $recentReviews,
            'recent_products' => $recentProducts,
        ];
    }

    /**
     * Get store issues and warnings
     */
    private function getStoreIssues(Store $store): array
    {
        $issues = [];

        // Check for inactive products
        $inactiveCount = Product::where('store_id', $store->id)
            ->where('is_active', false)
            ->count();
        if ($inactiveCount > 0) {
            $issues[] = [
                'severity' => 'warning',
                'title' => 'منتجات غير نشطة',
                'message' => "لديك {$inactiveCount} منتج غير نشط",
                'action' => 'عرض المنتجات',
                'link' => '/vendor/products',
            ];
        }

        // Check for pending orders
        $pendingCount = Order::whereHas('items', function ($query) use ($store) {
            $query->where('store_id', $store->id);
        })->where('status', 'pending')->count();
        if ($pendingCount > 0) {
            $issues[] = [
                'severity' => 'info',
                'title' => 'طلبيات قيد الانتظار',
                'message' => "لديك {$pendingCount} طلبية تحتاج معالجة",
                'action' => 'عرض الطلبيات',
                'link' => '/vendor/orders',
            ];
        }

        // Check for low reviews
        $avgRating = ProductReview::whereHas('product', function ($query) use ($store) {
            $query->where('store_id', $store->id);
        })->avg('rating');
        if ($avgRating && $avgRating < 3) {
            $issues[] = [
                'severity' => 'error',
                'title' => 'تقييم منخفض',
                'message' => 'متوسط التقييمات منخفض: ' . number_format($avgRating, 1),
                'action' => 'عرض التقييمات',
                'link' => '/vendor/reviews',
            ];
        }

        // Check store status
        if (!$store->is_active) {
            $issues[] = [
                'severity' => 'error',
                'title' => 'المتجر معطل',
                'message' => 'المتجر حالياً غير نشط',
                'action' => 'تفعيل المتجر',
                'link' => '/vendor/settings',
            ];
        }

        return $issues;
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(Store $store): array
    {
        $totalProducts = Product::where('store_id', $store->id)->count();
        $productsWithVariants = Product::where('store_id', $store->id)
            ->has('variants')
            ->count();
        $productsWithImages = Product::where('store_id', $store->id)
            ->whereHas('media')
            ->count();
        $productsWithReviews = Product::where('store_id', $store->id)
            ->has('reviews')
            ->count();

        return [
            'product_completion' => $totalProducts > 0
                ? round((($productsWithVariants + $productsWithImages + $productsWithReviews) / ($totalProducts * 3)) * 100, 1)
                : 0,
            'products_with_variants' => $productsWithVariants,
            'products_with_images' => $productsWithImages,
            'products_with_reviews' => $productsWithReviews,
            'seo_optimization' => $this->calculateSEOScore($store),
        ];
    }

    /**
     * Calculate conversion rate
     */
    private function calculateConversionRate(Store $store): string
    {
        $visitors = 0; // You need to implement visitor tracking
        $orders = Order::whereHas('items', function ($query) use ($store) {
            $query->where('store_id', $store->id);
        })->count();

        return $visitors > 0 ? number_format(($orders / $visitors) * 100, 2) : '0.00';
    }

    /**
     * Calculate SEO score
     */
    private function calculateSEOScore(Store $store): array
    {
        $score = 0;
        $checks = [];

        // Check store SEO
        if ($store->seo_title) {
            $score += 15;
            $checks[] = ['name' => 'عنوان SEO', 'passed' => true];
        } else {
            $checks[] = ['name' => 'عنوان SEO', 'passed' => false];
        }

        if ($store->seo_description) {
            $score += 15;
            $checks[] = ['name' => 'وصف SEO', 'passed' => true];
        } else {
            $checks[] = ['name' => 'وصف SEO', 'passed' => false];
        }

        // Check products with SEO
        $totalProducts = Product::where('store_id', $store->id)->count();
        if ($totalProducts > 0) {
            $productsWithSEO = Product::where('store_id', $store->id)
                ->where(function ($query) {
                    $query->whereNotNull('meta_title')
                        ->orWhereNotNull('meta_description');
                })->count();

            $seoPercentage = ($productsWithSEO / $totalProducts) * 70;
            $score += $seoPercentage;
            $checks[] = ['name' => 'SEO المنتجات', 'passed' => $seoPercentage >= 35];
        }

        return [
            'total_score' => min(100, round($score)),
            'checks' => $checks,
        ];
    }

    /**
     * Get system logs for store
     */
    public function getLogs(Request $request, Store $store = null)
    {
        // If store parameter not provided, get from request
        if (!$store) {
            $storeId = $request->query('store_id');
            $store = Store::findOrFail($storeId);
        }

        // Verify authorization
        if ($store->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $type = $request->query('type', 'all');
        $limit = $request->query('limit', 50);

        // Read log file
        $logPath = storage_path('logs/laravel.log');
        if (!file_exists($logPath)) {
            return response()->json(['logs' => []]);
        }

        $logs = [];
        $lines = array_reverse(file($logPath));

        foreach ($lines as $line) {
            if (strpos($line, "store_id={$store->id}") !== false || $request->query('type') === 'all') {
                if ($type === 'all' || strpos($line, $type) !== false) {
                    $logs[] = trim($line);
                    if (count($logs) >= $limit) break;
                }
            }
        }

        return response()->json([
            'logs' => $logs,
            'total' => count($logs),
        ]);
    }

    /**
     * Export store database
     */
    public function exportDatabase(Request $request, Store $store = null)
    {
        // If store parameter not provided, get from request
        if (!$store) {
            $storeId = $request->query('store_id');
            $store = Store::findOrFail($storeId);
        }

        // Verify authorization
        if ($store->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $filename = 'store_' . $store->subdomain . '_' . date('Y-m-d_H-i-s') . '.sql';

            // Use mysqldump if available
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');

            // Create a temporary file for the dump
            $tmpFile = storage_path('exports/' . $filename);
            if (!is_dir(storage_path('exports'))) {
                mkdir(storage_path('exports'), 0755, true);
            }

            // Execute mysqldump command
            $command = sprintf(
                'mysqldump -h %s -u %s -p%s %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbUser),
                escapeshellarg($dbPassword),
                escapeshellarg($dbName),
                escapeshellarg($tmpFile)
            );

            // Log the export request
            Log::info('Store database export requested', [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'user_id' => auth()->id(),
                'timestamp' => now(),
            ]);

            if (file_exists($tmpFile)) {
                return response()->download($tmpFile, $filename)->deleteFileAfterSend(true);
            }

            return response()->json([
                'error' => 'Failed to create backup',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Database export failed', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'An error occurred while exporting the database',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get database migration status
     */
    public function getMigrationStatus(Request $request, Store $store = null)
    {
        // If store parameter not provided, get from request
        if (!$store) {
            $storeId = $request->query('store_id');
            $store = Store::findOrFail($storeId);
        }

        // Verify authorization
        if ($store->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'store_id' => $store->id,
            'store_name' => $store->name,
            'database_size' => $this->getDatabaseSize($store),
            'product_count' => Product::where('store_id', $store->id)->count(),
            'order_count' => Order::whereHas('items', function ($q) use ($store) {
                $q->where('store_id', $store->id);
            })->count(),
            'storage_used' => $this->getStorageUsed($store),
            'last_backup' => $this->getLastBackup($store),
        ]);
    }

    /**
     * Get database size
     */
    private function getDatabaseSize(Store $store): string
    {
        $size = 0;

        // Calculate tables related to store
        $tables = [
            'products' => Product::where('store_id', $store->id)->count(),
            'orders' => Order::whereHas('items', function ($q) use ($store) {
                $q->where('store_id', $store->id);
            })->count(),
            'media' => $this->getStoreMediaCount($store),
        ];

        return json_encode($tables);
    }

    /**
     * Get store media count
     */
    private function getStoreMediaCount(Store $store): int
    {
        return Product::where('store_id', $store->id)
            ->withCount('media')
            ->sum('media_count');
    }

    /**
     * Get storage used
     */
    private function getStorageUsed(Store $store): string
    {
        $storageUsed = 0;

        // Sum media files
        $products = Product::where('store_id', $store->id)->with('media')->get();
        foreach ($products as $product) {
            foreach ($product->media as $media) {
                $path = storage_path('app/' . $media->id . '/' . $media->file_name);
                if (file_exists($path)) {
                    $storageUsed += filesize($path);
                }
            }
        }

        return $this->formatBytes($storageUsed);
    }

    /**
     * Get last backup
     */
    private function getLastBackup(Store $store): ?string
    {
        $backupDir = storage_path('exports');
        $pattern = 'store_' . $store->subdomain . '_*.sql';

        $files = glob($backupDir . '/' . $pattern);
        if (!empty($files)) {
            usort($files, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            return date('Y-m-d H:i:s', filemtime($files[0]));
        }

        return null;
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
}
