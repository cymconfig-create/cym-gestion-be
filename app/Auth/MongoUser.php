<?php

namespace App\Auth;

use MongoDB\BSON\UTCDateTime;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class MongoUser extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'full_name',
        'identification_number',
        'password',
        'profile_id',
        'employee_id',
        'all_companies',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
    ];

    public static function fromDocument(array $doc): self
    {
        $user = new self();
        $data = [
            'user_id' => (int) ($doc['user_id'] ?? 0),
            'name' => (string) ($doc['name'] ?? ''),
            'email' => $doc['email'] ?? null,
            'full_name' => $doc['full_name'] ?? null,
            'identification_number' => $doc['identification_number'] ?? null,
            'password' => (string) ($doc['password'] ?? ''),
            'profile_id' => (int) ($doc['profile_id'] ?? 0),
            'employee_id' => isset($doc['employee_id']) ? (int) $doc['employee_id'] : null,
            'all_companies' => (bool) ($doc['all_companies'] ?? false),
            'status' => (bool) ($doc['status'] ?? true),
            'created_by' => $doc['created_by'] ?? null,
            'updated_by' => $doc['updated_by'] ?? null,
            'created_at' => self::normalizeDate($doc['created_at'] ?? null),
            'updated_at' => self::normalizeDate($doc['updated_at'] ?? null),
        ];
        $user->forceFill($data);
        $user->syncOriginal();

        return $user;
    }

    private static function normalizeDate(mixed $value): mixed
    {
        if ($value instanceof UTCDateTime) {
            return $value->toDateTime();
        }

        if (is_numeric($value)) {
            $seconds = (int) floor(((int) $value) / 1000);
            return date('Y-m-d H:i:s', $seconds);
        }

        return $value;
    }

    public function getAuthIdentifierName(): string
    {
        return 'user_id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->attributes['user_id'] ?? null;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): string
    {
        return (string) ($this->attributes['password'] ?? '');
    }

    public function getJWTIdentifier()
    {
        return $this->getAuthIdentifier();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
