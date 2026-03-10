<?php

namespace App\Services\Event\CreateEvent;

use App\Repositories\EventRepository;
use App\Util\Constants;
use App\Models\Event;
use App\Services\Service;
use App\Services\Shared\ValidatorService;

class CreateEventService extends Service
{
    private $repository;
    private $validatorService;

    public function __construct(
        EventRepository $repository,
        ValidatorService $validatorService  // Inyecta el servicio de validación en el constructor
    ) {
        $this->repository = $repository;
        $this->validatorService = $validatorService;  // Inicializa el servicio de validación
    }

    public function create($request)
    {
        $model = new Event();
        $model->fill($request->all());

        $errors = $this->validatorService->validate($request, $model->rulesCreate);

        if (count($errors) > 0) {
            return $this->resolve(true, Constants::ERROR_VALIDATING, reset($errors), Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        $save = $this->repository->save($model);

        if (reset($save)) {
            return $this->resolve(false, Constants::CREATED, '', Constants::CODE_CREATED);
        } else {
            return $this->resolve(true, Constants::NOT_CREATED, end($save), Constants::CODE_BAD_REQUEST);
        }
    }
}
