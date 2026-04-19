<?php

namespace App\Services\Company\CreateCompany;

use App\Repositories\CompanyRepository;
use App\Util\Constants;
use App\Models\Company;
use App\Repositories\EmployeeRepository;
use App\Services\Service;
use App\Services\Shared\ErrorResponseFormatter;
use App\Services\Shared\ValidatorService;
use App\Services\Shared\UploadAttachmentForDocumentCodeService;
use App\Traits\LoadCompanyRelationshipsTrait;
use App\Util\CompanyConstants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class CreateCompanyService extends Service
{
    use LoadCompanyRelationshipsTrait;

    private $repository;
    private $validatorService;
    private $employeeRepository;
    private $uploadAttachmentForDocumentCodeService;
    private $errorResponseFormatter;

    public function __construct(
        CompanyRepository $repository,
        ValidatorService $validatorService,
        UploadAttachmentForDocumentCodeService $uploadAttachmentForDocumentCodeService,
        EmployeeRepository $employeeRepository,
        ErrorResponseFormatter $errorResponseFormatter
    ) {
        $this->repository = $repository;
        $this->validatorService = $validatorService;
        $this->uploadAttachmentForDocumentCodeService = $uploadAttachmentForDocumentCodeService;
        $this->employeeRepository = $employeeRepository;
        $this->errorResponseFormatter = $errorResponseFormatter;
    }

    public function create(Request $request)
    {
        $model = new Company();
        $model->fill($request->all());

        // Validar reglas de negocio (una vez que el modelo está lleno)
        try {
            $this->validatorService->validate($request, $model->getRulesCreate());
        } catch (ValidationException $e) {
            $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
            return $this->resolve(true, $messageError, Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        // Verificar existencia por NIT
        $exist = $this->repository->findBy(Constants::NIT, $model->nit);
        if ($exist) {
            return $this->resolve(true, CompanyConstants::ALREADY_EXIST, '', Constants::CODE_CONFLICT);
        }

        // Genera el código basado en el nombre de la empresa
        $createdBy = $model->created_by = auth()->user()->name;
        $model->code = $this->generateUniqueCode($model->name);

        try {
            DB::beginTransaction();
            $save = $this->repository->save($model);

            if (!$save) {
                DB::rollBack();
                return $this->resolve(true, CompanyConstants::NOT_CREATED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
            }

            $companyId = $model->company_id;

            // Inicializa quantity_employees si legal_representative_id está presente
            if ($request->has(CompanyConstants::LEGAL_REPRESENTATIVE_ID) && !is_null($request->input(CompanyConstants::LEGAL_REPRESENTATIVE_ID))) {
                $model->quantity_employees = 1;
                $model->save(); // Guardar el modelo actualizado con la cantidad de empleados

                // Actualizar el company_id del empleado representante legal
                $legalRepresentativeId = $request->input(CompanyConstants::LEGAL_REPRESENTATIVE_ID);
                $employee = $this->employeeRepository->find($legalRepresentativeId);

                if ($employee) {
                    $employee->company_id = $companyId;
                    $employee->save();
                }
            }

            // Subir documentos de empresa dinámicamente
            try {
                if (!$this->uploadAttachmentForDocumentCodeService->uploadAttachmentForDocumentCode($request, $createdBy, $companyId)) {
                    DB::rollBack();
                    return $this->resolve(true, Constants::ERROR_UPLOADING_FILE, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
                }
            } catch (ValidationException $e) {
                DB::rollBack();
                $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
                return $this->resolve(true, $messageError, Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
            }

            DB::commit();

            $company = $this->repository->find($companyId);
            $this->loadCompanyRelationships($company);
            return $this->resolve(false, CompanyConstants::CREATED, $company, Constants::CODE_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating company', ['exception' => $e]);
            return $this->resolve(true, CompanyConstants::NOT_CREATED, Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }

    public function generateCode($name)
    {
        $words = explode(' ', $name);
        $abbreviation = '';

        foreach ($words as $word) {
            $abbreviation .= strtoupper(substr($word, 0, 2));
        }

        $randomNumber = rand(100, 999);
        return $abbreviation . $randomNumber;
    }

    public function generateUniqueCode($name)
    {
        do {
            $code = $this->generateCode($name);
        } while ($this->repository->findBy(Constants::CODE, $code));

        return $code;
    }
}
