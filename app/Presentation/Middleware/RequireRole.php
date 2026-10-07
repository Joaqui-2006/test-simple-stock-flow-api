<?php

declare(strict_types=1);

namespace App\Presentation\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequireRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $userRole = (string) $request->attributes->get('auth_role', '');
        if ($userRole !== $role) {
            return response('', 403, ['Content-Length' => '0']);
        }

        return $next($request);
    }
}