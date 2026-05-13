<?php

namespace App\Repositories;

use App\Ia\Mongo\MongoClientFactory;
use MongoDB\Collection;

class DocumentRepository extends Repository
{
    private function collection(): Collection
    {
        return MongoClientFactory::database()->selectCollection('documents');
    }

    private function map(?object $doc): ?object
    {
        if (!$doc) return null;
        $row = (array) $doc;
        unset($row['_id']);
        return (object) $row;
    }

    private function many(iterable $docs)
    {
        $rows = [];
        foreach ($docs as $doc) $rows[] = $this->map($doc);
        return collect($rows);
    }

    public function all()
    {
        return $this->many($this->collection()->find([], ['sort' => ['document_id' => 1]]));
    }

    public function find($value)
    {
        return $this->map($this->collection()->findOne(['document_id' => (int) $value]));
    }

    public function findBy($column, $value)
    {
        return $this->map($this->collection()->findOne([$column => $value]));
    }

    public function findByAll($column, $value)
    {
        return $this->many($this->collection()->find([$column => $value], ['sort' => ['document_id' => 1]]));
    }

    public function findByAttributes($attributes)
    {
        return $this->map($this->collection()->findOne($attributes));
    }

    public function findByAllAttributes($attributes)
    {
        return $this->many($this->collection()->find($attributes, ['sort' => ['document_id' => 1]]));
    }
}
