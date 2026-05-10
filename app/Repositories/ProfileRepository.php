<?php

namespace App\Repositories;

use App\Ia\Mongo\MongoClientFactory;
use InvalidArgumentException;
use MongoDB\Collection;

class ProfileRepository extends Repository
{
    private const ALLOWED_FILTER_COLUMNS = [
        'profile_id',
        'code',
        'name',
        'status',
    ];

    private function profiles(): Collection
    {
        return MongoClientFactory::database()->selectCollection('profiles');
    }

    private function menus(): Collection
    {
        return MongoClientFactory::database()->selectCollection('menus');
    }

    private function subMenus(): Collection
    {
        return MongoClientFactory::database()->selectCollection('sub_menus');
    }

    private function menuProfiles(): Collection
    {
        return MongoClientFactory::database()->selectCollection('menu_profiles');
    }

    private function mapDoc(?object $doc): ?object
    {
        if (!$doc) {
            return null;
        }

        $row = (array) $doc;
        unset($row['_id']);

        return (object) $row;
    }

    public function all()
    {
        $loggedInUser = auth()->user();
        $cursor = $this->profiles()->find([], ['sort' => ['profile_id' => 1]]);
        $rows = [];
        foreach ($cursor as $doc) {
            $rows[] = $this->mapDoc($doc);
        }

        if (!$loggedInUser) {
            return collect($rows);
        }

        $loggedProfile = $this->find((int) $loggedInUser->profile_id);
        if (($loggedProfile->code ?? null) === 'SUPER') {
            return collect($rows);
        }

        return collect($rows)->filter(fn ($p) => !in_array($p->code ?? null, ['SUPER', 'ADMIN'], true))->values();
    }

    public function find($value)
    {
        return $this->mapDoc($this->profiles()->findOne(['profile_id' => (int) $value]));
    }

    public function findBy($column, $value)
    {
        $this->assertAllowedColumn($column);
        return $this->mapDoc($this->profiles()->findOne([$column => $this->normalizeValue($column, $value)]));
    }

    public function findByAll($column, $value)
    {
        $this->assertAllowedColumn($column);
        $cursor = $this->profiles()->find([$column => $this->normalizeValue($column, $value)], ['sort' => ['profile_id' => 1]]);
        $rows = [];
        foreach ($cursor as $doc) {
            $rows[] = $this->mapDoc($doc);
        }

        return collect($rows);
    }

    public function findByAttributes($attributes)
    {
        $query = [];
        foreach ($attributes as $column => $value) {
            $this->assertAllowedColumn($column);
            $query[$column] = $this->normalizeValue($column, $value);
        }

        return $this->mapDoc($this->profiles()->findOne($query));
    }

    public function findByAllAttributes($attributes)
    {
        $query = [];
        foreach ($attributes as $column => $value) {
            $this->assertAllowedColumn($column);
            $query[$column] = $this->normalizeValue($column, $value);
        }

        $cursor = $this->profiles()->find($query, ['sort' => ['profile_id' => 1]]);
        $rows = [];
        foreach ($cursor as $doc) {
            $rows[] = $this->mapDoc($doc);
        }

        return collect($rows);
    }

    function findMenusByProfile($profile_id)
    {
        $profile = $this->find((int) $profile_id);
        if (!$profile) {
            return collect([]);
        }

        $relations = $this->menuProfiles()->find(['profile_id' => (int) $profile_id, 'status' => true]);
        $allowedMenuIds = [];
        foreach ($relations as $rel) {
            $allowedMenuIds[] = (int) ($rel->menu_id ?? 0);
        }
        $allowedMenuIds = array_values(array_unique(array_filter($allowedMenuIds)));

        $menus = [];
        if ($allowedMenuIds !== []) {
            $menuDocs = $this->menus()->find(
                ['menu_id' => ['$in' => $allowedMenuIds], 'status' => true],
                ['sort' => ['position' => 1]]
            );

            foreach ($menuDocs as $menuDoc) {
                $menu = (array) $menuDoc;
                unset($menu['_id']);
                $subMenuDocs = $this->subMenus()->find(
                    ['menu_id' => (int) $menu['menu_id'], 'status' => true],
                    ['sort' => ['position' => 1]]
                );
                $subMenus = [];
                foreach ($subMenuDocs as $smDoc) {
                    $sm = (array) $smDoc;
                    unset($sm['_id']);
                    $subMenus[] = (object) $sm;
                }
                $menu['sub_menus'] = $subMenus;
                $menus[] = (object) $menu;
            }
        }

        $profile->menus = $menus;

        return collect([$profile]);
    }

    private function assertAllowedColumn(string $column): void
    {
        if (!in_array($column, self::ALLOWED_FILTER_COLUMNS, true)) {
            throw new InvalidArgumentException('Invalid profile filter column');
        }
    }

    private function normalizeValue(string $column, mixed $value): mixed
    {
        if ($column === 'profile_id') {
            return (int) $value;
        }
        if ($column === 'status') {
            return (bool) $value;
        }

        return $value;
    }
}
