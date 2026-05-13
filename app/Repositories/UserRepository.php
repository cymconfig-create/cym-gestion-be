<?php

namespace App\Repositories;

use App\Auth\MongoUser;
use App\Ia\Mongo\MongoClientFactory;
use App\Util\Constants;
use InvalidArgumentException;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

class UserRepository extends Repository
{
    private const ALLOWED_FILTER_COLUMNS = [
        'user_id',
        'name',
        'email',
        'profile_id',
        'employee_id',
        'status',
    ];

    private function collection(): Collection
    {
        return MongoClientFactory::database()->selectCollection('users');
    }

    private function mapDocToUser(?object $doc): ?MongoUser
    {
        if (!$doc) {
            return null;
        }

        return MongoUser::fromDocument((array) $doc);
    }

    /**
     * Obtiene todos los usuarios.
     */
    public function all()
    {
        $cursor = $this->collection()->find([], ['sort' => ['user_id' => 1]]);
        $users = [];
        foreach ($cursor as $doc) {
            $users[] = $this->mapDocToUser($doc);
        }

        return collect($users);
    }

    /**
     * Busca un usuario por su clave primaria.
     *
     * @param mixed $value El valor de la clave primaria.
     */
    public function find($value)
    {
        return $this->mapDocToUser($this->collection()->findOne(['user_id' => (int) $value]));
    }

    /**
     * Busca el primer usuario que coincida con una columna y valor específicos.
     *
     * @param string $column La columna a buscar.
     * @param mixed $value El valor a buscar.
     */
    public function findBy($column, $value)
    {
        $this->assertAllowedColumn($column);
        return $this->mapDocToUser($this->collection()->findOne([$column => $this->normalizeValue($column, $value)]));
    }

    /**
     * Busca todos los usuarios que coincidan con una columna y valor específicos.
     *
     * @param string $column La columna a buscar.
     * @param mixed $value El valor a buscar.
     */
    public function findByAll($column, $value)
    {
        $this->assertAllowedColumn($column);
        $cursor = $this->collection()->find([$column => $this->normalizeValue($column, $value)], ['sort' => ['user_id' => 1]]);
        $users = [];
        foreach ($cursor as $doc) {
            $users[] = $this->mapDocToUser($doc);
        }

        return collect($users);
    }

    /**
     * Busca el primer usuario que coincida con un conjunto de atributos.
     *
     * @param array $attributes Un array asociativo de atributos (columna => valor).
     */
    public function findByAttributes($attributes)
    {
        $this->assertAllowedAttributes($attributes);
        $query = [];
        foreach ($attributes as $key => $value) {
            $query[$key] = $this->normalizeValue($key, $value);
        }

        return $this->mapDocToUser($this->collection()->findOne($query));
    }

    /**
     * Busca todos los usuarios que coincidan con un conjunto de atributos.
     *
     * @param array $attributes Un array asociativo de atributos (columna => valor).
     */
    public function findByAllAttributes($attributes)
    {
        $this->assertAllowedAttributes($attributes);
        $query = [];
        foreach ($attributes as $key => $value) {
            $query[$key] = $this->normalizeValue($key, $value);
        }

        $cursor = $this->collection()->find($query, ['sort' => ['user_id' => 1]]);
        $users = [];
        foreach ($cursor as $doc) {
            $users[] = $this->mapDocToUser($doc);
        }

        return collect($users);
    }

    public function nextUserId(): int
    {
        $last = $this->collection()->findOne([], ['sort' => ['user_id' => -1], 'projection' => ['user_id' => 1]]);
        if (!$last) {
            return 1;
        }

        return ((int) ($last->user_id ?? 0)) + 1;
    }

    public function saveMongo(MongoUser $user): bool
    {
        $attributes = $user->getAttributes();
        if (empty($attributes['user_id'])) {
            $attributes['user_id'] = $this->nextUserId();
            $user->forceFill(['user_id' => $attributes['user_id']]);
        }
        $now = new UTCDateTime((int) (microtime(true) * 1000));
        $attributes['created_at'] = $attributes['created_at'] ?? $now;
        $attributes['updated_at'] = $now;

        $result = $this->collection()->insertOne($attributes);

        return $result->isAcknowledged();
    }

    public function updateMongo(MongoUser $user): bool
    {
        $attributes = $user->getAttributes();
        $userId = (int) ($attributes['user_id'] ?? 0);
        if ($userId <= 0) {
            return false;
        }
        $attributes['updated_at'] = new UTCDateTime((int) (microtime(true) * 1000));

        $result = $this->collection()->updateOne(
            ['user_id' => $userId],
            ['$set' => $attributes]
        );

        return $result->isAcknowledged();
    }

    public function deleteMongo(MongoUser $user): bool
    {
        $userId = (int) $user->user_id;
        $result = $this->collection()->deleteOne(['user_id' => $userId]);

        return $result->isAcknowledged();
    }

    private function assertAllowedColumn(string $column): void
    {
        if (!in_array($column, self::ALLOWED_FILTER_COLUMNS, true)) {
            throw new InvalidArgumentException(Constants::INVALID_FILTER_COLUMN);
        }
    }

    private function assertAllowedAttributes(array $attributes): void
    {
        foreach (array_keys($attributes) as $column) {
            $this->assertAllowedColumn($column);
        }
    }

    private function normalizeValue(string $column, mixed $value): mixed
    {
        if (in_array($column, ['user_id', 'profile_id', 'employee_id'], true)) {
            return (int) $value;
        }
        if ($column === 'status') {
            return (bool) $value;
        }

        return $value;
    }
}
