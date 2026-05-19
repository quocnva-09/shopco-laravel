<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\DTOs\Auth\ForgetPasswordDTO;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\VerifyOtpDTO;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        protected readonly UserRepositoryInterface $userRepo,
    ) {
    }

    public function register(RegisterDTO $dto): array
    {
        $user = $this->userRepo->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'password' => bcrypt($dto->password, ['rounds' => 10]),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(LoginDTO $dto): array
    {
        $user = $this->userRepo->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new Exception('Invalid credentials');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return (bool) $user->currentAccessToken()->delete();
    }

    public function logoutAllDevices(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return (bool) $user->tokens()->delete();
    }

    public function getMyInfo(): ?User
    {
        return Auth::user();
    }

    public function forgetPassword(ForgetPasswordDTO $dto): bool
    {
        $user = $this->userRepo->findByEmail($dto->email);

        if (!$user) {
            throw new Exception('User not found');
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put($dto->email, $otp, 60);

        Mail::to($dto->email)->send(new OtpMail($otp));
        return true;
    }

    public function verifyOtp(VerifyOtpDTO $dto): bool
    {
        $otp = Cache::get($dto->email);

        if (!$otp || (string) $otp !== $dto->otp) {
            throw new Exception('Invalid OTP');
        }

        Cache::forget($dto->email);

        $user = $this->userRepo->findByEmail($dto->email);

        if (!$user) {
            throw new Exception('User not found');
        }

        if ($dto->type === VerifyOtpRequest::VERIFY_TYPES['verify_forget_password']) {
            $user->password = Hash::make($dto->password);
            $user->save();
        } elseif ($dto->type === VerifyOtpRequest::VERIFY_TYPES['verify_register']) {
            $user->email_verified_at = Carbon::now();
            $user->save();
        }

        return true;
    }
}
