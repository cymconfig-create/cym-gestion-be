<?php

namespace App\Services;

use App\Util\Constants;
use Illuminate\Http\JsonResponse;

class Service
{
    /**
     * Resuelve una respuesta para la API.
     *
     * @param bool $error Indica si hay un error en la respuesta.
     * @param string $message Mensaje descriptivo de la respuesta.
     * @param mixed $data Datos a devolver en la respuesta.
     * @param int|null $status Código de estado HTTP de la respuesta.
     * @return JsonResponse
     */
    public function resolve($error = false, $message = "", $data = null, $status = null): JsonResponse
    {
        // Usa el estado por defecto si no se proporciona uno.
        $status = $status === null ? Constants::CODE_SUCCESS : $status;

        // Crea el array de datos para el cuerpo JSON de la respuesta.
        $responseData = [
            "error" => $error,
            "message" => $message,
            "data" => $data,
            "status" => $status
        ];

        return response()->json($responseData, $status);
    }
}
