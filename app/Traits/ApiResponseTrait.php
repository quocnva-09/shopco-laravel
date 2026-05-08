<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    protected function successResponse(mixed $data = [], string $message = 'Success', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, mixed $errors = null): JsonResponse
    {
        $response = [
            'status' => $statusCode,
            'message' => $message,
        ];

        if (! is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    protected function paginatedResponse(ResourceCollection $resourceCollection, string $message = 'Success', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $response = $resourceCollection->response()->getData(true);

        return response()->json([
            'status' => $statusCode,
            'message' => $message,
            'data' => $response['data'] ?? [],
            'meta' => $response['meta'] ?? null,
            'links' => $response['links'] ?? null,
        ], $statusCode);
    }
}
