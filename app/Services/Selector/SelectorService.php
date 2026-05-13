<?php

namespace App\Services\Selector;

use App\Repositories\SelectorRepository;
use App\Services\Service;
use App\Util\Constants;

class SelectorService extends Service
{
    private $repository;
    public function __construct(SelectorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        $selectors = $this->repository->all();
        $status = empty($selectors) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $selectors, $status);
    }
}
