<?php

namespace App\Http\Controllers;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

use App\Contracts\AuthServiceInterface;

use App\Http\Resources\UserResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Response;
use Exception;


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

            return response()->json([
                'data' => [
                    'user' => new UserResource($authData['user']),
                    'access_token' => $authData['token'],
                    'token_type' => 'Bearer',
                ],
                'message' => 'Login successfully',
                'status' => Response::HTTP_OK,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'data' => null,
                'message' => $exception->getMessage(),
                'status' => Response::HTTP_UNAUTHORIZED,
            ]);
        }
    }

    public function register(RegisterRequest $request)
    {
        $dto = RegisterDTO::fromRequest($request);
        $authData = $this->authService->register($dto);

        return response()->json([
            'data' => [
                'user' => new UserResource($authData['user']),
                'access_token' => $authData['token'],
                'token_type' => 'Bearer',
            ],
            'message' => 'Register successfully',
            'status' => Response::HTTP_CREATED,
        ]);
    }

    public function logout(Request $request)
    {
        $isLogout = $this->authService->logout($request->user());

        return response()->json([
            'data' => $isLogout,
            'message' => $isLogout ? 'Logout successfully' : 'Logout failed',
            'status' => $isLogout ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);
    }
}
