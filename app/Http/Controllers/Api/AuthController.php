<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\AuthServiceInterface;
use App\DTOs\Auth\ForgetPasswordDTO;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\VerifyOtpDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
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
                response: Response::HTTP_OK,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
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
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')
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
            ], __('response.auth.login_successful'), Response::HTTP_OK);
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
                response: Response::HTTP_CREATED,
                description: 'Registration successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_CREATED),
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
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Validation Error')
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
        ], __('response.auth.registration_successful'), Response::HTTP_CREATED);
    }

    #[OA\Post(
        path: '/api/logout',
        summary: 'User Logout',
        description: 'Revoke the current user access token.',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Logout successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Logout successfully'),
                        new OA\Property(property: 'data', type: 'boolean', example: true)
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthenticated'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal Server Error')
        ]
    )]
    public function logout()
    {
        $isLogout = $this->authService->logout();
        if ($isLogout) {
            return $this->successResponse($isLogout, __('response.auth.logout_successful'), Response::HTTP_OK);
        }

        return $this->errorResponse(__('exception.logout_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[OA\Post(
        path: '/api/logout/all',
        summary: 'Logout All Devices',
        description: 'Revoke ALL Sanctum tokens for the authenticated user. '
        . 'This signs the user out of every device and browser simultaneously. '
        . 'Use this when an account is suspected to be compromised.',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'All sessions revoked',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Logged out from all devices successfully'
                        ),
                        new OA\Property(property: 'data', type: 'boolean', example: true)
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthenticated'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal Server Error')
        ]
    )]
    public function logoutAll()
    {
        $result = $this->authService->logoutAllDevices();

        if ($result) {
            return $this->successResponse(
                $result,
                __('response.auth.logout_all_successful'),
                Response::HTTP_OK
            );
        }

        return $this->errorResponse(__('exception.logout_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[OA\Get(
        path: '/api/me',
        summary: 'Get My Info',
        description: 'Get information about the currently authenticated user.',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'My info fetched successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserResource')
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthenticated')
        ]
    )]
    public function getMyInfo()
    {
        $user = $this->authService->getMyInfo();

        return $this->successResponse(new UserResource($user), __('response.auth.profile_retrieved'), Response::HTTP_OK);
    }

    #[OA\Post(
        path: '/api/forget-password',
        summary: 'Forget Password',
        description: 'Forget password',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: '#/components/schemas/ForgetPasswordRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'The OTP Code has been sent to your email'),
                        new OA\Property(property: 'data', type: 'boolean', example: true)
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal Server Error')
        ]
    )]
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $dto = ForgetPasswordDTO::fromRequest($request);

        try {
            $result = $this->authService->forgetPassword($dto);

            if ($result) {
                return $this->successResponse(
                    $result,
                    __('response.auth.password_reset_otp_sent'),
                    Response::HTTP_OK
                );
            }

            return $this->errorResponse(__('exception.forget_password_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Post(
        path: '/api/verify-otp',
        summary: 'Verify OTP',
        description: 'Verify OTP',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: '#/components/schemas/VerifyOtpRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Verify otp successfully'),
                        new OA\Property(property: 'data', type: 'boolean', example: true)
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal Server Error')
        ]
    )]
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $dto = VerifyOtpDTO::fromRequest($request);

        try {
            $result = $this->authService->verifyOtp($dto);

            if ($result) {
                return $this->successResponse(
                    $result,
                    __('response.auth.otp_verified'),
                    Response::HTTP_OK
                );
            }

            return $this->errorResponse(__('exception.verify_otp_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    // public function resetPassword(ResetPasswordRequest $request)
    // {
    //     $dto = ResetPasswordDTO::fromRequest($request);
    //     $result = $this->authService->resetPassword($dto);

    //     if ($result) {
    //         return $this->successResponse(
    //             $result,
    //             'Reset password successfully',
    //             Response::HTTP_OK
    //         );
    //     }

    //     return $this->errorResponse('Reset password failed', Response::HTTP_INTERNAL_SERVER_ERROR);
    // }
}
