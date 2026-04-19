<?php

namespace App\Util;

class DocumentConstants
{
    public const COMPLETION_SUMMARY = 'Resumen de cumplimiento de documentos';

    public const INVALID_SCOPE = 'El parámetro scope debe ser auto, employee, company o all.';

    public const FORBIDDEN_EMPLOYEE = 'No tiene permiso para consultar el cumplimiento de otro empleado.';

    public const FORBIDDEN_COMPANY = 'No tiene permiso para consultar el cumplimiento de esta empresa.';

    public const EMPLOYEE_ID_REQUIRED = 'Para el ámbito employee debe indicar employee_id o usar un usuario vinculado a un empleado.';

    public const COMPANY_ID_REQUIRED = 'Para el ámbito company debe indicar company_id o usar un usuario vinculado a una empresa.';
}
