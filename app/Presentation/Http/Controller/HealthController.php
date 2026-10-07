<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

final class HealthController
{
    public function check(): JsonResponse
    {
        $dbStatus = 'connected';
        try {
            DB::select('SELECT 1');
        } catch (Throwable) {
            $dbStatus = 'disconnected';
        }

        return response()->json([
            'status' => $dbStatus === 'connected' ? 'pass' : 'fail',
            'database' => $dbStatus,
        ], 200);
    }
}