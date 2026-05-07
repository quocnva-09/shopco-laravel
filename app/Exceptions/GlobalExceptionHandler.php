<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;

class GlobalExceptionHandler
{
    public static function register(Exceptions $exceptions)
    {
        $exceptions->render(function (AuthenticationException $exception) {
            return response()->json([
                'message' => 'Unauthenticated',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        });

        $exceptions->render(function (AccessDeniedHttpException $exception) {
            return response()->json([
                'message' => 'Forbidden',
                'status' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        });
    }
}
