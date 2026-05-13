<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Services\Attachment\AttachmentService;
use App\Services\Attachment\CreateAttachment\CreateAttachmentService;
use App\Services\Attachment\DeleteAttachment\DeleteAttachmentService;
use App\Services\Attachment\UpdateAttachment\UpdateAttachmentService;

class AttachmentController extends Controller
{
    private $createAttachmentService;
    private $updateAttachmentService;
    private $deleteAttachmentService;
    private $attachmentService;

    public function __construct(
        AttachmentService $attachmentService,
        CreateAttachmentService $createAttachmentService,
        UpdateAttachmentService $updateAttachmentService,
        DeleteAttachmentService $deleteAttachmentService
    ) {
        $this->createAttachmentService = $createAttachmentService;
        $this->updateAttachmentService = $updateAttachmentService;
        $this->deleteAttachmentService = $deleteAttachmentService;
        $this->attachmentService = $attachmentService;
    }

    public function all()
    {
        return $this->attachmentService->all();
    }

    public function find($attachment_id)
    {
        return $this->attachmentService->find($attachment_id);
    }

    public function findBy($colum, $attachment_id)
    {
        return $this->attachmentService->findBy($colum, $attachment_id);
    }

    public function findByAll($colum, $value)
    {
        return $this->attachmentService->findByAll($colum, $value);
    }

    public function findByAllAttributes(Request $request)
    {
        $attributes = $request->query();
        return $this->attachmentService->findByAllAttributes($attributes);
    }

    public function create(Request $request)
    {
        return $this->createAttachmentService->create($request);
    }

    public function update(Request $request, $attachment_id)
    {
        return $this->updateAttachmentService->update($request, $attachment_id);
    }

    public function delete($attachment_id)
    {
        return $this->deleteAttachmentService->delete($attachment_id);
    }

    /**
     * Sirve un archivo adjunto protegido.     *
     */
    public function showFile(string $path)
    {
        // La ruta del archivo en storage/app/public
        $fullPath = 'public/' . $path;

        // Verifica si el archivo existe
        if (!Storage::exists($fullPath)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        // Obtiene la ruta física del archivo para el método file()
        $physicalPath = Storage::path($fullPath);

        // Retorna el archivo al cliente
        return response()->file($physicalPath);
    }
}
