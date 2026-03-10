<?php

namespace App\Services\Company\DeleteCompany;

use App\Repositories\CompanyRepository;
use App\Services\Attachment\DeleteAttachment\DeleteAttachmentService;
use App\Util\Constants;
use App\Services\Service;
use App\Util\CompanyConstants;

class DeleteCompanyService extends Service
{
    private $repository;
    private $documentCompanyService;

    public function __construct(
        CompanyRepository $repository,
        DeleteAttachmentService $documentCompanyService
    ) {
        $this->repository = $repository;
        $this->documentCompanyService = $documentCompanyService;
    }

    public function delete($id)
    {
        try {
            // Validamos si existe el registro
            $model = $this->repository->find($id);

            if (!$model) {
                return $this->resolve(true, CompanyConstants::NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
            }

            $documents = $model->attachments;

            if (count($documents) > 0) {
                foreach ($documents as $document) {
                    $this->documentCompanyService->delete($document->attachment_id);
                }
            }

            $this->repository->delete($model);

            return $this->resolve(false, CompanyConstants::DELETED, '', Constants::CODE_SUCCESS);
        } catch (\Exception $e) {
            return $this->resolve(true, $e->getMessage(), Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }
    }
}
