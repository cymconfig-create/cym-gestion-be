<?php

namespace App\Util;

class Constants
{
    // STATUS CODES HTTP
    // Operaciones exitosas
    const CODE_SUCCESS = 200; // Solicitud exitosa
    const CODE_CREATED = 201; // Recurso creado correctamente
    const CODE_SUCCESS_NO_CONTENT = 200; // Solicitud exitosa, pero sin contenido que devolver

    // Errores del cliente
    const CODE_BAD_REQUEST = 400; // Solicitud malformada o incorrecta
    const CODE_UNAUTHORIZED = 401; // No autenticado (se necesitan credenciales válidas)
    const CODE_FORBIDDEN = 403; // Está autenticado pero no tiene permiso para acceder al recurso
    const CODE_NOT_FOUND = 404; // El recurso no fue encontrado
    const CODE_CONFLICT = 409; // Conflicto con el estado actual del recurso (p. ej., ya existe)
    const CODE_UNSUPPORTED_MEDIA_TYPE = 415; // Tipo de contenido no soportado
    const CODE_UNPROCESSABLE_ENTITY = 422; // Entidad no procesable (p. ej., errores de validación)

    // Errores del servidor
    const CODE_INTERNAL_SERVER_ERROR = 500; // Error genérico del servidor

    // Menssajes
    const SUCCESS = "Consulta exitosa";
    const CREATED = "Guardado";
    const NOT_CREATED = "Error al guardar";
    const OBJECT_ALREADY_EXIST = "Error al guardar, el objeto ya existe";
    const DELETED = "Eliminado";
    const NOT_DELETED = "Error al eliminar";
    const UPDATED = "Actualizado";
    const NOT_UPDATED = "Error al actualizar";
    const REPLIED_TO_CONVERSATION = "Respondido";
    const OBJECT_NOT_FOUND = "Error, el objeto no existe";
    const ERROR_VALIDATING = "Error al guardar, el objeto no cumple las reglas de validación";
    const ERROR_USER_ALREADY_ASIGNED = "El empleado ya tiene un usuario asignado.";
    const ERROR_EMPLOYEE_EMPTY = "El campo employee_id no puede estar vacío.";
    const ERROR_UPLOADING_FILE = "El archivo no se subió correctamente al servidor";
    const FILE_NOT_PROVIDED = "No se ha enviado ningún archivo en la petición";
    const FILE_NOT_EXIST = "No se encuentra el archivo";
    const ERROR_TO_CREATE_ZIP = 'Error al crear el archivo ZIP';
    const FILE_NO_FOUND = 'No existe el archivo';
    const ERROR_DELETING_FILE = "Error al eliminar el archivo del almacenamiento: ";
    const ERROR_DELETING_RECORD_DB = "Error al eliminar el registro de la base de datos";
    const DATABASE_TRANSACTION_FAILED = "La transacción de la base de datos falló";
    const LOG_ERROR_DELETING_FILE = "Error al eliminar el archivo: ";
    const LOG_ERROR_DB_TRANSACTION = "Error durante la transacción de la base de datos: ";
    const INTERNAL_SERVER_ERROR_MESSAGE = "Ocurrió un error interno. Intente nuevamente.";
    const INVALID_FILTER_COLUMN = "Columna de filtro no permitida";
    const INVALID_FILTER_ATTRIBUTES = "Hay atributos de filtro no permitidos";
    const INVALID_FILE_TYPE = "El tipo de archivo no está permitido";
    const INVALID_FILE_SIZE = "El archivo supera el tamaño máximo permitido";

    const NOT_DATA  = null;
    const NOT_MESSAGE  = null;

    //CONSTANTS AUTHENTICATE
    const CREDENTIALS_INVALID = "Credenciales invalidas";
    const USER_CHECK_TOKEN_INVALID = "Token invalido";
    const RESOURCE_NOT_FOUND = "URL no encontrada, no existe";

    //ATTRIBUTES
    const NAME = "name";
    const PASSWORD = "password";
    const TOKEN = 'token';
    const NIT = 'nit';
    const CODE = 'code';
    const PUBLIC_PATH = "public/";

    //CODE DATABASE
    const ID_DUPLICATE = 1062;
    const FOREIGN_KEY_VIOLATION = 1451;
    const LENGTH_EXCEEDED = 1406;
}
