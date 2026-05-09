<?php

namespace App\Ia\Mongo;

use MongoDB\Database;

class MongoSchemaInitializer
{
    /**
     * Crea colecciones si no existen. Validación JSON Schema en modo "warn"
     * para no bloquear inserciones mientras se evoluciona el modelo.
     *
     * @return list<array{name: string, status: string}>
     */
    public function ensureCollections(Database $database, bool $withValidators = true): array
    {
        $existing = iterator_to_array($database->listCollectionNames());
        $existingSet = array_fill_keys($existing, true);

        $results = [];

        foreach (MongoSchemaRegistry::collectionNames() as $name) {
            if (isset($existingSet[$name])) {
                $results[] = ['name' => $name, 'status' => 'exists'];
                continue;
            }

            $options = [];
            if ($withValidators) {
                $validator = $this->validatorForCollection($name);
                if ($validator !== null) {
                    $options['validator'] = $validator;
                    $options['validationLevel'] = 'moderate';
                    $options['validationAction'] = 'warn';
                }
            }

            $database->createCollection($name, $options);
            $results[] = ['name' => $name, 'status' => 'created'];
        }

        return $results;
    }

    /**
     * Esquema mínimo alineado con columnas principales de migraciones SQL.
     */
    private function validatorForCollection(string $name): ?array
    {
        $schemas = [
            'users' => [
                'bsonType' => 'object',
                'required' => ['user_id', 'name', 'profile_id'],
                'properties' => [
                    'user_id' => ['bsonType' => 'int'],
                    'name' => ['bsonType' => 'string'],
                    'password' => ['bsonType' => 'string'],
                    'profile_id' => ['bsonType' => 'int'],
                    'employee_id' => ['bsonType' => ['int', 'null']],
                    'status' => ['bsonType' => 'bool'],
                    'created_by' => ['bsonType' => 'string'],
                    'updated_by' => ['bsonType' => ['string', 'null']],
                    'created_at' => ['bsonType' => ['date', 'null']],
                    'updated_at' => ['bsonType' => ['date', 'null']],
                ],
            ],
            'companies' => [
                'bsonType' => 'object',
                'required' => ['company_id', 'code', 'nit', 'name'],
                'properties' => [
                    'company_id' => ['bsonType' => 'int'],
                    'code' => ['bsonType' => 'string'],
                    'nit' => ['bsonType' => 'string'],
                    'name' => ['bsonType' => 'string'],
                    'status' => ['bsonType' => 'bool'],
                    'is_eliminated' => ['bsonType' => 'bool'],
                    'created_at' => ['bsonType' => ['date', 'null']],
                    'updated_at' => ['bsonType' => ['date', 'null']],
                ],
            ],
            'employees' => [
                'bsonType' => 'object',
                'required' => ['employee_id', 'full_name', 'identification_number'],
                'properties' => [
                    'employee_id' => ['bsonType' => 'int'],
                    'full_name' => ['bsonType' => 'string'],
                    'identification_number' => ['bsonType' => 'string'],
                    'company_id' => ['bsonType' => ['int', 'null']],
                    'user_id' => ['bsonType' => ['int', 'null']],
                    'status' => ['bsonType' => 'bool'],
                    'created_at' => ['bsonType' => ['date', 'null']],
                    'updated_at' => ['bsonType' => ['date', 'null']],
                ],
            ],
            'attachments' => [
                'bsonType' => 'object',
                'required' => ['attachment_id', 'document_id', 'route_file'],
                'properties' => [
                    'attachment_id' => ['bsonType' => 'int'],
                    'document_id' => ['bsonType' => 'int'],
                    'company_id' => ['bsonType' => ['int', 'null']],
                    'employee_id' => ['bsonType' => ['int', 'null']],
                    'route_file' => ['bsonType' => 'string'],
                    'created_by' => ['bsonType' => ['string', 'null']],
                    'created_at' => ['bsonType' => ['date', 'null']],
                    'updated_at' => ['bsonType' => ['date', 'null']],
                ],
            ],
            'documents' => [
                'bsonType' => 'object',
                'required' => ['document_id', 'code', 'name'],
                'properties' => [
                    'document_id' => ['bsonType' => 'int'],
                    'code' => ['bsonType' => 'string'],
                    'name' => ['bsonType' => 'string'],
                    'percentage' => ['bsonType' => ['double', 'null']],
                    'status' => ['bsonType' => 'bool'],
                    'created_at' => ['bsonType' => ['date', 'null']],
                    'updated_at' => ['bsonType' => ['date', 'null']],
                ],
            ],
            'profiles' => [
                'bsonType' => 'object',
                'required' => ['profile_id', 'code', 'name'],
                'properties' => [
                    'profile_id' => ['bsonType' => 'int'],
                    'code' => ['bsonType' => 'string'],
                    'name' => ['bsonType' => 'string'],
                    'description' => ['bsonType' => ['string', 'null']],
                    'status' => ['bsonType' => 'bool'],
                    'created_at' => ['bsonType' => ['date', 'null']],
                    'updated_at' => ['bsonType' => ['date', 'null']],
                ],
            ],
        ];

        if (!isset($schemas[$name])) {
            return [
                '$jsonSchema' => [
                    'bsonType' => 'object',
                    'additionalProperties' => true,
                ],
            ];
        }

        return ['$jsonSchema' => $schemas[$name]];
    }
}
