<?php

namespace App\Services\Shared;

use App\Services\Service;

class ValidatorPasswordService extends Service
{
    public function validatePassword($requestPassword, $storedEncryptedPassword)
    {
        // Comparar las contraseñas cifradas directamente usando password_verify (para contraseñas cifradas con bcrypt)
        return password_verify($requestPassword, $storedEncryptedPassword);
    }
}
