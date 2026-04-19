<?php

namespace App\Services\User\CreateUser;

use App\Repositories\UserRepository;
use App\Util\Constants;
use App\Models\User;
use App\Services\Service;
use App\Services\Shared\EncryptorService;
use App\Services\Shared\ValidatorService;
use Illuminate\Validation\ValidationException;
use App\Services\Employee\EmployeeService;
use App\Services\Shared\ErrorResponseFormatter;
use App\Traits\LoadUserRelationshipsTrait;
use App\Util\UserConstants;
use Illuminate\Support\Facades\DB;

class CreateUserService extends Service
{
    use LoadUserRelationshipsTrait;

    private $repository;
    private $validatorService;
    private $encryptorService;
    private $employeeService;
    private $errorResponseFormatter;

    public function __construct(
        UserRepository $repository,
        ValidatorService $validatorService,
        EncryptorService $encryptorService,
        EmployeeService $employeeService,
        ErrorResponseFormatter $errorResponseFormatter
    ) {
        $this->repository = $repository;
        $this->validatorService = $validatorService;
        $this->encryptorService = $encryptorService;
        $this->employeeService = $employeeService;
        $this->errorResponseFormatter = $errorResponseFormatter;
    }

    public function create($request)
    {
        $employeeId = $request->input(UserConstants::EMPLOYEE_ID);

        // Paso 1: Validar si el 'employee_id' no está vacío
        if (empty($employeeId)) {
            return $this->resolve(true, UserConstants::ERROR_EMPLOYEE_EMPTY, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        // Paso 2: Validar si el 'employee_id' ya tiene un usuario asociado
        $employeeResponse = $this->employeeService->find($employeeId);
        $employeeData = $employeeResponse->getOriginalContent();

        // Verifica si la respuesta es exitosa y si contiene datos, y si el campo 'user_id' no es nulo
        if (isset($employeeData[UserConstants::DATA]) && !empty($employeeData[UserConstants::DATA]) && $employeeData[UserConstants::DATA]->user_id) {
            return $this->resolve(true, UserConstants::ERROR_USER_ALREADY_ASIGNED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        $model = new User();
        // Excluye 'employee_id' de la asignación masiva al modelo User
        $userData = $request->except(UserConstants::EMPLOYEE_ID);
        $model->fill($userData);
        $model->created_by = auth()->user()->name;

        try {
            // Agregamos el 'employee_id' a las reglas de validación como campo obligatorio
            $rules = array_merge($model->getRulesCreate(), [UserConstants::EMPLOYEE_ID => UserConstants::ADD_RULES_USER]);
            $this->validatorService->validate($request, $rules);
        } catch (ValidationException $e) {
            $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
            return $this->resolve(true, $messageError, Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        $model->password = $this->encryptorService->encrypt($model->password);

        try {
            DB::beginTransaction();

            $save = $this->repository->save($model);

            if (!$save) {
                DB::rollBack();
                return $this->resolve(true, UserConstants::NOT_CREATED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
            }

            $userId = $model->user_id;

            // Delegar la actualización del user_id al repositorio de empleados
            $employee = $employeeData[UserConstants::DATA];
            $employee->user_id = $userId;
            $employee->save();

            DB::commit();

            $user = $this->repository->find($userId);
            $this->loadUserRelationships($user);
            return $this->resolve(false, UserConstants::CREATED, $user, Constants::CODE_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resolve(true, UserConstants::NOT_CREATED, Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }
}
