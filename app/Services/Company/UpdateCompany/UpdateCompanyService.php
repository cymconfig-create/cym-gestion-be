<?php

namespace App\Services\Company\UpdateCompany;

use App\Repositories\CompanyRepository;
use App\Repositories\EmployeeRepository;
use App\Services\Document\DocumentService;
use App\Util\Constants;
use App\Services\Service;
use App\Services\Shared\ValidatorService;
use App\Services\Attachment\UpdateAttachment\UpdateAttachmentService;
use App\Services\Shared\ErrorResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\LoadCompanyRelationshipsTrait;
use App\Util\CompanyConstants;

class UpdateCompanyService extends Service
{
    use LoadCompanyRelationshipsTrait;

    private $repository;
    private $validatorService;
    private $updateAttachmentService;
    private $documentService;
    private $employeeRepository;
    private $errorResponseFormatter;

    public function __construct(
        CompanyRepository $repository,
        ValidatorService $validatorService,
        UpdateAttachmentService $updateAttachmentService,
        DocumentService $documentService,
        EmployeeRepository $employeeRepository,
        ErrorResponseFormatter $errorResponseFormatter
    ) {
        $this->repository = $repository;
        $this->validatorService = $validatorService;
        $this->updateAttachmentService = $updateAttachmentService;
        $this->documentService = $documentService;
        $this->employeeRepository = $employeeRepository;
        $this->errorResponseFormatter = $errorResponseFormatter;
    }

    public function update(Request $request, $id) // Indicar el tipo Request
    {
        // Validamos si existe el registro
        $model = $this->repository->find($id);

        if (!$model) {
            return $this->resolve(true, CompanyConstants::NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        $updateBy = $model->updated_by = auth()->user()->name;
        $model->fill($request->all());

        // Validar reglas de negocio (una vez que el modelo está lleno)
        try {
            $this->validatorService->validate($request, $model->getRulesCreate());
        } catch (ValidationException $e) {
            $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
            return $this->resolve(true, $messageError, Constants::NOT_DATA, Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction(); // Iniciar la transacción aquí

            $updated = $this->repository->update($model);

            if (!$updated) {
                DB::rollBack();
                // El mensaje de error debería ser NOT_UPDATED, no NOT_CREATED para una actualización
                return $this->resolve(true, Constants::NOT_UPDATED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
            }

            $companyId = $model->company_id;

            if ($request->has(CompanyConstants::LEGAL_REPRESENTATIVE_ID) && !is_null($request->input(CompanyConstants::LEGAL_REPRESENTATIVE_ID))) {
                // se recalcula la cantidad total de empleados para esta empresa.
                // Esto asegura que quantity_employees refleje el conteo real.
                $model->quantity_employees = $model->employees()->count();

                // Actualizar el company_id del empleado representante legal
                $legalRepresentativeId = $request->input(CompanyConstants::LEGAL_REPRESENTATIVE_ID);
                $employee = $this->employeeRepository->find($legalRepresentativeId);

                if ($employee) {
                    // Solo actualiza si el company_id actual del empleado es diferente
                    if ($employee->company_id !== $companyId) {
                        $employee->company_id = $companyId;
                        $employee->save();
                    }
                }
            }

            // Subir/actualizar documentos de empresa dinámicamente
            if (!$this->updateCompanyDocuments($request, $companyId, $updateBy)) {
                DB::rollBack();
                return $this->resolve(true, Constants::ERROR_UPLOADING_FILE, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
            }

            DB::commit();

            $company = $this->repository->find($companyId);
            $this->loadCompanyRelationships($company);
            // El código de respuesta para una actualización exitosa es CODE_SUCCESS, no CODE_CREATED
            return $this->resolve(false, CompanyConstants::UPDATED, $company, Constants::CODE_SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating company', ['exception' => $e]);
            return $this->resolve(true, CompanyConstants::NOT_UPDATED, Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Sube/Actualiza los documentos relacionados con la empresa de forma dinámica.
     *
     * @param Request $request La solicitud HTTP entrante.
     * @param int $companyId El ID de la empresa a actualizar.
     * @param string $updatedBy El nombre del usuario que actualizó la empresa.
     * @return bool True si todos los documentos enviados se procesaron correctamente, false en caso contrario.
     */
    public function updateCompanyDocuments(Request $request, int $companyId, string $updatedBy): bool
    {
        // Obtener todos los tipos de documentos desde DocumentService
        $documentTypes = $this->documentService->getAllDocumentTypes();

        foreach ($documentTypes as $documentType) {
            // Basado en el 'code' de la base de datos, convertido a minúsculas
            $documentCode = strtolower($documentType->code);

            // Verifica si se ha subido un nuevo archivo para este campo y si es válido
            if ($request->hasFile($documentCode) && $request->file($documentCode)->isValid()) {
                $file = $request->file($documentCode);

                // Llama al servicio de actualización de adjuntos
                // El tercer parámetro para employeeId es 'null' ya que es para una compañía
                $updated = $this->updateAttachmentService->updateAttachmentFromService(
                    $documentType->document_id, // document_id del tipo de documento
                    $companyId,
                    null, // Aquí se pasa null porque es un documento de empresa, no de empleado
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
