<?php

namespace App\Traits;

trait LoadCompanyRelationshipsTrait
{
    /**
     * Carga las relaciones comunes de un modelo o una colección de la compañía.
     *
     * @param mixed $data El modelo o la colección para cargar las relaciones.
     * @return mixed El modelo o la colección con las relaciones cargadas.
     */
    private function loadCompanyRelationships($data)
    {
        if ($data && method_exists($data, 'load')) {
            $data->load(
                'legalRepresentative',
                'systemManager',
                'employees',
                'personType',
                'taxRegime',
                'riskLevel',
                'arl',
                'attachments'
            );
        }
        return $data;
    }
}
