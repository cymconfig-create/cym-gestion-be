<?php

namespace App\Auth;

use App\Ia\Mongo\MongoClientFactory;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use MongoDB\Collection;

class MongoUserProvider implements UserProvider
{
    public function __construct(private readonly Hasher $hasher) {}

    private function collection(): Collection
    {
        return MongoClientFactory::database()->selectCollection('users');
    }

    private function mapDocumentToUser(?object $doc): ?MongoUser
    {
        if (!$doc) {
            return null;
        }

        return MongoUser::fromDocument((array) $doc);
    }

    public function retrieveById($identifier): ?UserContract
    {
        $doc = $this->collection()->findOne(['user_id' => (int) $identifier]);

        return $this->mapDocumentToUser($doc);
    }

    public function retrieveByToken($identifier, $token): ?UserContract
    {
        return null;
    }

    public function updateRememberToken(UserContract $user, $token): void
    {
    }

    public function retrieveByCredentials(array $credentials): ?UserContract
    {
        if (empty($credentials)) {
            return null;
        }

        $query = [];
        foreach ($credentials as $key => $value) {
            if ($key === 'password' || $value === null || $value === '') {
                continue;
            }
            $query[$key] = $value;
        }

        if ($query === []) {
            return null;
        }

        $doc = $this->collection()->findOne($query);

        return $this->mapDocumentToUser($doc);
    }

    public function validateCredentials(UserContract $user, array $credentials): bool
    {
        $plain = (string) ($credentials['password'] ?? '');
        if ($plain === '') {
            return false;
        }

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(UserContract $user, array $credentials, bool $force = false): void
    {
    }
}

