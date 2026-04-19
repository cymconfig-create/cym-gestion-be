<?php

namespace App\Repositories;

use App\Models\Attachment;
use App\Util\Constants;
use InvalidArgumentException;

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

    public function all()
    {
        return Attachment::all();
    }

    public function find($value)
    {
        return Attachment::find($value);
    }

    public function findBy($column, $value)
    {
        $this->assertAllowedColumn($column);
        return Attachment::where($column, $value)->first();
    }

    public function findByAll($column, $value)
    {
        $this->assertAllowedColumn($column);
        $query = Attachment::query();

        // Comprobamos si el valor que viene es la cadena "null"
        if ($value === 'null') {
            $query->whereNull($column);
        } else {
            $query->where($column, $value);
        }

        return $query->get();
    }

    public function findByAttributes($attributes)
    {
        $this->assertAllowedAttributes($attributes);
        $response = null;
        foreach ($attributes as $column => $value) {
            $response = $response == null ? Attachment::where($column, $value) : $response->where($column, $value);
        }
        return $response == null ? $response : $response->first();
    }

    public function findByAllAttributes($attributes)
    {
        $this->assertAllowedAttributes($attributes);
        $response = null;
        foreach ($attributes as $column => $value) {
            $response = $response == null ? Attachment::where($column, $value) : $response->where($column, $value);
        }
        return $response == null ? $response : $response->get();
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
