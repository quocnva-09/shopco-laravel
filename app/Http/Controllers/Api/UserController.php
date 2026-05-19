<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\UserServiceInterface;
use App\DTOs\User\UserDTO;
use App\DTOs\User\UserFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserFilterRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService
    ) {
    }

    #[OA\Get(
        path: '/api/admin/users',
        summary: 'List users',
        security: [['bearerAuth' => []]],
        tags: ['User Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'perPage', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Users retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Users retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/UserResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta')
                    ]
                )
            )
        ]
    )]
    public function index(UserFilterRequest $request): JsonResponse
    {
        $filterDTO = UserFilterDTO::fromRequest($request);
        $users = $this->userService->getAllUsers($filterDTO);

        return $this->paginatedResponse(UserResource::collection($users), __('response.user.list_retrieved'));
    }

    #[OA\Post(
        path: '/api/admin/users',
        summary: 'Create a new user',
        security: [['bearerAuth' => []]],
        tags: ['User Module - Admin'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserRequest')
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'User created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'User created successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserResource')
                    ]
                )
            )
        ]
    )]
    public function store(UserRequest $request): JsonResponse
    {
        $dto = UserDTO::fromRequest($request);
        $user = $this->userService->createUser($dto);

        return $this->successResponse(new UserResource($user), __('response.user.created'), Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/admin/users/{id}',
        summary: 'Get a single user',
        security: [['bearerAuth' => []]],
        tags: ['User Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'User retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'User retrieved successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserResource')
                    ]
                )
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        return $this->successResponse(new UserResource($user), __('response.user.retrieved'));
    }

    #[OA\Put(
        path: '/api/users/{id}',
        summary: 'Update a user',
        security: [['bearerAuth' => []]],
        tags: ['User Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateUserRequest')
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'User updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'User updated successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserResource')
                    ]
                )
            )
        ]
    )]
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $dto = UserDTO::fromRequest($request);
        $user = $this->userService->updateUser($id, $dto);

        return $this->successResponse(new UserResource($user), __('response.user.updated'));
    }

    #[OA\Delete(
        path: '/api/admin/users/{id}',
        summary: 'Delete a user',
        security: [['bearerAuth' => []]],
        tags: ['User Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'User deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'User deleted successfully'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
                    ]
                )
            )
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);

        return $this->successResponse(null, __('response.user.deleted'));
    }

    #[OA\Get(
        path: '/api/admin/users/trashed',
        summary: 'List trashed users',
        security: [['bearerAuth' => []]],
        tags: ['User Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'perPage', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Trashed users retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Trashed users retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/UserResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta')
                    ]
                )
            )
        ]
    )]
    public function trashed(UserFilterRequest $request): JsonResponse
    {
        $filterDTO = UserFilterDTO::fromRequest($request);
        $users = $this->userService->getTrashed($filterDTO);

        return $this->paginatedResponse(UserResource::collection($users), __('response.user.trashed_retrieved'));
    }

    #[OA\Patch(
        path: '/api/admin/users/{id}/restore',
        summary: 'Restore a trashed user',
        security: [['bearerAuth' => []]],
        tags: ['User Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'User restored successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'User restored successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserResource')
                    ]
                )
            )
        ]
    )]
    public function restore(int $id): JsonResponse
    {
        $user = $this->userService->restore($id);

        return $this->successResponse(new UserResource($user), __('response.user.restored'));
    }

    #[OA\Delete(
        path: '/api/admin/users/{id}/force-delete',
        summary: 'Permanently delete a user',
        security: [['bearerAuth' => []]],
        tags: ['User Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'User permanently deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'User permanently deleted successfully'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
                    ]
                )
            )
        ]
    )]
    public function forceDelete(int $id): JsonResponse
    {
        $this->userService->forceDelete($id);

        return $this->successResponse(null, __('response.user.force_deleted'));
    }
}
