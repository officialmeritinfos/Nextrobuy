<?php

use App\Http\Controllers\Account\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['json.response']], function () {

    // Unauthenticated Routes
    Route::group(['prefix' => 'auth', 'middleware' => ['json.response']], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('verify-email', [VerificationController::class, 'verifyEmail']);
        Route::post('resend-verify-code', [VerificationController::class, 'resendVerifyCode']);
        Route::post('request-reset-password', [PasswordController::class, 'sendResetPasswordCode']);
        Route::post('reset-password', [PasswordController::class, 'resetPassword']);
        Route::get('redirect/{provider}', [AuthController::class, 'redirectSocial'])->where('provider', 'google|facebook');
        Route::get('callback/{provider}', [AuthController::class, 'callbackSocial'])->where('provider', 'google|facebook');
    });

    //authenticated routes
    Route::group(['prefix' => 'account', 'middleware' => ['auth:api', 'json.response', 'isverified', 'active']], function () {
        Route::get('my-profile', [UserController::class, 'getProfile']);
        Route::get('user-profile/{user}', [UserController::class, 'getUserProfile']);
        Route::post('change-password', [UserController::class, 'changePassword']);
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::post('update-bank-info', [UserController::class, 'updateBankInfo']);
    });
});
