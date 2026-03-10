<?php

namespace App\Traits;

trait LoadEmployeeRelationshipsTrait
{
    /**
     * Carga las relaciones comunes del modelo Employee.
     * @param mixed $data El modelo o la colección de modelos.
     * @return void
     */
    private function loadEmployeeRelationships($data)
    {
        if ($data) {
            $data->load(
                'identificationType',
                'academicLevel',
                'arlType',
                'epsType',
                'pensionFundType',
                'severanceFundType',
                'contractType',
                'bloodType',
                'civilStatus',
                'contactIdentificationType',
                'company',
                'attachments'
            );
        }
        return $data;
    }
}
