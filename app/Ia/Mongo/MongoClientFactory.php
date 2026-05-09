<?php

namespace App\Ia\Mongo;

use MongoDB\Client;
use MongoDB\Database;
use RuntimeException;

class MongoClientFactory
{
    public static function makeClient(): Client
    {
        $uri = (string) config('mongodb.uri', '');
        if ($uri === '') {
            throw new RuntimeException('MONGODB_URI no está definido en .env.');
        }

        return new Client($uri);
    }

    public static function database(?string $databaseName = null): Database
    {
        $name = $databaseName ?? (string) config('mongodb.database', 'cymDatabase');

        return self::makeClient()->selectDatabase($name);
    }
}
