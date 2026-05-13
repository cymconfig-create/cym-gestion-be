<?php

namespace App\Services\Event\UpdateEvent;

use App\Repositories\EventRepository;
use App\Util\Constants;
use App\Services\Service;
use App\Services\Shared\ValidatorService;

class UpdateEventService extends Service
{
    private $repository;
    private $validatorService;

    public function __construct(
        EventRepository $repository,
        ValidatorService $validatorService
    ) {
        $this->repository = $repository;
        $this->validatorService = $validatorService;
    }

    public function update($request, $id)
    {
        // Validamos si existe el registro
        $model = $this->repository->find($id);

        if (!$model) {
            return $this->resolve(true, Constants::OBJECT_NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        $model->fill($request->all());
        $errors = $this->validatorService->validate($request, $model->rulesCreate);

        if (count($errors) > 0) {
            return $this->resolve(true, Constants::NOT_CREATED, reset($errors), Constants::CODE_BAD_REQUEST);
        }

        $update = $this->repository->updateMongo($model);

        if ($update) {
            return $this->resolve(false, Constants::CREATED);
        } else {
            return $this->resolve(true, Constants::NOT_CREATED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }
    }
}
