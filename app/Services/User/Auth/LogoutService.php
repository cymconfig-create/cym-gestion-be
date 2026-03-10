<?php

namespace App\Services\User\Auth;

use App\Services\Service;
use App\Util\AuthConstants;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutService extends Service
{
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->resolve(false, AuthConstants::LOGOUT);
    }
}
