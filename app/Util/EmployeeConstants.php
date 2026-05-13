<?php

namespace App\Util;

class EmployeeConstants
{
    public const SUCCESS = "Consulta de empleado exitosa";
    public const CREATED = "Empleado guardado";
    public const NOT_CREATED = "Error del servidor al guardar el empleado";
    public const ALREADY_EXIST = "Error al guardar, la identificacion ingresada está siendo utilizada por un empleado registrado";
    public const DELETED = "Empleado eliminado";
    public const NOT_DELETED = "Error del servidor al eliminar el empleado";
    public const UPDATED = "Empleado actualizado";
    public const NOT_UPDATED = "Error del servidor al actualizar el empleado";
    public const NOT_FOUND = "Error, el empleado no existe";
    public const ERROR_VALIDATING = "Error al guardar, el empleado no cumple las reglas de validación";

    // ATTRIBUTES
    public const DOCUMENT_NUMBER = 'identification_number';
    public const COMPANY_ID = 'company_id';
}
