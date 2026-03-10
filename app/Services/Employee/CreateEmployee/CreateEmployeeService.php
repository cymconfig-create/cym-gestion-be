<?php

namespace App\Services\Employee\CreateEmployee;

use App\Repositories\EmployeeRepository;
use App\Util\Constants;
use App\Models\Employee;
use App\Services\Document\DocumentService;
use App\Services\Attachment\CreateAttachment\CreateAttachmentFromService;
use App\Services\Shared\UploadAttachmentForDocumentCodeService;
use App\Traits\LoadEmployeeRelationshipsTrait;
use App\Services\Service;
use App\Services\Shared\ValidatorService;
use App\Models\Company;
use App\Services\Shared\ErrorResponseFormatter;
use App\Util\EmployeeConstants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class CreateEmployeeService extends Service
{
    use LoadEmployeeRelationshipsTrait;

    private $repository;
    private $validatorService;
    private $documentService;
    private $createAttachmentFromService;
    private $uploadAttachmentForDocumentCodeService;
    private $errorResponseFormatter;

    public function __construct(
        EmployeeRepository $repository,
        ValidatorService $validatorService,
        CreateAttachmentFromService $createAttachmentFromService,
        UploadAttachmentForDocumentCodeService $uploadAttachmentForDocumentCodeService,
        DocumentService $documentService,
        ErrorResponseFormatter $errorResponseFormatter
    ) {
        $this->repository = $repository;
        $this->validatorService = $validatorService;
        $this->createAttachmentFromService = $createAttachmentFromService;
        $this->uploadAttachmentForDocumentCodeService = $uploadAttachmentForDocumentCodeService;
        $this->documentService = $documentService;
        $this->errorResponseFormatter = $errorResponseFormatter;
    }

    public function create(Request $request) // Indicar el tipo Request
    {
        $model = new Employee();
        $model->fill($request->all());
        $createdBy = $model->created_by = auth()->user()->name;

        // Validar si la compañía existe
        $company = Company::find($request->input(EmployeeConstants::COMPANY_ID));
        if (!$company) {
            return $this->resolve(true, EmployeeConstants::NOT_FOUND, Constants::NOT_DATA, Constants::CODE_NOT_FOUND);
        }

        $exist = $this->repository->findBy(EmployeeConstants::DOCUMENT_NUMBER, $model->identification_number);
        if ($exist) {
            return $this->resolve(true, EmployeeConstants::ALREADY_EXIST, Constants::NOT_DATA, Constants::CODE_CONFLICT);
        }

        try {
            $this->validatorService->validate($request, $model->getRulesCreate());
        } catch (ValidationException $e) {
            $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
            return $this->resolve(true, $messageError, Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            // Guardamos el modelo y la variable $save ahora almacena el resultado booleano
            $save = $this->repository->save($model);

            if (!$save) {
                DB::rollBack();
                return $this->resolve(true, EmployeeConstants::NOT_CREATED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
            }

            // El modelo $model ya tiene el employee_id asignado por Eloquent
            $companyId = $model->company_id;
            $employeeId = $model->employee_id;

            // Usamos el objeto $company ya validado
            $totalEmployees = $company->employees()->count();
            $company->quantity_employees = $totalEmployees;
            $company->save();

            // Subir documentos de empleado dinámicamente
            if (!$this->uploadAttachmentForDocumentCodeService->uploadAttachmentForDocumentCode($request, $createdBy, $companyId, $employeeId)) {
                DB::rollBack();
                return $this->resolve(true, Constants::ERROR_UPLOADING_FILE, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
            }

            DB::commit();

            $employee = $this->repository->find($employeeId);
            $this->loadEmployeeRelationships($employee);
            return $this->resolve(false, EmployeeConstants::CREATED, $employee, Constants::CODE_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resolve(true, $e->getMessage(), Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }
}
