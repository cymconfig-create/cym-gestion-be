<?php

namespace App\Util;

class MessagingConstants
{
    // MESSAGES
    public const SUCCESSFUL_QUERY = "Consulta de mensajería exitosa";
    public const CONVERSATION_CREATED = "Conversación creada";
    public const MESSAGE_SENT = "Mensaje enviado";
    public const NOT_CREATED = "Error del servidor al guardar la conversación o el mensaje";
    public const NOT_FOUND = "Error, la conversación o el mensaje no existe";
    public const UPDATED = "Conversación actualizada";
    public const NOT_UPDATED = "Error del servidor al actualizar la conversación";
    public const CONVERSATION_ARCHIVED = "Conversación archivada";
    public const CONVERSATION_UNARCHIVED = "Conversación desarchivada";
    public const CONVERSATION_DELETED = "Conversación eliminada";
    public const CONVERSATION_RESTORED = "Conversación restaurada";
    public const VALIDATION_ERROR = "Error al guardar, la información no cumple las reglas de validación";
    public const INVALID_STATUS = "El estado de conversación proporcionado es inválido";
    public const FORBIDDEN = "No eres un participante en esta conversación";
    public const ERROR_VALIDATING = "Error al guardar, el mensaje no cumple las reglas de validación";

    // ATTRIBUTES
    public const PARTICIPANTS = 'participants';
    public const SUBJECT = 'subject';
    public const TYPE = 'type';
    public const BODY = 'body';
    public const COMPANY_ID = 'company_id';
    public const CREATED_BY = 'created_by';
    public const LAST_MESSAGE_AT = 'last_message_at';
    public const CONVERSATION_ID = 'conversation_id';
    public const USER_ID = 'user_id';
    public const USERS_USER_ID = 'users.user_id';
    public const IS_ARCHIVED = 'is_archived';
    public const IS_DELETED = 'is_deleted';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const UPDATED_BY = 'updated_by';
    public const PROFILE_ID = 'profile_id';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_PENDING = 'pending';
}
