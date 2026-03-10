<?php

namespace App\Repositories;

use App\Models\Profile;

class ProfileRepository extends Repository
{

    public function all()
    {
        return Profile::getFilteredProfiles();
    }

    public function find($value)
    {
        $profile = Profile::find($value);
        $resources = $profile->resources;

        return $resources;
    }

    public function findBy($column, $value)
    {
        return Profile::where($column, $value)->first();
    }

    public function findByAll($column, $value)
    {
        return Profile::where($column, $value)->get();
    }

    public function findByAttributes($attributes)
    {
        $response = null;
        foreach ($attributes as $column => $value) {
            $response = $response == null ? Profile::where($column, $value) : $response->where($column, $value);
        }
        return $response == null ? $response : $response->first();
    }

    public function findByAllAttributes($attributes)
    {
        $response = null;
        foreach ($attributes as $column => $value) {
            $response = $response == null ? Profile::where($column, $value) : $response->where($column, $value);
        }
        return $response == null ? $response : $response->get();
    }

    function findMenusByProfile($profile_id)
    {
        $menus = Profile::with(['menus.sub_menus'])
            ->where('profile_id', $profile_id)
            ->get();

        return $menus;
    }
}
