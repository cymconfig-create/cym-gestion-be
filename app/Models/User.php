<?php

namespace App\Models;

use App\Auth\MongoUser;

class User extends MongoUser
{
    public function getRulesCreate(): array
    {
        return [
            'profile_id' => 'required|integer',
            'name' => 'required|string|max:16',
            'email' => 'nullable|email|max:64',
            'full_name' => 'nullable|string|max:128',
            'identification_number' => 'nullable|string|max:32',
            'password' => 'required|string|min:6',
            'status' => 'nullable|boolean',
            'employee_id' => 'nullable|integer',
            'all_companies' => 'nullable|boolean',
        ];
    }
}
