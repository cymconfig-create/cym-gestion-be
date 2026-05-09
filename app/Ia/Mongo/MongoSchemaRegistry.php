<?php

namespace App\Ia\Mongo;

/**
 * Colecciones equivalentes a las tablas SQL del proyecto (migraciones).
 * En MongoDB las relaciones FK se modelan como referencias (ObjectId o int)
 * o documentos embebidos según el caso — ver Ia/MONGODB_STRUCTURE.md
 */
class MongoSchemaRegistry
{
    /**
     * @return list<string>
     */
    public static function collectionNames(): array
    {
        return [
            'actions',
            'action_profiles',
            'attachments',
            'companies',
            'conversation_user',
            'conversations',
            'documents',
            'employees',
            'events',
            'menu_profiles',
            'menus',
            'messages',
            'profiles',
            'selectors',
            'sub_menus',
            'users',
        ];
    }
}
