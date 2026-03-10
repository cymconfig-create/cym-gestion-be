<?php

namespace App\Services\User\DeleteUser;

use App\Repositories\UserRepository;
use App\Util\Constants;
use App\Services\Service;
use App\Util\UserConstants;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class DeleteUserService extends Service
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function delete($user_id)
    {
        // Validamos si existe el registro
        $model = $this->repository->find($user_id);

        if (!$model) {
            return $this->resolve(true, UserConstants::NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        try {
            $deleted = $this->repository->delete($model);

            if ($deleted) {
                return $this->resolve(false, UserConstants::DELETED, Constants::NOT_DATA, Constants::CODE_SUCCESS);
            }

            return $this->resolve(true, UserConstants::NOT_DELETED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->resolve(true, $e->getMessage(), Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }
}
