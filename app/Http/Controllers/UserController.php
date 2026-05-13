<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User\UserService;
use App\Services\User\CreateUser\CreateUserService;
use App\Services\User\UpdateUser\UpdateUserService;
use App\Services\User\DeleteUser\DeleteUserService;

class UserController extends Controller
{
    private $createUserService;
    private $updateUserService;
    private $deleteUserService;
    private $userService;

    public function __construct(
        UserService $userService,
        CreateUserService $createUserService,
        UpdateUserService $updateUserService,
        DeleteUserService $deleteUserService
    ) {
        $this->createUserService = $createUserService;
        $this->updateUserService = $updateUserService;
        $this->deleteUserService = $deleteUserService;
        $this->userService = $userService;
    }

    public function all()
    {
        return $this->userService->all();
    }

    public function find($user_id)
    {
        return $this->userService->find($user_id);
    }

    public function findBy($colum, $user_id)
    {
        return $this->userService->findBy($colum, $user_id);
    }

    public function findByAll($colum, $value)
    {
        return $this->userService->findByAll($colum, $value);
    }

    public function create(Request $request)
    {
        return $this->createUserService->create($request);
    }

    public function update(Request $request, $user_id)
    {
        return $this->updateUserService->update($request, $user_id);
    }

    public function delete($user_id)
    {
        return $this->deleteUserService->delete($user_id);
    }
}
