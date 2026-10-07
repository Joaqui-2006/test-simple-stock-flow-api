<?php

declare(strict_types=1);

namespace App\Presentation\Http\ProblemDetails;

use App\Application\Exception\ConcurrencyConflict;
use App\Domain\Exception\BusinessRuleViolation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class ProblemDetailsRenderer
{
    public static function render(Throwable $e): JsonResponse|Response
    {
        // Regla: 401, 403, 404, 405 -> cuerpo vacÃ­o con Content-Length: 0
        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            if (in_array($status, [401, 403, 404, 405], true)) {
                return response('', $status, array_merge($e->getHeaders(), ['Content-Length' => '0']));
            }
        }

        // 422: Invariantes y reglas de negocio del dominio
        if ($e instanceof BusinessRuleViolation) {
            return response()->json([
                'type' => 'about:blank',
                'title' => 'Unprocessable Entity',
                'status' => 422,
                'detail' => $e->getMessage(),
            ], 422, ['Content-Type' => 'application/problem+json']);
        }

        // 409: Concurrencia optimista
        if ($e instanceof ConcurrencyConflict) {
            return response()->json([
                'type' => 'about:blank',
                'title' => 'Conflict',
                'status' => 409,
                'detail' => $e->getMessage(),
            ], 409, ['Content-Type' => 'application/problem+json']);
        }

        // 400: Error de validaciÃ³n de formato
        if ($e instanceof ValidationException) {
            return response()->json([
                'type' => 'about:blank',
                'title' => 'Bad Request',
                'status' => 400,
                'detail' => 'La solicitud contiene errores de validaciÃ³n de formato',
                'errors' => $e->errors(),
            ], 400, ['Content-Type' => 'application/problem+json']);
        }

        // 500: ExcepciÃ³n no controlada (no filtrar detalles internos)
        return response()->json([
            'type' => 'about:blank',
            'title' => 'Internal Server Error',
            'status' => 500,
            'detail' => 'Ha ocurrido un error interno en el servidor',
        ], 500, ['Content-Type' => 'application/problem+json']);
    }
}