<?php

namespace App\Repositories;

use App\Ia\Mongo\MongoClientFactory;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

class CompanyRepository extends Repository
{
    private function collection(): Collection
    {
        return MongoClientFactory::database()->selectCollection('companies');
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
        return $this->many($this->collection()->find([], ['sort' => ['company_id' => 1]]));
    }

    public function find($value)
    {
        return $this->map($this->collection()->findOne(['company_id' => (int) $value]));
    }

    public function findBy($column, $value)
    {
        return $this->map($this->collection()->findOne([$column => $value]));
    }

    public function findByAll($column, $value)
    {
        return $this->many($this->collection()->find([$column => $value], ['sort' => ['company_id' => 1]]));
    }

    public function findByAttributes($attributes, $relations = [])
    {
        return $this->map($this->collection()->findOne($attributes));
    }

    public function findByAllAttributes($attributes)
    {
        return $this->many($this->collection()->find($attributes, ['sort' => ['company_id' => 1]]));
    }

    public function nextCompanyId(): int
    {
        $last = $this->collection()->findOne([], ['sort' => ['company_id' => -1], 'projection' => ['company_id' => 1]]);
        return $last ? ((int) $last->company_id + 1) : 1;
    }

    public function saveMongo(object $model): bool
    {
        $attrs = method_exists($model, 'getAttributes') ? $model->getAttributes() : (array) $model;
        if (empty($attrs['company_id'])) {
            $attrs['company_id'] = $this->nextCompanyId();
            $model->company_id = $attrs['company_id'];
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
        $id = (int) ($attrs['company_id'] ?? 0);
        if ($id <= 0) return false;
        $attrs['updated_at'] = new UTCDateTime((int) (microtime(true) * 1000));
        $result = $this->collection()->updateOne(['company_id' => $id], ['$set' => $attrs]);
        return $result->isAcknowledged();
    }

    public function deleteMongo(object $model): bool
    {
        $id = (int) (($model->company_id ?? 0));
        if ($id <= 0) return false;
        $result = $this->collection()->deleteOne(['company_id' => $id]);
        return $result->isAcknowledged();
    }
}
