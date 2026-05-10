<?php

namespace App\Services\Attachment\CreateAttachment;

use App\Util\Constants;
use App\Services\Service;
use App\Services\Shared\UploadAttachmentForDocumentCodeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CreateAttachmentService extends Service
{
    private $uploadAttachmentForDocumentCodeService;

    public function __construct(
        UploadAttachmentForDocumentCodeService $uploadAttachmentForDocumentCodeService
    ) {
        $this->uploadAttachmentForDocumentCodeService = $uploadAttachmentForDocumentCodeService;
    }

    public function create(Request $request)
    {
        $created_by = auth()->user()->name;

        // Subir documentos dinámicamente
        try {
            if (!$this->uploadAttachmentForDocumentCodeService->uploadAttachmentForDocumentCode($request, $created_by)) {
                return $this->resolve(true, Constants::ERROR_UPLOADING_FILE, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
            }
        } catch (ValidationException $e) {
            return $this->resolve(true, Constants::ERROR_VALIDATING, $e->errors(), Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        try {
            return $this->resolve(false, Constants::CREATED, Constants::NOT_DATA, Constants::CODE_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating Attachment: ' . $e->getMessage());
            return $this->resolve(true, Constants::NOT_CREATED, Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }
}
