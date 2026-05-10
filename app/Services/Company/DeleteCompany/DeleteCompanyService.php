<?php

namespace App\Services\Company\DeleteCompany;

use App\Repositories\CompanyRepository;
use App\Repositories\AttachmentRepository;
use App\Services\Attachment\DeleteAttachment\DeleteAttachmentService;
use App\Util\Constants;
use App\Services\Service;
use App\Util\CompanyConstants;
use Illuminate\Support\Facades\Log;

class DeleteCompanyService extends Service
{
    private $repository;
    private $documentCompanyService;
    private $attachmentRepository;

    public function __construct(
        CompanyRepository $repository,
        DeleteAttachmentService $documentCompanyService,
        AttachmentRepository $attachmentRepository
    ) {
        $this->repository = $repository;
        $this->documentCompanyService = $documentCompanyService;
        $this->attachmentRepository = $attachmentRepository;
    }

    public function delete($id)
    {
        try {
            // Validamos si existe el registro
            $model = $this->repository->find($id);

            if (!$model) {
                return $this->resolve(true, CompanyConstants::NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
            }

            $documents = $this->attachmentRepository->findByAll('company_id', $id);

            if (count($documents) > 0) {
                foreach ($documents as $document) {
                    $this->documentCompanyService->delete($document->attachment_id);
                }
            }

            $this->repository->deleteMongo($model);

            return $this->resolve(false, CompanyConstants::DELETED, '', Constants::CODE_SUCCESS);
        } catch (\Exception $e) {
            Log::error('Error deleting company', ['exception' => $e]);
            return $this->resolve(true, CompanyConstants::NOT_DELETED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }
    }
}
