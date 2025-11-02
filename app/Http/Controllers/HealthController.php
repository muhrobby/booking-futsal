<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    /**
     * Health check endpoint
     * Used by monitoring systems and load balancers
     */
    public function check(): JsonResponse
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'checks' => [],
        ];

        // Check database connection
        try {
            DB::connection()->getPDO();
            $status['checks']['database'] = 'ok';
        } catch (\Exception $e) {
            $status['checks']['database'] = 'error';
            $status['status'] = 'degraded';
        }

        // Check cache connection
        try {
            Cache::get('health-check-test');
            $status['checks']['cache'] = 'ok';
        } catch (\Exception $e) {
            $status['checks']['cache'] = 'warning';
        }

        // Check storage
        if (is_writable(storage_path())) {
            $status['checks']['storage'] = 'ok';
        } else {
            $status['checks']['storage'] = 'error';
            $status['status'] = 'degraded';
        }

        // Check config cached
        $status['checks']['config_cached'] = config_path('app.php') ? 'ok' : 'warning';

        $http_code = $status['status'] === 'healthy' ? 200 : 503;

        return response()->json($status, $http_code);
    }

    /**
     * Readiness probe - used by Kubernetes
     */
    public function ready(): JsonResponse
    {
        try {
            DB::connection()->getPDO();
            return response()->json(['status' => 'ready'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'not-ready', 'error' => $e->getMessage()], 503);
        }
    }

    /**
     * Liveness probe - used by Kubernetes
     */
    public function alive(): JsonResponse
    {
        return response()->json(['status' => 'alive'], 200);
    }
}
