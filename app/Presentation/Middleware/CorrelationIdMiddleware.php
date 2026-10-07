<?php

declare(strict_types=1);

namespace App\Presentation\Middleware;

use App\Infrastructure\Logging\CorrelationId;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CorrelationIdMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $cid = $request->header('X-Correlation-ID');
        if ($cid !== null && trim($cid) !== '') {
            CorrelationId::set($cid);
        }

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set('X-Correlation-ID', CorrelationId::get());

        return $response;
    }
}