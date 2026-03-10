<?php

namespace App\Services\Profile;

use App\Repositories\ProfileRepository;
use App\Services\Service;
use App\Util\Constants;

class ProfileService extends Service
{
    private $repository;
    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        $profiles = $this->repository->all();
        $status = empty($profiles) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $profiles, $status);
    }

    public function find($profile_id)
    {
        $profile = $this->repository->find($profile_id);
        $status = empty($profile) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $profile, $status);
    }

    public function findBy($column, $value)
    {
        $profile = $this->repository->findBy($column, $value);
        $status = empty($profile) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $profile, $status);
    }

    public function findByAll($colum, $value)
    {
        $profiles = $this->repository->findByAll($colum, $value);
        $status = empty($profiles) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $profiles, $status);
    }
}
