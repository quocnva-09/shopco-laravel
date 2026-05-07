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
    ) {
    }

    public function index(UserFilterRequest $request)
    {
        $filterDTO = UserFilterDTO::fromRequest($request);
        $users = $this->userService->getAllUsers($filterDTO);

        $resource = UserResource::collection($users)->response()->getData(true);

        return response()->json([
            'data' => $resource['data'],
            'meta' => $resource['meta'],
            'links' => $resource['links'],
            'message' => 'Users retrieved successfully',
            'status' => Response::HTTP_OK
        ]);
    }

    public function store(UserRequest $request)
    {
        $dto = UserDTO::fromRequest($request);
        $user = $this->userService->createUser($dto);

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'User created successfully',
            'status' => Response::HTTP_CREATED,
        ]);
    }

    public function show(int $id)
    {
        $user = $this->userService->getUserById($id);

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'User retrieved successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function update(UserRequest $request, int $id)
    {
        $dto = UserDTO::fromRequest($request);
        $user = $this->userService->updateUser($id, $dto);

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'User updated successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function destroy(int $id)
    {
        $this->userService->deleteUser($id);

        return response()->json([
            'message' => 'User deleted successfully',
            'status' => Response::HTTP_OK,
        ]);
    }
}
