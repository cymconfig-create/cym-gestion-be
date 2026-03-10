<?php

namespace App\Services\User\Auth;

use App\Services\Service;
use App\Util\AuthConstants;
use App\Util\Constants;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService extends Service
{
    private $validatorPermissionsService;

    public function __construct(ValidatorPermissionsService $validatorPermissionsService)
    {
        $this->validatorPermissionsService = $validatorPermissionsService;
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only(AuthConstants::NAME, AuthConstants::PASSWORD);

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->resolve(true, AuthConstants::CREDENTIALS_INVALID, null, Constants::CODE_UNAUTHORIZED);
        }

        return $this->resolve(false, AuthConstants::TOKEN, $token, Constants::CODE_SUCCESS);
    }

    public function me()
    {
        $user = JWTAuth::parseToken()->authenticate()->load(AuthConstants::PROFILE, AuthConstants::EMPLOYEE); // Corrected here
        $menu_profile = $this->validatorPermissionsService->findMenusByProfile($user->profile_id);

        $data = array(
            AuthConstants::USER => $user,
            AuthConstants::MENUS => $menu_profile
        );

        return $this->resolve(false, AuthConstants::INFO_ME, $data, Constants::CODE_SUCCESS);
    }
}
