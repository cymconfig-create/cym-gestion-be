<?php

namespace App\Services\User\Auth;

use App\Repositories\ProfileRepository;
use App\Services\Service;

class ValidatorPermissionsService extends Service
{
    private $repository;

    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findMenusByProfile($profile_id)
    {
        return $this->repository->findMenusByProfile($profile_id);
    }

    public function findProfileByUser($profile_id)
    {
        return $this->repository->find($profile_id);
    }
}
