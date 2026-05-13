<?php

namespace App\Services\User\CreateUser;

use App\Repositories\UserRepository;
use App\Repositories\ProfileRepository;
use App\Util\Constants;
use App\Models\User;
use App\Services\Service;
use App\Services\Shared\EncryptorService;
use App\Services\Shared\ValidatorService;
use Illuminate\Validation\ValidationException;
use App\Services\Employee\EmployeeService;
use App\Services\Shared\ErrorResponseFormatter;
use App\Util\UserConstants;
use Illuminate\Support\Facades\Log;

class CreateUserService extends Service
{
    private $repository;
    private $validatorService;
    private $encryptorService;
    private $employeeService;
    private $errorResponseFormatter;
    private $profileRepository;

    public function __construct(
        UserRepository $repository,
        ProfileRepository $profileRepository,
        ValidatorService $validatorService,
        EncryptorService $encryptorService,
        EmployeeService $employeeService,
        ErrorResponseFormatter $errorResponseFormatter
    ) {
        $this->repository = $repository;
        $this->profileRepository = $profileRepository;
        $this->validatorService = $validatorService;
        $this->encryptorService = $encryptorService;
        $this->employeeService = $employeeService;
        $this->errorResponseFormatter = $errorResponseFormatter;
    }

    public function create($request)
    {
        $employeeId = $request->input(UserConstants::EMPLOYEE_ID);
        $employeeData = null;
        if (!empty($employeeId)) {
            // Validar si el 'employee_id' ya tiene un usuario asociado
            $employeeResponse = $this->employeeService->find($employeeId);
            $employeeData = $employeeResponse->getOriginalContent();

            // Verifica si la respuesta es exitosa y si contiene datos, y si el campo 'user_id' no es nulo
            if (isset($employeeData[UserConstants::DATA]) && !empty($employeeData[UserConstants::DATA]) && $employeeData[UserConstants::DATA]->user_id) {
                return $this->resolve(true, UserConstants::ERROR_USER_ALREADY_ASIGNED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
            }
        }

        $model = new User();
        // Excluye 'employee_id' de la asignación masiva al modelo User
        $userData = $request->except(UserConstants::EMPLOYEE_ID);
        $model->fill($userData);
        $model->created_by = auth()->user()->name;

        $targetProfileId = (int) ($model->profile_id ?? 0);
        $targetProfile = $targetProfileId > 0 ? $this->profileRepository->find($targetProfileId) : null;
        if (($targetProfile->code ?? null) !== 'SUPER') {
            $model->all_companies = false;
        } else {
            $model->all_companies = (bool) ($request->input('all_companies', false));
        }

        try {
            $rules = $model->getRulesCreate();
            $this->validatorService->validate($request, $rules);
        } catch (ValidationException $e) {
            $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
            return $this->resolve(true, $messageError, Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        if ($this->repository->findBy('name', $model->name)) {
            return $this->resolve(true, 'El nombre de usuario ya existe.', Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }
        if ($model->email && $this->repository->findBy('email', $model->email)) {
            return $this->resolve(true, 'El correo ya existe.', Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        $model->password = $this->encryptorService->encrypt($model->password);

        try {
            $save = $this->repository->saveMongo($model);

            if (!$save) {
                return $this->resolve(true, UserConstants::NOT_CREATED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
            }

            $userId = $model->user_id;

            // Delegar la actualización del user_id al repositorio de empleados cuando exista empleado
            if (!empty($employeeId) && isset($employeeData[UserConstants::DATA]) && !empty($employeeData[UserConstants::DATA])) {
                $employee = $employeeData[UserConstants::DATA];
                $employee->user_id = $userId;
                $employee->save();
            }

            $user = $this->repository->find($userId);
            return $this->resolve(false, UserConstants::CREATED, $user, Constants::CODE_CREATED);
        } catch (\Throwable $e) {
            Log::error('CreateUserService::create failed', [
                'payload' => $request->all(),
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->resolve(true, UserConstants::NOT_CREATED, Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }
}
