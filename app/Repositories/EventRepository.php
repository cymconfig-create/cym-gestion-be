<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository extends Repository
{

    public function all()
    {
        return Event::all();
    }

    public function find($value)
    {
        return Event::find($value);
    }

    public function findBy($column, $value)
    {
        return Event::where($column, $value)->first();
    }

    public function findByAll($column, $value)
    {
        return Event::where($column, $value)->get();
    }

    public function findByAttributes($attributes)
    {
        $response = null;
        foreach ($attributes as $column => $value) {
            $response = $response == null ? Event::where($column, $value) : $response->where($column, $value);
        }
        return $response == null ? $response : $response->first();
    }

    public function findByAllAttributes($attributes)
    {
        $response = null;
        foreach ($attributes as $column => $value) {
            $response = $response == null ? Event::where($column, $value) : $response->where($column, $value);
        }
        return $response == null ? $response : $response->get();
    }
}
