<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateBankRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\BankInformation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    //get User Profile
    public function getProfile()
    {
        $user = User::where('id', auth()->user()->id)->first();
        $userResource = new UserResource($user);
        return ApiResponse::successResponseWithData($userResource, "User Profile retrieved", Response::HTTP_OK);
    }

    //get user's profile by ID
    public function getUserProfile(User $user)
    {
        $userResource = new UserResource($user);
        return ApiResponse::successResponseWithData($userResource, "User Profile retrieved", Response::HTTP_OK);
    }

    //change user password
    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::where('id', auth()->user()->id)->first();

        $user->update([
            'password' => Hash::make($data['password'])
        ]);
        $userResource = new UserResource($user);
        return ApiResponse::successResponseWithData($userResource, "Password updated successfully", Response::HTTP_OK);
    }

    //update user profile
    public function updateProfile(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        $user = User::where('id', auth()->user()->id)->first();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $data['image'] = pushFileToStorage($image, 'profile');
        }

        $user->update($data);
        $userResource = new UserResource($user);
        return ApiResponse::successResponseWithData($userResource, "Profile updated successfully", Response::HTTP_OK);
    }

    //update bank info
    public function updateBankInfo(UpdateBankRequest $request)
    {
        $data = $request->validated();
        $userBank = BankInformation::where('id', auth()->user()->id)->first();

        $userBank->update($data);
        $userResource = new UserResource(auth()->user());
        return ApiResponse::successResponseWithData($userResource, "Payment info Updated successfully", Response::HTTP_OK);
    }
}
