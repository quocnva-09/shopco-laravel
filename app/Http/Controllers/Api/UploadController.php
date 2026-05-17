<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\Upload\FileUploadDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Upload\FileUploadRequest;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
    }

    #[OA\Post(
        path: '/api/admin/products/upload',
        summary: 'Upload a product image (Admin only)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['image'],
                    properties: [
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Image file (max 5MB, jpeg/png/jpg/webp)'),
                    ]
                )
            )
        ),
        tags: ['Upload Images'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Image uploaded successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Uploaded successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'img_path', type: 'string', example: 'products/xyz.jpg'),
                                new OA\Property(property: 'image_url', type: 'string', example: 'https://s3.amazonaws.com/bucket/products/xyz.jpg'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function uploadProductImage(FileUploadRequest $request): JsonResponse
    {
        $dto = FileUploadDTO::fromRequest($request, 'products');

        $path = $this->fileUploadService->upload($dto->file, $dto->type);

        if (!$path) {
            return $this->errorResponse('Upload failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->successResponse([
            'img_path' => $path,
            'image_url' => $this->fileUploadService->url($path),
        ], 'Uploaded successfully');
    }

    #[OA\Post(
        path: '/api/users/upload',
        summary: 'Upload a user profile image',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['image'],
                    properties: [
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Image file (max 5MB, jpeg/png/jpg/webp)'),
                    ]
                )
            )
        ),
        tags: ['Upload Images'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Image uploaded successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Uploaded successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'img_path', type: 'string', example: 'users/xyz.jpg'),
                                new OA\Property(property: 'image_url', type: 'string', example: 'https://s3.amazonaws.com/bucket/users/xyz.jpg'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function uploadUserImage(FileUploadRequest $request): JsonResponse
    {
        $dto = FileUploadDTO::fromRequest($request, 'users');

        $path = $this->fileUploadService->upload($dto->file, $dto->type);

        if (!$path) {
            return $this->errorResponse('Upload failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->successResponse([
            'img_path' => $path,
            'image_url' => $this->fileUploadService->url($path),
        ], 'Uploaded successfully');
    }
}
