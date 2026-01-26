<?php

/**
 * Slow Queries monitoring script
 * Logs all queries that take a long time to execute
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Enable query logging
DB::enableQueryLog();

// Log all queries
$slow_queries = [];
$start_time = microtime(true);

DB::listen(function ($query) use (&$slow_queries) {
    $time = $query->time; // Time in milliseconds

    // Log queries taking more than 100ms
    if ($time > 100) {
        $slow_queries[] = [
            'query' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $time,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        echo "\n⚠️  SLOW QUERY ({$time}ms):\n";
        echo "SQL: " . $query->sql . "\n";
        echo "Bindings: " . json_encode($query->bindings) . "\n";
    }
});

// Example: Simulate test operations
echo "🔍 Starting query monitoring...\n";
echo "Logging any query taking more than 100ms\n\n";

// Test some endpoints
$user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)';
$endpoints = [
    'products',
    'products?page=1',
    'products?search=watch',
    'api/products',
];

foreach ($endpoints as $endpoint) {
    echo "Testing: /$endpoint\n";

    try {
        // This is just an example - in reality you should make HTTP requests
        // to test the actual endpoints

    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Print report
echo "\n" . str_repeat("=", 80) . "\n";
echo "📊 Slow Queries Report\n";
echo str_repeat("=", 80) . "\n";

if (empty($slow_queries)) {
    echo "✅ No slow queries found!\n";
} else {
    echo "\nNumber of slow queries: " . count($slow_queries) . "\n\n";

    // Sort by time
    usort($slow_queries, function ($a, $b) {
        return $b['time'] <=> $a['time'];
    });

    foreach ($slow_queries as $index => $query) {
        echo ($index + 1) . ". Time: {$query['time']}ms\n";
        echo "   SQL: {$query['query']}\n";
        if (!empty($query['bindings'])) {
            echo "   Bindings: " . json_encode($query['bindings']) . "\n";
        }
        echo "\n";
    }
}

// Optimization recommendations
echo "\n💡 Recommendations:\n";
echo str_repeat("-", 80) . "\n";

$has_slow = !empty($slow_queries);

if ($has_slow) {
    echo "1. Use EXPLAIN to understand the query:\n";
    echo "   EXPLAIN {$slow_queries[0]['query']}\n\n";

    echo "2. Check indexes:\n";
    echo "   SELECT * FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = 'your_db';\n\n";

    echo "3. Use Laravel Debugbar to monitor queries:\n";
    echo "   composer require barryvdh/laravel-debugbar --dev\n\n";

    echo "4. Possible improvements:\n";
    echo "   - Use eager loading: with() in queries\n";
    echo "   - Add indexes to fields used in WHERE and JOIN\n";
    echo "   - Use pagination for large results\n";
    echo "   - Use select() to choose only required fields\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
