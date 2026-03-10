<?php

namespace App\Repositories;

use App\Models\Attachment;

class AttachmentRepository extends Repository
{

    public function all()
    {
        return Attachment::all();
    }

    public function find($value)
    {
        return Attachment::find($value);
    }

    public function findBy($column, $value)
    {
        return Attachment::where($column, $value)->first();
    }

    public function findByAll($column, $value)
    {
        $query = Attachment::query();

        // Comprobamos si el valor que viene es la cadena "null"
        if ($value === 'null') {
            $query->whereNull($column);
        } else {
            $query->where($column, $value);
        }

        return $query->get();
    }

    public function findByAttributes($attributes)
    {
        $response = null;
        foreach ($attributes as $column => $value) {
            $response = $response == null ? Attachment::where($column, $value) : $response->where($column, $value);
        }
        return $response == null ? $response : $response->first();
    }

    public function findByAllAttributes($attributes)
    {
        $response = null;
        foreach ($attributes as $column => $value) {
            $response = $response == null ? Attachment::where($column, $value) : $response->where($column, $value);
        }
        return $response == null ? $response : $response->get();
    }
}
