<?php

namespace App\Services\Employee;

use App\Repositories\EmployeeRepository;
use App\Services\Service;
use App\Util\Constants;
use App\Traits\LoadEmployeeRelationshipsTrait;

class EmployeeService extends Service
{
    use LoadEmployeeRelationshipsTrait;

    private $repository;

    public function __construct(EmployeeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        $employees = $this->repository->all();
        $status = empty($employees) ? Constants::CODE_SUCCESS_NO_CONTENT : null;
        $this->loadEmployeeRelationships($employees);

        return $this->resolve(false, Constants::NOT_MESSAGE, $employees, $status);
    }

    public function find($id)
    {
        $employee = $this->repository->find($id);
        $status = empty($employee) ? Constants::CODE_SUCCESS_NO_CONTENT : null;
        $this->loadEmployeeRelationships($employee);

        return $this->resolve(false, Constants::NOT_MESSAGE, $employee, $status);
    }

    public function findBy($column, $id)
    {
        $employee = $this->repository->findBy($column, $id);
        $status = empty($employee) ? Constants::CODE_SUCCESS_NO_CONTENT : null;
        $this->loadEmployeeRelationships($employee);

        return $this->resolve(false, Constants::NOT_MESSAGE, $employee, $status);
    }

    public function findByAll($colum, $value)
    {
        $employees = $this->repository->findByAll($colum, $value);
        $status = empty($employees) ? Constants::CODE_SUCCESS_NO_CONTENT : null;
        $this->loadEmployeeRelationships($employees);

        return $this->resolve(false, Constants::NOT_MESSAGE, $employees, $status);
    }
}
