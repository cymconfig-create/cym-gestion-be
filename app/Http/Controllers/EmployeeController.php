<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Employee\EmployeeService;
use App\Services\Employee\CreateEmployee\CreateEmployeeService;
use App\Services\Employee\UpdateEmployee\UpdateEmployeeService;
use App\Services\Employee\DeleteEmployee\DeleteEmployeeService;

class EmployeeController extends Controller
{
    private $createEmployeeService;
    private $updateEmployeeService;
    private $deleteEmployeeService;
    private $employeeService;

    public function __construct(
        EmployeeService $employeeService,
        CreateEmployeeService $createEmployeeService,
        UpdateEmployeeService $updateEmployeeService,
        DeleteEmployeeService $deleteEmployeeService
    ) {
        $this->createEmployeeService = $createEmployeeService;
        $this->updateEmployeeService = $updateEmployeeService;
        $this->deleteEmployeeService = $deleteEmployeeService;
        $this->employeeService = $employeeService;
    }

    public function all()
    {
        return $this->employeeService->all();
    }

    public function find($employee_id)
    {
        return $this->employeeService->find($employee_id);
    }

    public function findBy($colum, $employee_id)
    {
        return $this->employeeService->findBy($colum, $employee_id);
    }

    public function findByAll($colum, $value)
    {
        return $this->employeeService->findByAll($colum, $value);
    }

    public function create(Request $request)
    {
        return $this->createEmployeeService->create($request);
    }

    public function update(Request $request, $employee_id)
    {
        return $this->updateEmployeeService->update($request, $employee_id);
    }

    public function delete($employee_id)
    {
        return $this->deleteEmployeeService->delete($employee_id);
    }
}
