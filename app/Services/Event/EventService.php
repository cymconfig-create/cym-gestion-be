<?php

namespace App\Services\Event;

use App\Repositories\EventRepository;
use App\Services\Service;
use App\Util\Constants;

class EventService extends Service
{
    private $repository;
    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        $events = $this->repository->all();
        $status = empty($events) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $events, $status);
    }

    public function find($id)
    {
        $event = $this->repository->find($id);
        $status = empty($event) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $event, $status);
    }

    public function findBy($column, $id)
    {
        $event = $this->repository->findBy($column, $id);
        $status = empty($event) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $event, $status);
    }

    public function findByAll($colum, $value)
    {
        $events = $this->repository->findByAll($colum, $value);
        $status = empty($events) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $events, $status);
    }
}
