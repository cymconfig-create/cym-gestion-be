<?php

namespace App\Services\Event\DeleteEvent;

use App\Repositories\EventRepository;
use App\Util\Constants;
use App\Services\Service;

class DeleteEventService extends Service
{
    private $repository;
    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function delete($id)
    {
        // Validamos si existe el registro
        $model = $this->repository->find($id);

        if (!$model) {
            return $this->resolve(true, Constants::OBJECT_NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        $delete = $this->repository->delete($model);

        if (reset($delete)) {
            return $this->resolve(false, Constants::DELETED, '', Constants::CODE_SUCCESS);
        } else {
            return $this->resolve(true, Constants::NOT_DELETED, end($delete), Constants::CODE_BAD_REQUEST);
        }
    }
}
