<?php

namespace Tests\Ia;

use App\Ia\Mongo\MongoClientFactory;
use App\Ia\Mongo\MongoSchemaInitializer;
use App\Ia\Mongo\MongoSchemaRegistry;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Throwable;

class MongoSchemaInitializationTest extends TestCase
{
    public function test_registry_lists_all_sql_mirror_collections(): void
    {
        $names = MongoSchemaRegistry::collectionNames();
        $this->assertContains('users', $names);
        $this->assertContains('companies', $names);
        $this->assertContains('attachments', $names);
        $this->assertGreaterThan(10, count($names));
    }

    public function test_artisan_dry_run_does_not_require_mongodb_extension(): void
    {
        $exit = Artisan::call('ia:mongo-init-schemas', ['--dry-run' => true]);
        $this->assertSame(0, $exit);
        $this->assertStringContainsString('users', Artisan::output());
    }

    public function test_initializer_creates_collections_when_mongodb_configured(): void
    {
        if (!extension_loaded('mongodb')) {
            $this->markTestSkipped('Extensión PHP mongodb no instalada.');
        }

        $uri = (string) env('MONGODB_URI', '');
        if ($uri === '') {
            $this->markTestSkipped('MONGODB_URI vacío: defina la URI en .env para prueba de integración.');
        }

        try {
            $db = MongoClientFactory::database();
        } catch (Throwable $e) {
            $this->markTestSkipped('No se pudo conectar a MongoDB: ' . $e->getMessage());
        }

        $initializer = new MongoSchemaInitializer;
        $results = $initializer->ensureCollections($db, true);

        $this->assertNotEmpty($results);
        foreach ($results as $row) {
            $this->assertContains($row['status'], ['created', 'exists'], $row['name']);
        }
    }
}
