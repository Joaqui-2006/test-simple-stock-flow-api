<?php

declare(strict_types=1);

namespace App\Presentation\Middleware;

use App\Application\Ports\Outbound\TokenGenerator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthenticateToken
{
    public function __construct(
        private readonly TokenGenerator $tokenGenerator
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');
        if (!str_starts_with($header, 'Bearer ')) {
            return response('', 401, ['WWW-Authenticate' => 'Bearer', 'Content-Length' => '0']);
        }

        $token = substr($header, 7);
        $payload = $this->tokenGenerator->validate($token);
        if ($payload === null) {
            return response('', 401, ['WWW-Authenticate' => 'Bearer', 'Content-Length' => '0']);
        }

        $request->attributes->set('auth_user_id', $payload['sub'] ?? '');
        $request->attributes->set('auth_username', $payload['username'] ?? '');
        $request->attributes->set('auth_role', $payload['role'] ?? '');

        return $next($request);
    }
}