<?php

namespace App\Repositories;

use App\Models\User;
use App\Util\Constants;
use InvalidArgumentException;

class UserRepository extends Repository
{
    private const ALLOWED_FILTER_COLUMNS = [
        'user_id',
        'name',
        'profile_id',
        'employee_id',
        'status',
    ];

    /**
     * Obtiene todos los usuarios.
     */
    public function all()
    {
        return User::getFilteredUsers();
    }

    /**
     * Busca un usuario por su clave primaria.
     *
     * @param mixed $value El valor de la clave primaria.
     */
    public function find($value)
    {
        return User::find($value);
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
        return User::where($column, $value)->first();
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
        return User::where($column, $value)->get();
    }

    /**
     * Busca el primer usuario que coincida con un conjunto de atributos.
     *
     * @param array $attributes Un array asociativo de atributos (columna => valor).
     */
    public function findByAttributes($attributes)
    {
        $this->assertAllowedAttributes($attributes);
        return User::where($attributes)->first();
    }

    /**
     * Busca todos los usuarios que coincidan con un conjunto de atributos.
     *
     * @param array $attributes Un array asociativo de atributos (columna => valor).
     */
    public function findByAllAttributes($attributes)
    {
        $this->assertAllowedAttributes($attributes);
        return User::where($attributes)->get();
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
}
