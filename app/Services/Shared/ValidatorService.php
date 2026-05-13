<?php

namespace App\Services\Shared;

use App\Repositories\Repository;
use Illuminate\Support\Facades\Validator;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ValidatorService extends Service
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($request, $rules)
    {
        // Verificar si es una instancia de Request
        if ($request instanceof Request) {
            $validator = Validator::make($request->all(), $rules);
        } else {
            // Es una instancia de Model
            $validator = Validator::make($request, $rules);
        }

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return [];
    }
}
