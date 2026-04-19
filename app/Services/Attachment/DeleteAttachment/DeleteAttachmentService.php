<?php

namespace App\Services\Attachment\DeleteAttachment;

use App\Repositories\AttachmentRepository;
use App\Util\Constants;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
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

            // Paso 2: Iniciar una transacción de base de datos
            DB::beginTransaction();

            try {
                // Paso 3: Eliminar el registro en la base de datos
                $deleted = $this->repository->delete($model);

                if ($deleted) {
                    // Paso 4: Si todo fue bien, confirmar la transacción
                    DB::commit();
                    return $this->resolve(false, Constants::DELETED, '', Constants::CODE_SUCCESS);
                } else {
                    // Si la eliminación de la BD falla (por alguna razón interna del repositorio)
                    // Paso 5: Revertir la transacción (si se inició)
                    DB::rollBack();
                    return $this->resolve(true, Constants::NOT_DELETED, Constants::ERROR_DELETING_RECORD_DB, Constants::CODE_BAD_REQUEST);
                }
            } catch (\Exception $e) {
                // Paso 5: Capturar cualquier excepción durante la operación de BD y revertir
                DB::rollBack();
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
