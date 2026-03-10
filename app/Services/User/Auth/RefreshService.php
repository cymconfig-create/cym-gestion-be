<?php

namespace App\Services\User\Auth;

use App\Services\Service;
use App\Util\AuthConstants;
use App\Util\Constants;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshService extends Service
{
    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return $this->resolve(false, AuthConstants::TOKEN,  $token);
    }
}
