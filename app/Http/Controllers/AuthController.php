<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AuthServiceInterface;
use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $dto = LoginDTO::fromRequest($request);

        try {
            $authData = $this->authService->login($dto);

            return $this->successResponse([
                'user' => new UserResource($authData['user']),
                'access_token' => $authData['token'],
                'token_type' => 'Bearer',
            ], 'Login successfully', Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function register(RegisterRequest $request)
    {
        $dto = RegisterDTO::fromRequest($request);
        $authData = $this->authService->register($dto);

        return $this->successResponse([
            'user' => new UserResource($authData['user']),
            'access_token' => $authData['token'],
            'token_type' => 'Bearer',
        ], 'Register successfully', Response::HTTP_CREATED);
    }

    public function logout()
    {
        $isLogout = $this->authService->logout();
        if ($isLogout) {
            return $this->successResponse($isLogout, 'Logout successfully', Response::HTTP_OK);
        }

        return $this->errorResponse('Logout failed', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getMyInfo()
    {
        $user = $this->authService->getMyInfo();

        return $this->successResponse(new UserResource($user), 'My info fetched successfully', Response::HTTP_OK);
    }
}
