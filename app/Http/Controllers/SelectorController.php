<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Selector\SelectorService;
use App\Services\Domain\CreateDomainService;
use App\Services\Domain\UpdateDomainService;

class SelectorController extends Controller
{
    private $service;
    private $createDomainService;
    private $updateDomainService;

    public function __construct(
        SelectorService $service
    ) {
        $this->service = $service;
    }

    public function all()
    {
        return $this->service->all();
    }
}
