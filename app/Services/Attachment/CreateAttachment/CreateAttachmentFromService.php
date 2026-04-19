<?php

namespace App\Services\Attachment\CreateAttachment;

use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use App\Util\Constants;
use App\Repositories\CompanyRepository;
use App\Repositories\DocumentRepository;
use App\Services\Service;
use App\Services\Shared\DocumentPathService;
use App\Services\Shared\UploadAttachmentForDocumentCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateAttachmentFromService extends Service
{
    private $repository;
    private $documentPathService;
    private $companyRepository;
    private $documentRepository;
    private $uploadAttachmentForDocumentCodeService;

    public function __construct(
        AttachmentRepository $repository,
        DocumentPathService $documentPathService,
        CompanyRepository $companyRepository,
        DocumentRepository $documentRepository,
        UploadAttachmentForDocumentCodeService $uploadAttachmentForDocumentCodeService
    ) {
        $this->repository = $repository;
        $this->documentPathService = $documentPathService;
        $this->companyRepository = $companyRepository;
        $this->documentRepository = $documentRepository;
        $this->uploadAttachmentForDocumentCodeService = $uploadAttachmentForDocumentCodeService;
    }

    public function createAttachmentFromService(
        $documentId,
        $companyId = null,
        $employeeId = null,
        $uploadedFile,
        $createdBy
    ) {
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
            return $pathWithFile;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Attachment: ' . $e->getMessage());
            return false;
        }
    }
}
