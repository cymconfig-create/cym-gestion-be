<?php

namespace App\Repositories;

use App\Ia\Mongo\MongoClientFactory;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

class EventRepository extends Repository
{
    private function collection(): Collection
    {
        return MongoClientFactory::database()->selectCollection('events');
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
        return $this->many($this->collection()->find([], ['sort' => ['id' => 1]]));
    }

    public function find($value)
    {
        return $this->map($this->collection()->findOne(['id' => (int) $value]));
    }

    public function findBy($column, $value)
    {
        return $this->map($this->collection()->findOne([$column => $value]));
    }

    public function findByAll($column, $value)
    {
        return $this->many($this->collection()->find([$column => $value], ['sort' => ['id' => 1]]));
    }

    public function findByAttributes($attributes)
    {
        return $this->map($this->collection()->findOne($attributes));
    }

    public function findByAllAttributes($attributes)
    {
        return $this->many($this->collection()->find($attributes, ['sort' => ['id' => 1]]));
    }

    public function nextId(): int
    {
        $last = $this->collection()->findOne([], ['sort' => ['id' => -1], 'projection' => ['id' => 1]]);
        return $last ? ((int) $last->id + 1) : 1;
    }

    public function saveMongo(object $model): bool
    {
        $attrs = method_exists($model, 'getAttributes') ? $model->getAttributes() : (array) $model;
        if (empty($attrs['id'])) {
            $attrs['id'] = $this->nextId();
            $model->id = $attrs['id'];
        }
        $now = new UTCDateTime((int) (microtime(true) * 1000));
        $attrs['created_at'] = $attrs['created_at'] ?? $now;
        $attrs['updated_at'] = $now;
        $result = $this->collection()->insertOne($attrs);
        return $result->isAcknowledged();
    }

    public function updateMongo(object $model): bool
    {
        $attrs = method_exists($model, 'getAttributes') ? $model->getAttributes() : (array) $model;
        $id = (int) ($attrs['id'] ?? 0);
        if ($id <= 0) return false;
        $attrs['updated_at'] = new UTCDateTime((int) (microtime(true) * 1000));
        $result = $this->collection()->updateOne(['id' => $id], ['$set' => $attrs]);
        return $result->isAcknowledged();
    }

    public function deleteMongo(object $model): bool
    {
        $id = (int) ($model->id ?? 0);
        if ($id <= 0) return false;
        $result = $this->collection()->deleteOne(['id' => $id]);
        return $result->isAcknowledged();
    }
}
