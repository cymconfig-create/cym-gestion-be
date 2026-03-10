<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository extends Repository
{

    public function all()
    {
        return Employee::all();
    }

    public function find($value)
    {
        return Employee::find($value);
    }

    public function findBy($column, $value)
    {
        return Employee::where($column, $value)->first();
    }

    public function findByAll($column, $value)
    {
        return Employee::where($column, $value)->get();
    }

    public function findByAttributes($attributes)
    {
        $response = Employee::query();

        foreach ($attributes as $column => $value) {
            $response = $response->where($column, $value);
        }

        return $response->first();
    }

    public function findByAllAttributes($attributes)
    {
        $response = Employee::query();

        foreach ($attributes as $column => $value) {
            $response = $response->where($column, $value);
        }

        return $response->get();
    }
}
