<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{

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
        return User::where($column, $value)->get();
    }

    /**
     * Busca el primer usuario que coincida con un conjunto de atributos.
     *
     * @param array $attributes Un array asociativo de atributos (columna => valor).
     */
    public function findByAttributes($attributes)
    {
        return User::where($attributes)->first();
    }

    /**
     * Busca todos los usuarios que coincidan con un conjunto de atributos.
     *
     * @param array $attributes Un array asociativo de atributos (columna => valor).
     */
    public function findByAllAttributes($attributes)
    {
        return User::where($attributes)->get();
    }
}
