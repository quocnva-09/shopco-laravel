<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use App\DTOs\UserDTO;
use App\DTOs\UserFilterDTO;
use App\Http\Requests\UserFilterRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService
    ) {}

    public function index(UserFilterRequest $request): JsonResponse
    {
        $filterDTO = UserFilterDTO::fromRequest($request);
        $users = $this->userService->getAllUsers($filterDTO);

        return $this->paginatedResponse(UserResource::collection($users), 'Users retrieved successfully');
    }

    public function store(UserRequest $request): JsonResponse
    {
        $dto = UserDTO::fromRequest($request);
        $user = $this->userService->createUser($dto);

        return $this->successResponse(new UserResource($user), 'User created successfully', Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        return $this->successResponse(new UserResource($user), 'User retrieved successfully');
    }

    public function update(UserRequest $request, int $id): JsonResponse
    {
        $dto = UserDTO::fromRequest($request);
        $user = $this->userService->updateUser($id, $dto);

        return $this->successResponse(new UserResource($user), 'User updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);

        return $this->successResponse(null, 'User deleted successfully');
    }
}
