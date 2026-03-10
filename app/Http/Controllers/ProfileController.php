<?php

namespace App\Http\Controllers;

use App\Services\Profile\ProfileService;

class ProfileController extends Controller
{

    private $profileService;

    public function __construct(
        ProfileService $profileService
    ) {
        $this->profileService = $profileService;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function all()
    {
        return $this->profileService->all();
    }

    public function find($profile_id)
    {
        return $this->profileService->find($profile_id);
    }

    public function findBy($colum, $profile_id)
    {
        return $this->profileService->findBy($colum, $profile_id);
    }

    public function findByAll($colum, $value)
    {
        return $this->profileService->findByAll($colum, $value);
    }
}
