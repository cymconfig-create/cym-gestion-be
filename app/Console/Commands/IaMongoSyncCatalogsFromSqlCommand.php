<?php

namespace App\Console\Commands;

use App\Ia\Mongo\MongoClientFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;

class IaMongoSyncCatalogsFromSqlCommand extends Command
{
    protected $signature = 'ia:mongo-sync-catalogs-from-sql {--truncate : Limpia colecciones de catálogos antes de sincronizar}';

    protected $description = '[IA] Sincroniza catálogos base (profiles/selectors/menus/sub_menus/menu_profiles) de MySQL hacia MongoDB.';

    public function handle(): int
    {
        $db = MongoClientFactory::database();
        $collections = [
            'profiles' => ['table' => 'profiles', 'pk' => 'profile_id'],
            'selectors' => ['table' => 'selectors', 'pk' => 'selector_id'],
            'menus' => ['table' => 'menus', 'pk' => 'menu_id'],
            'sub_menus' => ['table' => 'sub_menus', 'pk' => 'sub_menu_id'],
            'menu_profiles' => ['table' => 'menu_profiles', 'pk' => 'menu_profile_id'],
        ];

        if ($this->option('truncate')) {
            foreach (array_keys($collections) as $name) {
                $db->selectCollection($name)->deleteMany([]);
            }
        }

        foreach ($collections as $collectionName => $meta) {
            $rows = DB::table($meta['table'])->get();
            $collection = $db->selectCollection($collectionName);

            foreach ($rows as $row) {
                $data = (array) $row;
                if (isset($data['created_at']) && $data['created_at']) {
                    $data['created_at'] = new UTCDateTime((int) (strtotime((string) $data['created_at']) * 1000));
                }
                if (isset($data['updated_at']) && $data['updated_at']) {
                    $data['updated_at'] = new UTCDateTime((int) (strtotime((string) $data['updated_at']) * 1000));
                }
                foreach (['status'] as $boolField) {
                    if (array_key_exists($boolField, $data) && $data[$boolField] !== null) {
                        $data[$boolField] = (bool) $data[$boolField];
                    }
                }
                foreach ([
                    'profile_id',
                    'selector_id',
                    'menu_id',
                    'sub_menu_id',
                    'menu_profile_id',
                    'position',
                    'order',
                ] as $intField) {
                    if (array_key_exists($intField, $data) && $data[$intField] !== null) {
                        $data[$intField] = (int) $data[$intField];
                    }
                }

                $collection->updateOne(
                    [$meta['pk'] => (int) $row->{$meta['pk']}],
                    ['$set' => $data],
                    ['upsert' => true]
                );
            }

            $this->line(sprintf('%s: %d sincronizados', $collectionName, $rows->count()));
        }

        $this->info('Catálogos sincronizados en Mongo.');

        return self::SUCCESS;
    }
}

