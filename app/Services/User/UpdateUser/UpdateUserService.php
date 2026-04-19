<?php

namespace App\Services\User\UpdateUser;

use App\Repositories\UserRepository;
use App\Util\Constants;
use App\Services\Service;
use App\Services\Shared\ValidatorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\Shared\EncryptorService;
use App\Services\Shared\ErrorResponseFormatter;
use App\Services\Shared\ValidatorPasswordService;
use App\Traits\LoadUserRelationshipsTrait;
use App\Util\UserConstants;

class UpdateUserService extends Service
{
    use LoadUserRelationshipsTrait;

    private $repository;
    private $validatorService;
    private $encryptorService;
    private $validatorPasswordService;
    private $errorResponseFormatter;

    public function __construct(
        UserRepository $repository,
        ValidatorService $validatorService,
        EncryptorService $encryptorService,
        ValidatorPasswordService $validatorPasswordService,
        ErrorResponseFormatter $errorResponseFormatter
    ) {
        $this->repository = $repository;
        $this->validatorService = $validatorService;
        $this->encryptorService = $encryptorService;
        $this->validatorPasswordService = $validatorPasswordService;
        $this->errorResponseFormatter = $errorResponseFormatter;
    }

    public function update($request, $id)
    {
        // Validamos si existe el registro
        $model = $this->repository->find($id);
        $model->updated_by = auth()->user()->name;

        if (!$model) {
            return $this->resolve(true, UserConstants::NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        $model->fill($request->all());

        try {
            $this->validatorService->validate($request, $model->getRulesCreate());
        } catch (ValidationException $e) {
            $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
            return $this->resolve(true, $messageError, Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        $requestPassword = $request->input(UserConstants::PASSWORD);

        // Validar la contraseña proporcionada con la almacenada
        if ($requestPassword && !$this->validatorPasswordService->validatePassword($requestPassword, $model->password)) {
            $model->password = $this->encryptorService->encrypt($requestPassword);
        }

        try {
            DB::beginTransaction();

            $updated = $this->repository->update($model);

            if (!$updated) {
                DB::rollBack();
                return $this->resolve(true, UserConstants::NOT_UPDATED, $updated, Constants::CODE_BAD_REQUEST);
            }

            $userId = $model->user_id;

            DB::commit();

            $user = $this->repository->find($userId);
            $this->loadUserRelationships($user);
            return $this->resolve(false, UserConstants::UPDATED, Constants::NOT_DATA, Constants::CODE_SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resolve(true, UserConstants::NOT_UPDATED, Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }
}
