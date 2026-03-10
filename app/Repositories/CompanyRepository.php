<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository extends Repository
{
    public function all()
    {
        return Company::all();
    }

    public function find($value)
    {
        return Company::find($value);
    }

    public function findBy($column, $value)
    {
        return Company::where($column, $value)->first();
    }

    public function findByAll($column, $value)
    {
        return Company::where($column, $value)->get();
    }

    public function findByAttributes($attributes, $relations = [])
    {
        $response = Company::query(); // Mejor usar query() para cadenas

        foreach ($attributes as $column => $value) {
            $response = $response->where($column, $value);
        }

        return $response->first();
    }

    public function findByAllAttributes($attributes)
    {
        $response = Company::query();

        foreach ($attributes as $column => $value) {
            $response = $response->where($column, $value);
        }

        return $response->get();
    }
}
