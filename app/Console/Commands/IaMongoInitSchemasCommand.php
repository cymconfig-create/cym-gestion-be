<?php

namespace App\Console\Commands;

use App\Ia\Mongo\MongoClientFactory;
use App\Ia\Mongo\MongoSchemaInitializer;
use Illuminate\Console\Command;
use Throwable;

class IaMongoInitSchemasCommand extends Command
{
    protected $signature = 'ia:mongo-init-schemas
                            {--no-validators : Crear colecciones sin validador JSON Schema}
                            {--dry-run : No conectar; solo listar colecciones a asegurar}';

    protected $description = '[IA] Crea en MongoDB Atlas las colecciones equivalentes a las tablas SQL (sin tocar la BD relacional).';

    public function handle(): int
    {
        if ($this->option('dry-run')) {
            $this->info('Colecciones que se asegurarían en la base "' . config('mongodb.database') . '":');
            foreach (\App\Ia\Mongo\MongoSchemaRegistry::collectionNames() as $name) {
                $this->line(' - ' . $name);
            }

            return self::SUCCESS;
        }

        try {
            $db = MongoClientFactory::database();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $initializer = new MongoSchemaInitializer;
        $withValidators = !$this->option('no-validators');

        try {
            $rows = $initializer->ensureCollections($db, $withValidators);
        } catch (Throwable $e) {
            $this->error('Error al crear colecciones: ' . $e->getMessage());

            return self::FAILURE;
        }

        foreach ($rows as $row) {
            $this->line(sprintf('[%s] %s', $row['status'], $row['name']));
        }

        $this->info('Listo. Base de datos Mongo: ' . config('mongodb.database'));

        return self::SUCCESS;
    }
}
