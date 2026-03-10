<?php

namespace App\Repositories;

use App\Models\Document;

class DocumentRepository extends Repository
{

    public function all()
    {
        return Document::all();
    }

    public function find($value)
    {
        return Document::find($value);
    }

    public function findBy($column, $value)
    {
        return Document::where($column, $value)->first();
    }

    public function findByAll($column, $value)
    {
        return Document::where($column, $value)->get();
    }

    public function findByAttributes($attributes)
    {
        $response = null;
        foreach ($attributes as $column => $value) {
            $response = $response == null ? Document::where($column, $value) : $response->where($column, $value);
        }
        return $response == null ? $response : $response->first();
    }

    public function findByAllAttributes($attributes)
    {
        $response = null;
        foreach ($attributes as $column => $value) {
            $response = $response == null ? Document::where($column, $value) : $response->where($column, $value);
        }
        return $response == null ? $response : $response->get();
    }
}
