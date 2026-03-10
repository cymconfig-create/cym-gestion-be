<?php

namespace App\Services\Shared;

use App\Services\Service;

class EncryptorService extends Service
{
    public function encrypt($data)
    {
        if (!$data) {
            return;
        }
        return bcrypt($data);
    }
}
