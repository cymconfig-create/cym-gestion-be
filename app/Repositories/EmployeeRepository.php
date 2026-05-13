<?php

namespace App\Repositories;

use App\Ia\Mongo\MongoClientFactory;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

class EmployeeRepository extends Repository
{
    private function collection(): Collection
    {
        return MongoClientFactory::database()->selectCollection('employees');
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
        return $this->many($this->collection()->find([], ['sort' => ['employee_id' => 1]]));
    }

    public function find($value)
    {
        return $this->map($this->collection()->findOne(['employee_id' => (int) $value]));
    }

    public function findBy($column, $value)
    {
        return $this->map($this->collection()->findOne([$column => $value]));
    }

    public function findByAll($column, $value)
    {
        return $this->many($this->collection()->find([$column => $value], ['sort' => ['employee_id' => 1]]));
    }

    public function findByAttributes($attributes)
    {
        return $this->map($this->collection()->findOne($attributes));
    }

    public function findByAllAttributes($attributes)
    {
        return $this->many($this->collection()->find($attributes, ['sort' => ['employee_id' => 1]]));
    }

    public function nextEmployeeId(): int
    {
        $last = $this->collection()->findOne([], ['sort' => ['employee_id' => -1], 'projection' => ['employee_id' => 1]]);
        return $last ? ((int) $last->employee_id + 1) : 1;
    }

    public function countByCompanyId(int $companyId): int
    {
        return $this->collection()->countDocuments(['company_id' => $companyId]);
    }

    public function saveMongo(object $model): bool
    {
        $attrs = method_exists($model, 'getAttributes') ? $model->getAttributes() : (array) $model;
        if (empty($attrs['employee_id'])) {
            $attrs['employee_id'] = $this->nextEmployeeId();
            $model->employee_id = $attrs['employee_id'];
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
        $id = (int) ($attrs['employee_id'] ?? 0);
        if ($id <= 0) return false;
        $attrs['updated_at'] = new UTCDateTime((int) (microtime(true) * 1000));
        $result = $this->collection()->updateOne(['employee_id' => $id], ['$set' => $attrs]);
        return $result->isAcknowledged();
    }

    public function deleteMongo(object $model): bool
    {
        $id = (int) (($model->employee_id ?? 0));
        if ($id <= 0) return false;
        $result = $this->collection()->deleteOne(['employee_id' => $id]);
        return $result->isAcknowledged();
    }
}
