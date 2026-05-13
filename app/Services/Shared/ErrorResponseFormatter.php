<?php

namespace App\Services\Shared;

use Illuminate\Validation\ValidationException;

class ErrorResponseFormatter
{
    /**
     * Formatea los mensajes de error de una excepción de validación en una sola cadena.
     *
     * @param ValidationException $exception La excepción de validación que contiene los errores.
     * @return string La cadena con todos los mensajes de error unidos.
     */
    public function formatValidationErrors(ValidationException $exception): string
    {
        // Obtener todos los mensajes de error planos de la excepción.
        $allMessages = $exception->validator->errors()->all();

        // Unir los mensajes en una sola cadena de texto usando " and " como separador.
        return implode(' and ', $allMessages);
    }
}
