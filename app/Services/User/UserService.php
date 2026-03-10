<?php

namespace App\Services\User;

use App\Repositories\UserRepository;
use App\Services\Service;
use App\Util\Constants;
use App\Traits\LoadUserRelationshipsTrait;

class UserService extends Service
{
    use LoadUserRelationshipsTrait;

    private $repository;
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        $users = $this->repository->all();
        $users = $this->loadUserRelationships($users);
        $status = empty($users) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $users, $status);
    }

    public function find($id)
    {
        $user = $this->repository->find($id);
        $user = $this->loadUserRelationships($user);
        $status = empty($user) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $user, $status);
    }

    public function findBy($column, $id)
    {
        $user = $this->repository->findBy($column, $id);
        $user = $this->loadUserRelationships($user);
        $status = empty($user) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $user, $status);
    }

    public function findByAll($colum, $value)
    {
        $users = $this->repository->findByAll($colum, $value);
        $users = $this->loadUserRelationships($users);
        $status = empty($users) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $users, $status);
    }
}
