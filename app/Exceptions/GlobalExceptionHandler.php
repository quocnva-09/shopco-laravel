<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
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
    use ApiResponseTrait;

    public static function register(Exceptions $exceptions): void
    {
        $handler = new self;

        $exceptions->render(function (AuthenticationException $exception) use ($handler) {
            return $handler->errorResponse('Unauthenticated', Response::HTTP_UNAUTHORIZED);
        });

        $exceptions->render(function (AccessDeniedHttpException $exception) use ($handler) {
            return $handler->errorResponse('Forbidden', Response::HTTP_FORBIDDEN);
        });

        $exceptions->render(function (NotFoundHttpException $exception) use ($handler) {
            return $handler->errorResponse($exception->getMessage() ?: 'Not Found', Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function (ValidationException $exception) use ($handler) {
            return $handler->errorResponse(
                'Validation Error',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $exception->validator->getMessageBag()
            );
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception) use ($handler) {
            return $handler->errorResponse($exception->getMessage() ?: 'Method Not Allowed', Response::HTTP_METHOD_NOT_ALLOWED);
        });

        $exceptions->render(function (BadMethodCallException $exception) use ($handler) {
            return $handler->errorResponse($exception->getMessage() ?: 'Bad Method Call', Response::HTTP_METHOD_NOT_ALLOWED);
        });

        $exceptions->render(function (ModelNotFoundException $exception) use ($handler) {
            return $handler->errorResponse($exception->getMessage() ?: 'Resource Not Found', Response::HTTP_NOT_FOUND);
        });

        // Catch-all handler for any unhandled exceptions
        $exceptions->render(function (Exception $exception) use ($handler) {
            return $handler->errorResponse($exception->getMessage() ?: 'Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}
