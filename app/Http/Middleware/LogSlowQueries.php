<?php

/**
 * Middleware for logging slow queries and slow requests
 * Add in app/Http/Middleware/LogSlowQueries.php
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogSlowQueries
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);

        // Enable query logging
        DB::enableQueryLog();

        $response = $next($request);

        $time = (microtime(true) - $start) * 1000; // in milliseconds

        // Save queries
        $queries = DB::getQueryLog();
        $slow_queries = array_filter($queries, function ($q) {
            return $q['time'] > 50; // more than 50ms
        });

        if (!empty($slow_queries) || $time > 500) {
            Log::channel('performance')->warning('Slow Request Detected', [
                'path' => $request->path(),
                'method' => $request->method(),
                'response_time' => round($time, 2) . 'ms',
                'query_count' => count($queries),
                'slow_query_count' => count($slow_queries),
                'queries' => $slow_queries,
                'user_agent' => $request->header('User-Agent'),
            ]);
        }

        return $response;
    }
}
