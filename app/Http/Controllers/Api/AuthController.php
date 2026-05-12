<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\AuthServiceInterface;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    #[OA\Post(
        path: '/api/login',
        summary: 'User Login',
        description: 'Authenticate a user and return an access token.',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Login successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'user', ref: '#/components/schemas/UserResource'),
                                new OA\Property(property: 'access_token', type: 'string', example: '1|xyz...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer')
                            ],
                            type: 'object'
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
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

    #[OA\Post(
        path: '/api/register',
        summary: 'User Registration',
        description: 'Register a new user and return an access token.',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RegisterRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Registration successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Register successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'user', ref: '#/components/schemas/UserResource'),
                                new OA\Property(property: 'access_token', type: 'string', example: '2|abc...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer')
                            ],
                            type: 'object'
                        )
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation Error')
        ]
    )]
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

    #[OA\Post(
        path: '/api/logout',
        summary: 'User Logout',
        description: 'Revoke the current user access token.',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Logout successfully'),
                        new OA\Property(property: 'data', type: 'boolean', example: true)
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 500, description: 'Internal Server Error')
        ]
    )]
    public function logout()
    {
        $isLogout = $this->authService->logout();
        if ($isLogout) {
            return $this->successResponse($isLogout, 'Logout successfully', Response::HTTP_OK);
        }

        return $this->errorResponse('Logout failed', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[OA\Get(
        path: '/api/me',
        summary: 'Get My Info',
        description: 'Get information about the currently authenticated user.',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'My info fetched successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserResource')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function getMyInfo()
    {
        $user = $this->authService->getMyInfo();

        return $this->successResponse(new UserResource($user), 'My info fetched successfully', Response::HTTP_OK);
    }
}
