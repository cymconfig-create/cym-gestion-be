<?php

namespace App\Services\Attachment\UpdateAttachment;

use App\Repositories\CompanyRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\AttachmentRepository;
use App\Services\Attachment\CreateAttachment\CreateAttachmentFromService;
use App\Util\Constants;
use App\Services\Service;
use App\Services\Shared\DocumentPathService;
use App\Services\Shared\ValidatorService;
use Illuminate\Support\Facades\Storage;

class UpdateAttachmentService extends Service
{
    private $repository;
    private $createAttachmentFromService;
    private $documentPathService;
    private $validatorService;
    private $companyRepository;
    private $documentRepository;

    public function __construct(
        AttachmentRepository $repository,
        DocumentPathService $documentPathService,
        CreateAttachmentFromService $createAttachmentFromService,
        ValidatorService $validatorService,
        CompanyRepository $companyRepository,
        DocumentRepository $documentRepository
    ) {
        $this->repository = $repository;
        $this->documentPathService = $documentPathService;
        $this->createAttachmentFromService = $createAttachmentFromService;
        $this->validatorService = $validatorService;
        $this->companyRepository = $companyRepository;
        $this->documentRepository = $documentRepository;
    }

    public function update($request, $id)
    {
        // Buscar el documento en la base de datos
        $model = $this->repository->find($id);
        $model->updated_by = auth()->user()->name;

        if (!$model) {
            return $this->resolve(true, Constants::OBJECT_NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        $pathOld = Constants::PUBLIC_PATH . $model->route_file;

        // Rellenar el modelo con los nuevos datos
        $model->fill($request->all());
        $model->created_by = auth()->user()->name;
        $errors = $this->validatorService->validate($request, $model->rulesCreate);

        if (count($errors) > 0) {
            return $this->resolve(true, Constants::ERROR_VALIDATING, reset($errors), Constants::CODE_UNPROCESSABLE_ENTITY);
        }

        // Verificar si se subió un nuevo archivo
        if ($request->hasFile('route_file')) {
            $uploadedFile = $request->file('route_file');

            // Validar que el archivo sea correcto
            if (!$uploadedFile->isValid()) {
                return $this->resolve(true, Constants::NOT_CREATED, Constants::ERROR_UPLOADING_FILE, Constants::CODE_BAD_REQUEST);
            }

            try {
                // Obtener códigos de la empresa y documento
                $codeCompany = $this->companyRepository->find($model->company_id)->code;
                $codeDocument = $this->documentRepository->find($model->document_id)->code;
                $newName = $this->documentRepository->find($model->document_id)->name;

                // Eliminar el archivo anterior si existe
                if (empty($pathOld) && !Storage::exists($pathOld)) {
                    return $this->resolve(true, Constants::NOT_UPDATED, Constants::FILE_NOT_EXIST, Constants::CODE_BAD_REQUEST);
                }

                Storage::delete($pathOld);

                // Guardar el nuevo archivo y actualizar la ruta
                $pathWithFile = $this->documentPathService->saveDocumentInPath($codeCompany, $codeDocument, $newName, $uploadedFile);
                $model->route_file = $pathWithFile;
            } catch (\Exception $e) {
                return $this->resolve(true, Constants::NOT_CREATED, $e->getMessage(), Constants::CODE_INTERNAL_SERVER_ERROR);
            }
        }

        // Actualizar el registro en la base de datos
        $update = $this->repository->update($model);

        if ($update) {
            return $this->resolve(false, Constants::UPDATED, $model, Constants::CODE_SUCCESS);
        } else {
            return $this->resolve(true, Constants::NOT_UPDATED, $update, Constants::CODE_BAD_REQUEST);
        }
    }

    public function updateAttachmentFromService(
        $documentId,
        $companyId,
        $employeeId,
        $uploadedFile,
        $updatedBy
    ) {
        if (!$uploadedFile->isValid()) {
            return $this->resolve(true, Constants::NOT_CREATED, Constants::ERROR_UPLOADING_FILE, Constants::CODE_BAD_REQUEST);
        }

        // Buscar si ya existe el documento asociado a la empresa
        $attributes = [
            'company_id' => $companyId,
            'document_id' => $documentId
        ];

        $existingDocument = $this->repository->findByAttributes($attributes);

        if (!$existingDocument) {
            // Si no existe, crear uno nuevo
            return $this->createAttachmentFromService->createAttachmentFromService(
                $documentId,
                $companyId,
                $employeeId,
                $uploadedFile,
                $updatedBy
            );
        }

        try {
            if (!$this->deleteOldFile($existingDocument)) {
                return $this->resolve(true, Constants::NOT_UPDATED, Constants::FILE_NOT_EXIST, Constants::CODE_BAD_REQUEST);
            }

            $newPath = $this->storeNewDocumentFile($uploadedFile, $companyId, $documentId);

            // Actualizar datos del modelo
            $existingDocument->fill([
                'updated_by' => $updatedBy,
                'employee_id' => $employeeId,
                'route_file' => $newPath,
            ]);

            return $this->repository->update($existingDocument)
                ? $newPath
                : $this->resolve(true, Constants::NOT_UPDATED, '', Constants::CODE_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->resolve(true, Constants::NOT_CREATED, $e->getMessage(), Constants::CODE_INTERNAL_SERVER_ERROR);
        }
    }

    private function deleteOldFile($document)
    {
        $oldPath = Constants::PUBLIC_PATH . $document->route_file;
        if (!Storage::exists($oldPath)) {
            return false;
        }
        Storage::delete($oldPath);
        return true;
    }

    private function storeNewDocumentFile($uploadedFile, $companyId, $documentId)
    {
        $company = $this->companyRepository->find($companyId);
        $document = $this->documentRepository->find($documentId);

        return $this->documentPathService->saveDocumentInPath(
            $company->code,
            $document->code,
            $document->name,
            $uploadedFile
        );
    }
}
