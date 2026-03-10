<?php

namespace App\Traits;

trait LoadUserRelationshipsTrait
{
    /**
     * Carga las relaciones comunes para un modelo o una colección de usuario.
     *
     * @param mixed $data El modelo o la colección en la que se cargarán las relaciones.
     * @return mixed El modelo o la colección con las relaciones cargadas.
     */
    private function loadUserRelationships($data)
    {
        if ($data) {
            $data->load(['profile', 'employee.company']);
        }
        return $data;
    }
}
