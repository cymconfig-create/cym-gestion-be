<?php

namespace App\Services\Attachment\DeleteAttachment;

use App\Repositories\AttachmentRepository;
use App\Util\Constants;
use App\Services\Service;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Importa la clase Log

class DeleteAttachmentService extends Service
{
    private $repository;

    public function __construct(AttachmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function delete($id)
    {
        $model = $this->repository->find($id);

        if (!$model) {
            return $this->resolve(true, Constants::OBJECT_NOT_FOUND, Constants::NOT_DATA, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        $path = Constants::PUBLIC_PATH . $model->route_file;

        try {
            // Paso 1: Intentar eliminar el archivo
            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            try {
                $deleted = $this->repository->deleteMongo($model);

                if ($deleted) {
                    return $this->resolve(false, Constants::DELETED, '', Constants::CODE_SUCCESS);
                } else {
                    return $this->resolve(true, Constants::NOT_DELETED, Constants::ERROR_DELETING_RECORD_DB, Constants::CODE_BAD_REQUEST);
                }
            } catch (\Exception $e) {
                Log::error(Constants::LOG_ERROR_DB_TRANSACTION . $e->getMessage());
                return $this->resolve(true, Constants::NOT_DELETED, Constants::DATABASE_TRANSACTION_FAILED, Constants::CODE_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            // Paso 6: Capturar errores durante la eliminación del archivo
            Log::error(Constants::LOG_ERROR_DELETING_FILE . $e->getMessage());
            return $this->resolve(true, Constants::NOT_DELETED, Constants::ERROR_DELETING_FILE, Constants::CODE_BAD_REQUEST);
        }
    }
}
