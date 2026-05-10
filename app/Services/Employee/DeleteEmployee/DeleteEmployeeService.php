<?php

namespace App\Services\Employee\DeleteEmployee;

use App\Repositories\EmployeeRepository;
use App\Repositories\AttachmentRepository;
use App\Services\Attachment\DeleteAttachment\DeleteAttachmentService;
use App\Util\Constants;
use App\Services\Service;
use App\Util\EmployeeConstants;
use Illuminate\Support\Facades\Log;

class DeleteEmployeeService extends Service
{
    private $repository;
    private $documentCompanyService;
    private $attachmentRepository;

    public function __construct(
        EmployeeRepository $repository,
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
                return $this->resolve(true, EmployeeConstants::NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
            }

            $documents = $this->attachmentRepository->findByAll('employee_id', $id);

            if (count($documents) > 0) {
                foreach ($documents as $document) {
                    $this->documentCompanyService->delete($document->attachment_id);
                }
            }

            $this->repository->deleteMongo($model);

            return $this->resolve(false, EmployeeConstants::DELETED, Constants::NOT_DATA, Constants::CODE_SUCCESS);
        } catch (\Exception $e) {
            Log::error('Error deleting employee', ['exception' => $e]);
            return $this->resolve(true, EmployeeConstants::NOT_DELETED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }
    }
}
