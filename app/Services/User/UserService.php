<?php

namespace App\Services\User;

use App\Repositories\UserRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\ProfileRepository;
use App\Services\Service;
use App\Util\Constants;
use InvalidArgumentException;

class UserService extends Service
{
    private $repository;
    private $employeeRepository;
    private $companyRepository;
    private $profileRepository;

    public function __construct(
        UserRepository $repository,
        EmployeeRepository $employeeRepository,
        CompanyRepository $companyRepository,
        ProfileRepository $profileRepository
    )
    {
        $this->repository = $repository;
        $this->employeeRepository = $employeeRepository;
        $this->companyRepository = $companyRepository;
        $this->profileRepository = $profileRepository;
    }

    public function all()
    {
        $users = $this->repository->all();
        $users = $users->map(fn ($user) => $this->enrichUser($user));
        $status = empty($users) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $users, $status);
    }

    public function find($id)
    {
        $user = $this->repository->find($id);
        $user = $this->enrichUser($user);
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

        $user = $this->enrichUser($user);
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

        $users = $users->map(fn ($user) => $this->enrichUser($user));
        $status = empty($users) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $users, $status);
    }

    private function enrichUser($user)
    {
        if (!$user) {
            return $user;
        }

        $employeeId = (int) ($user->employee_id ?? 0);
        if ($employeeId > 0) {
            $employee = $this->employeeRepository->find($employeeId);
            if ($employee) {
                $companyId = (int) ($employee->company_id ?? 0);
                if ($companyId > 0) {
                    $employee->company = $this->companyRepository->find($companyId);
                }
            }
            $user->employee = $employee;
        } else {
            $user->employee = null;
        }

        $profileId = (int) ($user->profile_id ?? 0);
        $user->profile = $profileId > 0 ? $this->profileRepository->find($profileId) : null;

        return $user;
    }
}
