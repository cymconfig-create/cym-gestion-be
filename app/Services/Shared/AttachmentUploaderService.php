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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AttachmentUploaderService extends Service
{
    private const ALLOWED_FILE_MIMES = 'pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,csv';
    private const MAX_FILE_SIZE_KB = 10240; // 10 MB

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
            $this->validateUploadedFile($uploadedFile);
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

    private function validateUploadedFile(UploadedFile $uploadedFile): void
    {
        $validator = Validator::make(
            ['route_file' => $uploadedFile],
            ['route_file' => 'file|mimes:' . self::ALLOWED_FILE_MIMES . '|max:' . self::MAX_FILE_SIZE_KB]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
