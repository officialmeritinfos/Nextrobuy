<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLoginRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\GoogleAuthRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Models\BankInformation;
use App\Models\User;
use App\Models\VerificationCode;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Traits\VerificationTraits;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends Controller
{
    use VerificationTraits;

    //login function
    public function login(CreateLoginRequest $request)
    {
        $userData =  $request->validated();
        if (Auth::attempt($userData)) {
            $accessToken = Auth::user()->createToken('Auth Token')->accessToken;

            if (auth()->user()->is_active == 0) {
                return ApiResponse::errorResponse('Unauthorized!, Your account has been deactivated, please contact the administrator', Response::HTTP_UNAUTHORIZED);
            }
            if (auth()->user()->email_verified_at == NULL) {
                return ApiResponse::errorResponse('Account is Unverified!', Response::HTTP_UNAUTHORIZED);
            }
            $data = new UserResource(auth()->user());
            return ApiResponse::successResponseWithData($data, 'Login successful', Response::HTTP_OK, $accessToken);
        }

        return ApiResponse::errorResponse('Invalid Login credentials', Response::HTTP_UNAUTHORIZED);
    }


    //register function
    public function register(CreateUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        //store user function here
        $createUser = $this->storeUser($data);

        $userResource = new UserResource($createUser);
        $accessToken = $userResource->createToken('Auth Token')->accessToken;
        return ApiResponse::successResponseWithData($userResource, 'Registration was successful', Response::HTTP_CREATED, $accessToken);
    }


    public function redirectSocial($provider, GoogleAuthRequest $request)
    {
        $data = $request->validated();
        return Socialite::driver($provider)->stateless()
            ->with(['state' => 'role=' . $data['role'] . ':' . $data['application_name'] . ''])
            ->redirect();
    }

    public function callbackSocial(Request $request, $provider)
    {
        try {
            $access_token = Socialite::driver($provider)->getAccessTokenResponse($request->code);
            $providerUser = Socialite::driver($provider)->userFromToken($access_token['access_token']);

            $provider_id = $providerUser->id;
            $findUser = User::where('provider_id', $provider_id)->first();
            if ($findUser) {
                $userResource = new UserResource($findUser);
                $message = "Login was successfull";
            } else {
                //check if email already exists to avoid conflict
                $findEmail = User::where('email', $providerUser->getEmail())->first();
                if ($findEmail) {
                    return ApiResponse::errorResponse('Email has already been taken', Response::HTTP_CONFLICT);
                }

                //proceed to create an account for user if email doesn't exist already
                parse_str($request->state, $result);
                $explodeRole = explode(':', $result['role']);
                $explodeName = explode(' ', $providerUser->getName());
                $data = [
                    'firstname' => $explodeName[0],
                    'lastname' => $explodeName[1],
                    'email' => $providerUser->getEmail(),
                    'provider_id' => $provider_id,
                    'provider' => $provider,
                    'role' => $explodeRole[0],
                    'application_name' => $explodeRole[1],
                    'image' => $providerUser->getAvatar()
                ];
                //store user function here
                $createUser = $this->storeUser($data);
                $userResource = new UserResource($createUser);
                $message = "Registration was successfull";
            }

            $accessToken = $userResource->createToken('Auth Token')->accessToken;
            return ApiResponse::successResponseWithData($userResource, $message, Response::HTTP_CREATED, $accessToken);
        } catch (Exception $e) {
            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }


    private function storeUser($data)
    {
        $createUser = User::create($data);
        $createBankInfo = BankInformation::create(['user_id' => $createUser->id]);
        $createWallet = Wallet::create(['user_id' => $createUser->id]);

        //send verification mail
        $this->sendVerificationCode($data['email'], 'verification', $data['firstname']);
        return $createUser;
    }
}
