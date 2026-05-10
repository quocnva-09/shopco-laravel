<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
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

        $paginator = $resourceCollection->resource;

        if (! $paginator instanceof LengthAwarePaginator) {
            return $this->successResponse($resourceCollection, $message, $statusCode);
        }

        return response()->json([
            'status' => $statusCode,
            'message' => $message,
            'data' => $resourceCollection,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last'  => $paginator->url($paginator->lastPage()),
                'prev'  => $paginator->previousPageUrl(),
                'next'  => $paginator->nextPageUrl(),
            ],
        ], $statusCode);
    }
}
