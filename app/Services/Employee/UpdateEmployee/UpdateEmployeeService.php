<?php

namespace App\Services\Employee\UpdateEmployee;

use App\Repositories\EmployeeRepository;
use App\Repositories\CompanyRepository;
use App\Services\Document\DocumentService;
use App\Services\Attachment\UpdateAttachment\UpdateAttachmentService;
use App\Util\Constants;
use App\Services\Service;
use App\Services\Shared\ErrorResponseFormatter;
use App\Services\Shared\ValidatorService;
use App\Traits\LoadEmployeeRelationshipsTrait;
use App\Util\EmployeeConstants;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class UpdateEmployeeService extends Service
{
    use LoadEmployeeRelationshipsTrait;

    private $repository;
    private $validatorService;
    private $documentService;
    private $updateAttachmentService;
    private $errorResponseFormatter;
    private $companyRepository;

    public function __construct(
        EmployeeRepository $repository,
        ValidatorService $validatorService,
        UpdateAttachmentService $updateAttachmentService,
        DocumentService $documentService,
        ErrorResponseFormatter $errorResponseFormatter,
        CompanyRepository $companyRepository
    ) {
        $this->repository = $repository;
        $this->validatorService = $validatorService;
        $this->updateAttachmentService = $updateAttachmentService;
        $this->documentService = $documentService;
        $this->errorResponseFormatter = $errorResponseFormatter;
        $this->companyRepository = $companyRepository;
    }

    public function update(Request $request, $employeeId) // Indicar el tipo Request
    {
        // Validamos si existe el registro
        $model = $this->repository->find($employeeId);

        if (!$model) {
            return $this->resolve(true, EmployeeConstants::NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        $model->fill($request->all());
        $model->updated_by = auth()->user()->name;

        try {
            $this->validatorService->validate($request, $model->getRulesCreate());
        } catch (ValidationException $e) {
            $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
            return $this->resolve(true, $messageError, Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        try {
            $updated = $this->repository->updateMongo($model);

            if (!$updated) {
                return $this->resolve(true, EmployeeConstants::NOT_UPDATED, $updated, Constants::CODE_BAD_REQUEST);
            }

            // Actualizar cantidad de empleados en la empresa
            $company = $this->companyRepository->find((int) $model->company_id);

            if ($company) {
                $totalEmployees = $this->repository->countByCompanyId((int) $company->company_id);
                $company->quantity_employees = $totalEmployees;
                $this->companyRepository->updateMongo($company);
            }

            // Subir/actualizar documentos de empleado dinámicamente
            if (!$this->updateEmployeeDocuments($request, $company->company_id, $employeeId, $model->updated_by)) {
                return $this->resolve(true, Constants::ERROR_UPLOADING_FILE, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
            }

            // Re-obtener el modelo actualizado y cargar las relaciones usando el trait
            $employee = $this->repository->find($employeeId);
            $this->loadEmployeeRelationships($employee);
            return $this->resolve(false, EmployeeConstants::UPDATED, $employee, Constants::CODE_SUCCESS);
        } catch (\Exception $e) {
            Log::error('Error updating employee', ['exception' => $e]);
            return $this->resolve(true, EmployeeConstants::NOT_UPDATED, Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Sube/Actualiza los documentos relacionados con el empleado de forma dinámica.
     *
     * @param Request $request La solicitud HTTP entrante.
     * @param int $companyId El ID de la empresa.
     * @param int $employeeId El ID del empleado a actualizar.
     * @param string $updatedBy El nombre del usuario que actualizó el empleado.
     * @return bool True si todos los documentos enviados se procesaron correctamente, false en caso contrario.
     */
    private function updateEmployeeDocuments(Request $request, int $companyId, int $employeeId, string $updatedBy): bool
    {
        // Obtener todos los tipos de documentos
        $documentTypes = $this->documentService->getAllDocumentTypes();

        foreach ($documentTypes as $documentType) {
            // Se toma el code de la base de datos, convertido a minúsculas.
            $documentCode = strtolower($documentType->code);

            // Verifica si se ha subido un nuevo archivo para este campo y si es válido
            if ($request->hasFile($documentCode) && $request->file($documentCode)->isValid()) {
                $file = $request->file($documentCode);

                // Llama al servicio de actualización de adjuntos
                $updated = $this->updateAttachmentService->updateAttachmentFromService(
                    $documentType->document_id, // document_id del tipo de documento
                    $companyId,
                    $employeeId,
                    $file,
                    $updatedBy
                );

                if (!$updated) {
                    // Si falla la actualización de un archivo, retorna falso
                    return false;
                }
            }
            // Si el campo no se envió o el archivo no es válido, simplemente se ignora.
            // Esto permite que solo se actualicen los documentos que se envían.
        }
        return true; // Todos los documentos enviados se procesaron correctamente
    }
}
