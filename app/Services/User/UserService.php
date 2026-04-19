<?php

namespace App\Services\User;

use App\Repositories\UserRepository;
use App\Services\Service;
use App\Util\Constants;
use App\Traits\LoadUserRelationshipsTrait;
use InvalidArgumentException;

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
        try {
            $user = $this->repository->findBy($column, $id);
        } catch (InvalidArgumentException $e) {
            return $this->resolve(true, $e->getMessage(), Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        $user = $this->loadUserRelationships($user);
        $status = empty($user) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $user, $status);
    }

    public function findByAll($colum, $value)
    {
        try {
            $users = $this->repository->findByAll($colum, $value);
        } catch (InvalidArgumentException $e) {
            return $this->resolve(true, $e->getMessage(), Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        $users = $this->loadUserRelationships($users);
        $status = empty($users) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $users, $status);
    }
}
