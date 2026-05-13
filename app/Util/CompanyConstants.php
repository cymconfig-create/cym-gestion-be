<?php

namespace App\Util;

class CompanyConstants
{
    public const SUCCESS = "Consulta de compañía exitosa";
    public const CREATED = "Compañía guardada";
    public const NOT_CREATED = "Error del servidor al guardar la compañía";
    public const ALREADY_EXIST = "Error al guardar, el NIT ingresado está siendo utilizado por una compañía";
    public const DELETED = "Compañía eliminada";
    public const NOT_DELETED = "Error del servidor al eliminar la compañía";
    public const UPDATED = "Compañía actualizada";
    public const NOT_UPDATED = "Error del servidor al actualizar la compañía";
    public const NOT_FOUND = "Error, la compañía no existe";
    public const ERROR_VALIDATING = "Error al guardar, la compañía no cumple las reglas de validación";

    // ATRIBUTES
    public const LEGAL_REPRESENTATIVE_ID = 'legal_representative_id';
}
