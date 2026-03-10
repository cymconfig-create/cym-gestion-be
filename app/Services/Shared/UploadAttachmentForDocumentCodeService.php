<?php

namespace App\Services\Shared;

use App\Services\Document\DocumentService;
use App\Services\Service;
use Illuminate\Http\Request;

class UploadAttachmentForDocumentCodeService extends Service
{
    private $attachmentUploaderService;
    private $documentService;

    public function __construct(
        AttachmentUploaderService $attachmentUploaderService,
        DocumentService $documentService
    ) {
        $this->attachmentUploaderService = $attachmentUploaderService;
        $this->documentService = $documentService;
    }

    /**
     * Sube los documentos relacionados con la empresa de forma dinámica.
     *
     * @param Request $request La solicitud HTTP entrante.
     * @param int|null $companyId El ID de la empresa recién creada.
     * @param string $createdBy El nombre del usuario que creó la empresa.
     * @return bool True si todos los documentos enviados se procesaron correctamente, false en caso contrario.
     */
    public function uploadAttachmentForDocumentCode(Request $request, $createdBy, $companyId = null, $employeeId = null): bool
    {
        $documentTypes = $this->documentService->getAllDocumentTypes();

        foreach ($documentTypes as $documentType) {
            $documentCode = strtolower($documentType->code);

            if ($request->hasFile($documentCode) && $request->file($documentCode)->isValid()) {
                $file = $request->file($documentCode);

                $uploaded = $this->attachmentUploaderService->createAttachmentFromService(
                    $documentType->document_id,
                    $companyId,
                    $employeeId,
                    $file,
                    $createdBy
                );

                if (!$uploaded) {
                    return false;
                }
            }
        }
        return true;
    }
}
