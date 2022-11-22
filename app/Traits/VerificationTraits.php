<?php

namespace App\Traits;

use App\Models\VerificationCode;
use App\Notifications\PasswordResetNotification;
use App\Notifications\VerificationCodeNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;

trait VerificationTraits
{
    public static function sendVerificationCode($email, $purpose, $firstname)
    {
        $code = random_int(100000, 999999);
        $deleteExistingCodes = VerificationCode::whereVerifiable($email)->delete();
        $hashedCode = Hash::make($code);

        $data = [
            'code' => $hashedCode,
            'verifiable' => $email,
            'expires_at' => Carbon::now()->addMinutes(5)->toDateTimeString()
        ];

        VerificationCode::create($data);

        // if ($purpose == 'verification') {
        //     $mail = [
        //         'subject' => 'Verify Email Address',
        //         'message' => ':code is your ' . env('APP_NAME') . ' verification code. DO NOT DISCLOSE',
        //         'code' => $code,
        //         'name' => $firstname
        //     ];
        //     Notification::route('mail', $email)->notify((new VerificationCodeNotification($mail)));
        // }

        // if ($purpose == 'password') {
        //     Notification::route('mail', $email)->notify((new PasswordResetNotification($firstname, $code)));
        // }

        // if ($purpose == 'device-verification') {
        //     $mail = [
        //         'subject' => 'Device Verification Code',
        //         'message' => 'Your device verification code: :code',
        //         'code' => $code,
        //         'name' => $firstname
        //     ];
        //     Notification::route('mail', $email)->notify((new VerificationCodeNotification($mail)));
        // }
    }

    public static function verifyCode($code, $email)
    {
        $getCode = VerificationCode::where('verifiable', $email)->first();
        if ($getCode) {
            $existingCode = $getCode->code;
            $correctCode = Hash::check($code, $existingCode);
            if ($correctCode) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
