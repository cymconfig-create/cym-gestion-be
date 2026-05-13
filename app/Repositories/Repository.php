<?php

namespace App\Repositories;

use App\Util\Constants;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Repository
{
    /**
     * Verifica si existe un registro que coincida con la columna y el valor especificados.
     *
     * @param string $model El nombre de la clase del modelo.
     * @param string $column El nombre de la columna.
     * @param mixed $value El valor a buscar.
     * @return bool Retorna true si existe un registro, false de lo contrario.
     */
    public function exists(string $model, string $column, $value): bool
    {
        return $model::where($column, $value)->exists();
    }

    public function save($model)
    {
        try {
            return $model->save(); // Devuelve directamente el resultado booleano de save()
        } catch (QueryException $e) {
            Log::channel('daily')->error($e);
            $this->handleQueryException($e, $model); //maneja el error.
            return false; //indica que hubo un fallo.
        }
    }

    public function update($model)
    {
        try {
            return $model->update(); // Devuelve directamente el resultado booleano
        } catch (QueryException $e) {
            Log::channel('daily')->error($e);
            $this->handleQueryException($e, $model);

            return false; // Indica que la actualización falló
        }
    }


    public function delete($model)
    {
        try {
            return $model->delete(); // Devuelve directamente el resultado booleano
        } catch (QueryException $e) {
            Log::channel('daily')->error($e);
            $this->handleQueryException($e, $model);
            return false; // Indica que la eliminación falló
        }
    }

    function handleQueryException(QueryException $e, $model)
    {
        $errorCode = $e->errorInfo[1];
        $table_name = $model->getTable();
        $errorMessage = $e->getMessage();

        if ($errorCode == Constants::ID_DUPLICATE) {
            preg_match("/Duplicate entry '(.*?)' for key '(.*?)'/", $errorMessage, $matches);
            if (count($matches) >= 3) {
                $nameOfColumnDuplicate = $matches[2];
                $tableReferenced = $this->getReferencedTable($nameOfColumnDuplicate, $table_name);

                if ($tableReferenced) {
                    $message = "No puede registrar dos " . $table_name . " con " . $tableReferenced . " iguales.";
                    Log::channel('daily')->error($message, ['exception' => $e]);
                    return $message;
                }
            }
            $message = "No puede registrar dos " . $table_name . " con parámetros únicos iguales.";
            Log::channel('daily')->error($message, ['exception' => $e]);
            return $message;
        }

        if ($errorCode == Constants::LENGTH_EXCEEDED) {
            $message = "Utilice menos caracteres para guardar el registro.";
            Log::channel('daily')->error($message, ['exception' => $e]);
            return $message;
        }

        if ($errorCode == Constants::FOREIGN_KEY_VIOLATION) {
            $message = "No puede eliminar " . $table_name . " si tiene registros asociados en otro módulo.";
            Log::channel('daily')->error($message, ['exception' => $e]);
            return $message;
        }

        $message = "Ocurrió un error en la base de datos.";
        Log::channel('daily')->error($message, ['exception' => $e]);
        return $message;
    }

    private function getReferencedTable($columnaForanea, $table_name)
    {
        try {
            $consulta = DB::select(
                "SELECT referenced_table_name FROM information_schema.key_column_usage WHERE table_name = ? AND column_name = ?",
                [$table_name, $columnaForanea]
            );

            if (!empty($consulta[0]->referenced_table_name)) {
                return $consulta[0]->referenced_table_name;
            }

            return null;
        } catch (\Exception $e) {
            Log::channel('daily')->error('Error getting referenced table: ' . $e->getMessage(), ['exception' => $e]);
            return null;
        }
    }
}
