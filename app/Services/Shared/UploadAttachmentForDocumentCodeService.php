<?php

namespace App\Services\Shared;

use App\Services\Document\DocumentService;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UploadAttachmentForDocumentCodeService extends Service
{
    private const ALLOWED_FILE_MIMES = 'pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,csv';
    private const MAX_FILE_SIZE_KB = 10240; // 10 MB

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
        $this->validateAttachmentFiles($request, $documentTypes);

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

    private function validateAttachmentFiles(Request $request, $documentTypes): void
    {
        $rules = [];

        foreach ($documentTypes as $documentType) {
            $documentCode = strtolower($documentType->code);

            if ($request->hasFile($documentCode)) {
                $rules[$documentCode] = 'file|mimes:' . self::ALLOWED_FILE_MIMES . '|max:' . self::MAX_FILE_SIZE_KB;
            }
        }

        if (empty($rules)) {
            return;
        }

        $validator = Validator::make(
            $request->allFiles(),
            $rules,
            [
                '*.mimes' => 'Tipo de archivo no permitido. Formatos válidos: pdf, jpg, jpeg, png, webp, doc, docx, xls, xlsx, csv.',
                '*.max' => 'El archivo supera el tamaño máximo permitido (10MB).',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
