<?php

namespace App\Services\User\UpdateUser;

use App\Repositories\UserRepository;
use App\Repositories\ProfileRepository;
use App\Models\User;
use App\Util\Constants;
use App\Services\Service;
use App\Services\Shared\ValidatorService;
use Illuminate\Validation\ValidationException;
use App\Services\Shared\EncryptorService;
use App\Services\Shared\ErrorResponseFormatter;
use App\Services\Shared\ValidatorPasswordService;
use App\Traits\LoadUserRelationshipsTrait;
use App\Util\UserConstants;
use Illuminate\Support\Facades\Log;

class UpdateUserService extends Service
{
    use LoadUserRelationshipsTrait;

    private $repository;
    private $validatorService;
    private $encryptorService;
    private $validatorPasswordService;
    private $errorResponseFormatter;
    private $profileRepository;

    public function __construct(
        UserRepository $repository,
        ProfileRepository $profileRepository,
        ValidatorService $validatorService,
        EncryptorService $encryptorService,
        ValidatorPasswordService $validatorPasswordService,
        ErrorResponseFormatter $errorResponseFormatter
    ) {
        $this->repository = $repository;
        $this->profileRepository = $profileRepository;
        $this->validatorService = $validatorService;
        $this->encryptorService = $encryptorService;
        $this->validatorPasswordService = $validatorPasswordService;
        $this->errorResponseFormatter = $errorResponseFormatter;
    }

    public function update($request, $id)
    {
        $model = $this->repository->find($id);
        if (!$model) {
            return $this->resolve(true, UserConstants::NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
        }
        $model->updated_by = auth()->user()->name;

        $model->fill($request->all());

        $targetProfileId = (int) ($model->profile_id ?? 0);
        $targetProfile = $targetProfileId > 0 ? $this->profileRepository->find($targetProfileId) : null;
        if (($targetProfile->code ?? null) !== 'SUPER') {
            $model->all_companies = false;
        } else {
            $model->all_companies = (bool) ($request->input('all_companies', $model->all_companies ?? false));
        }

        try {
            $rules = (new User())->getRulesCreate();
            $rules[UserConstants::PASSWORD] = 'nullable|string|min:6';
            $this->validatorService->validate($request, $rules);
        } catch (ValidationException $e) {
            $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
            return $this->resolve(true, $messageError, Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        $existingByName = $this->repository->findBy('name', $model->name);
        if ($existingByName && (int) $existingByName->user_id !== (int) $model->user_id) {
            return $this->resolve(true, 'El nombre de usuario ya existe.', Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }
        if ($model->email) {
            $existingByEmail = $this->repository->findBy('email', $model->email);
            if ($existingByEmail && (int) $existingByEmail->user_id !== (int) $model->user_id) {
                return $this->resolve(true, 'El correo ya existe.', Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
            }
        }

        $requestPassword = $request->input(UserConstants::PASSWORD);

        // Validar la contraseña proporcionada con la almacenada
        if ($requestPassword && !$this->validatorPasswordService->validatePassword($requestPassword, $model->password)) {
            $model->password = $this->encryptorService->encrypt($requestPassword);
        }

        try {
            $updated = $this->repository->updateMongo($model);

            if (!$updated) {
                return $this->resolve(true, UserConstants::NOT_UPDATED, $updated, Constants::CODE_BAD_REQUEST);
            }

            return $this->resolve(false, UserConstants::UPDATED, Constants::NOT_DATA, Constants::CODE_SUCCESS);
        } catch (\Throwable $e) {
            Log::error('UpdateUserService::update failed', [
                'user_id' => $id,
                'payload' => $request->all(),
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->resolve(true, UserConstants::NOT_UPDATED, Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }
}
