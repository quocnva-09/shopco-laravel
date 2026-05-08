<?php

namespace App\Exceptions;

use BadMethodCallException;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GlobalExceptionHandler
{
    public static function register(Exceptions $exceptions)
    {
        $exceptions->render(function (AuthenticationException $exception) {
            return response()->json([
                'message' => 'Unauthenticated',
                'status' => Response::HTTP_UNAUTHORIZED,
            ], Response::HTTP_UNAUTHORIZED);
        });

        $exceptions->render(function (AccessDeniedHttpException $exception) {
            return response()->json([
                'message' => 'Forbidden',
                'status' => Response::HTTP_FORBIDDEN,
            ], Response::HTTP_FORBIDDEN);
        });

        $exceptions->render(function (NotFoundHttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage() ?? 'Not Found',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function (ValidationException $exception) {
            return response()->json([
                'message' => $exception->validator->getMessageBag() ?? 'Validation Error',
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage() ?? 'Method Not Allowed',
                'status' => Response::HTTP_METHOD_NOT_ALLOWED,
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        });

        $exceptions->render(function (BadMethodCallException $exception) {
            return response()->json([
                'message' => $exception->getMessage() ?? 'Bad Method Call',
                'status' => Response::HTTP_METHOD_NOT_ALLOWED,
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        });

        $exceptions->render(function (ModelNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage() ?? 'Resource Not Found',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        });

        // bắt exception tổng
        $exceptions->render(function (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage() ?? 'Internal Server Error',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}
