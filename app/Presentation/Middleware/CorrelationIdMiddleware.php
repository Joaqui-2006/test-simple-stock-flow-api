<?php

declare(strict_types=1);

namespace App\Presentation\Middleware;

use Closure;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class CorrelationIdMiddleware
{
    private static ?string $current = null;

    public static function get(): string
    {
        if (self::$current === null) {
            self::$current = Uuid::uuid4()->toString();
        }
        return self::$current;
    }

    public static function set(string $id): void
    {
        self::$current = $id;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $cid = $request->header('X-Correlation-ID');
        if ($cid !== null && trim($cid) !== '') {
            self::set($cid);
        } else {
            self::set(Uuid::uuid4()->toString());
        }

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set('X-Correlation-ID', self::get());

        return $response;
    }
}