<?php

namespace App\Repositories;

use App\Ia\Mongo\MongoClientFactory;
use App\Util\Constants;
use InvalidArgumentException;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

class AttachmentRepository extends Repository
{
    private const ALLOWED_FILTER_COLUMNS = [
        'attachment_id',
        'document_id',
        'company_id',
        'employee_id',
        'route_file',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    private function collection(): Collection
    {
        return MongoClientFactory::database()->selectCollection('attachments');
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
        return $this->many($this->collection()->find([], ['sort' => ['attachment_id' => 1]]));
    }

    public function find($value)
    {
        return $this->map($this->collection()->findOne(['attachment_id' => (int) $value]));
    }

    public function findBy($column, $value)
    {
        $this->assertAllowedColumn($column);
        return $this->map($this->collection()->findOne([$column => $this->normalizeValue($column, $value)]));
    }

    public function findByAll($column, $value)
    {
        $this->assertAllowedColumn($column);
        $query = [];
        if ($value === 'null') {
            $query[$column] = null;
        } else {
            $query[$column] = $this->normalizeValue($column, $value);
        }

        return $this->many($this->collection()->find($query, ['sort' => ['attachment_id' => 1]]));
    }

    public function findByAttributes($attributes)
    {
        $this->assertAllowedAttributes($attributes);
        $query = [];
        foreach ($attributes as $column => $value) {
            $query[$column] = $this->normalizeValue($column, $value);
        }
        return $this->map($this->collection()->findOne($query));
    }

    public function findByAllAttributes($attributes)
    {
        $this->assertAllowedAttributes($attributes);
        $query = [];
        foreach ($attributes as $column => $value) {
            $query[$column] = $this->normalizeValue($column, $value);
        }
        return $this->many($this->collection()->find($query, ['sort' => ['attachment_id' => 1]]));
    }

    public function nextAttachmentId(): int
    {
        $last = $this->collection()->findOne([], ['sort' => ['attachment_id' => -1], 'projection' => ['attachment_id' => 1]]);
        return $last ? ((int) $last->attachment_id + 1) : 1;
    }

    public function saveMongo(object $model): bool
    {
        $attrs = method_exists($model, 'getAttributes') ? $model->getAttributes() : (array) $model;
        if (empty($attrs['attachment_id'])) {
            $attrs['attachment_id'] = $this->nextAttachmentId();
            $model->attachment_id = $attrs['attachment_id'];
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
        $id = (int) ($attrs['attachment_id'] ?? 0);
        if ($id <= 0) return false;
        $attrs['updated_at'] = new UTCDateTime((int) (microtime(true) * 1000));
        $result = $this->collection()->updateOne(['attachment_id' => $id], ['$set' => $attrs]);
        return $result->isAcknowledged();
    }

    public function deleteMongo(object $model): bool
    {
        $id = (int) (($model->attachment_id ?? 0));
        if ($id <= 0) return false;
        $result = $this->collection()->deleteOne(['attachment_id' => $id]);
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
        if (in_array($column, ['attachment_id', 'document_id', 'company_id', 'employee_id'], true)) {
            return $value === null ? null : (int) $value;
        }
        return $value;
    }
}
