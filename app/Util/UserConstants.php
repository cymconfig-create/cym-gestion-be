<?php

namespace App\Util;

class UserConstants
{
    public const CREATED = "Usuario guardado";
    public const NOT_CREATED = "Error del servidor al guardar el usuario";
    public const ALREADY_EXIST = "Error al guardar, la identificacion ingresada está siendo utilizada por un empleado registrado";
    public const DELETED = "Usuario eliminado";
    public const NOT_DELETED = "Error del servidor al eliminar el usuario";
    public const UPDATED = "Usuario actualizado";
    public const NOT_UPDATED = "Error del servidor al actualizar el usuario";
    public const NOT_FOUND = "Error, el usuario no existe";
    public const ERROR_VALIDATING = "Error al guardar, el usuario no cumple las reglas de validación";
    public const ERROR_EMPLOYEE_EMPTY = "El campo employee_id no puede estar vacío.";
    public const ERROR_USER_ALREADY_ASIGNED = "El usuario ya tiene un usuario asignado.";


    // ATRIBUTES
    public const EMPLOYEE_ID = 'employee_id';
    public const PASSWORD = "password";
    public const DATA = "data";
    public const ADD_RULES_USER = "required|exists:employees,employee_id";
}
