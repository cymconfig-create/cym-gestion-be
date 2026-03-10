<?php

namespace App\Services\Shared;

use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use App\Services\Service;
use App\Services\Shared\DocumentPathService;
use App\Repositories\DocumentRepository;
use App\Repositories\CompanyRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttachmentUploaderService extends Service
{
    private $repository;
    private $documentPathService;
    private $documentRepository;
    private $companyRepository;

    public function __construct(
        AttachmentRepository $repository,
        DocumentPathService $documentPathService,
        DocumentRepository $documentRepository,
        CompanyRepository $companyRepository
    ) {
        $this->repository = $repository;
        $this->documentPathService = $documentPathService;
        $this->documentRepository = $documentRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * Guarda un archivo adjunto y crea un registro en la base de datos.
     *
     * @param int $documentId El ID del tipo de documento.
     * @param int|null $companyId El ID de la compañía.
     * @param int|null $employeeId El ID del empleado.
     * @param UploadedFile $uploadedFile El archivo subido.
     * @param string $createdBy El nombre del usuario que sube el archivo.
     * @return bool True si el archivo se subió y guardó correctamente.
     */
    public function createAttachmentFromService(
        int $documentId,
        ?int $companyId,
        ?int $employeeId,
        UploadedFile $uploadedFile,
        string $createdBy
    ): bool {
        $model = new Attachment();
        $model->document_id = $documentId;
        $model->company_id = $companyId;
        $model->employee_id = $employeeId;
        $model->created_by = $createdBy;

        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return false;
        }

        try {
            DB::beginTransaction();

            $company = $this->companyRepository->find($model->company_id);
            $codeCompany = $company ? $company->code : 'ADMIN';
            $codeDocument = $this->documentRepository->find($documentId)->code;
            $newName = $this->documentRepository->find($documentId)->name;
            $pathWithFile = $this->documentPathService->saveDocumentInPath($codeCompany, $codeDocument, $newName, $uploadedFile);

            $model->route_file = $pathWithFile;
            $save = $this->repository->save($model);

            if (!$save) {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Attachment: ' . $e->getMessage());
            return false;
        }
    }
}
