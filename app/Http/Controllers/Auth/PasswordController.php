<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerificationCode;
use App\Traits\VerificationTraits;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class PasswordController extends Controller
{
    use VerificationTraits;

    //send password reset code
    public function sendResetPasswordCode(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
        $user = User::where('email', $data['email'])->first();

        //send password reset mail
        $this->sendVerificationCode($data['email'], 'password', $user);
        return ApiResponse::successResponse('Password reset code sent', Response::HTTP_OK);
    }


    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        $verifyCode = $this->verifyCode($data['code'], $data['email']);
        $code = VerificationCode::where('verifiable', $data['email'])->first();

        if (!$user) {
            return ApiResponse::errorResponse('Invalid Credentials!', Response::HTTP_NOT_FOUND);
        }
        if (!$verifyCode) {
            return ApiResponse::errorResponse('Invalid code!', Response::HTTP_NOT_FOUND);
        }
        if ($code->expires_at < now()) {
            return ApiResponse::errorResponse('Verification code expired!', Response::HTTP_UNAUTHORIZED);
        }

        $user->update([
            'password' => Hash::make($data['password'])
        ]);
        $user->save();

        $code->delete();
        return ApiResponse::successResponse('Password reset was successful', Response::HTTP_OK);
    }
}
