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
        $emailOrUsername = $request->input(AuthConstants::EMAIL);
        $password = $request->input(AuthConstants::PASSWORD);

        if (!$emailOrUsername || !$password) {
            return $this->resolve(true, AuthConstants::CREDENTIALS_INVALID, null, Constants::CODE_UNAUTHORIZED);
        }

        $credentials = [
            AuthConstants::EMAIL => $emailOrUsername,
            AuthConstants::PASSWORD => $password,
        ];

        if (!$token = JWTAuth::attempt($credentials)) {
            // Compatibilidad temporal con usuarios antiguos que aún autenticaban con "name".
            $fallbackCredentials = [
                AuthConstants::NAME => $emailOrUsername,
                AuthConstants::PASSWORD => $password,
            ];

            if (!$token = JWTAuth::attempt($fallbackCredentials)) {
                return $this->resolve(true, AuthConstants::CREDENTIALS_INVALID, null, Constants::CODE_UNAUTHORIZED);
            }
        }

        return $this->resolve(false, AuthConstants::TOKEN, $token, Constants::CODE_SUCCESS);
    }

    public function me()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $profile = $this->validatorPermissionsService->findProfileByUser((int) $user->profile_id);
        $user->profile = $profile;
        $menu_profile = $this->validatorPermissionsService->findMenusByProfile((int) $user->profile_id);

        $data = array(
            AuthConstants::USER => $user,
            AuthConstants::MENUS => $menu_profile
        );

        return $this->resolve(false, AuthConstants::INFO_ME, $data, Constants::CODE_SUCCESS);
    }
}
