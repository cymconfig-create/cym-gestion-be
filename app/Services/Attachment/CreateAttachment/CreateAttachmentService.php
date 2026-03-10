<?php

namespace App\Services\Attachment\CreateAttachment;

use App\Util\Constants;
use App\Services\Service;
use App\Services\Shared\UploadAttachmentForDocumentCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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

        DB::beginTransaction();

        // Subir documentos dinámicamente
        if (!$this->uploadAttachmentForDocumentCodeService->uploadAttachmentForDocumentCode($request, $created_by)) {
            DB::rollBack();
            return $this->resolve(true, Constants::ERROR_UPLOADING_FILE, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        try {
            DB::commit();
            return $this->resolve(false, Constants::CREATED, Constants::NOT_DATA, Constants::CODE_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Attachment: ' . $e->getMessage());
            return $this->resolve(true, Constants::NOT_CREATED, $e->getMessage(), Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }
}
