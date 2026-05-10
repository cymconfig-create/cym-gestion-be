<?php

namespace App\Console\Commands;

use App\Ia\Mongo\MongoClientFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;

class IaMongoSyncCoreModule3FromSqlCommand extends Command
{
    protected $signature = 'ia:mongo-sync-core-module3-from-sql {--truncate : Limpia events/conversations/messages/conversation_user antes de sincronizar}';

    protected $description = '[IA] Sincroniza módulos core bloque 3 (events, conversations, messages, conversation_user) desde MySQL hacia MongoDB.';

    public function handle(): int
    {
        $db = MongoClientFactory::database();
        $collections = [
            'events' => ['table' => 'events', 'pk' => 'id'],
            'conversations' => ['table' => 'conversations', 'pk' => 'conversation_id'],
            'messages' => ['table' => 'messages', 'pk' => 'message_id'],
            'conversation_user' => ['table' => 'conversation_user', 'pk' => null],
        ];

        if ($this->option('truncate')) {
            foreach (array_keys($collections) as $name) {
                $db->selectCollection($name)->deleteMany([]);
            }
        }

        foreach ($collections as $collectionName => $meta) {
            if (!Schema::hasTable($meta['table'])) {
                $this->warn(sprintf('%s: tabla SQL "%s" no existe, se omite.', $collectionName, $meta['table']));
                continue;
            }

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
                if (isset($data['last_message_at']) && $data['last_message_at']) {
                    $data['last_message_at'] = new UTCDateTime((int) (strtotime((string) $data['last_message_at']) * 1000));
                }
                if (isset($data['last_read_at']) && $data['last_read_at']) {
                    $data['last_read_at'] = new UTCDateTime((int) (strtotime((string) $data['last_read_at']) * 1000));
                }
                foreach ([
                    'id',
                    'id_company',
                    'conversation_id',
                    'message_id',
                    'user_id',
                    'attachment_id',
                    'created_by',
                    'company_id',
                ] as $intField) {
                    if (array_key_exists($intField, $data) && $data[$intField] !== null) {
                        $data[$intField] = (int) $data[$intField];
                    }
                }
                foreach (['is_archived', 'is_deleted'] as $boolField) {
                    if (array_key_exists($boolField, $data) && $data[$boolField] !== null) {
                        $data[$boolField] = (bool) $data[$boolField];
                    }
                }

                if ($meta['pk']) {
                    $collection->updateOne(
                        [$meta['pk'] => (int) $row->{$meta['pk']}],
                        ['$set' => $data],
                        ['upsert' => true]
                    );
                } else {
                    $collection->updateOne(
                        [
                            'conversation_id' => (int) $row->conversation_id,
                            'user_id' => (int) $row->user_id,
                        ],
                        ['$set' => $data],
                        ['upsert' => true]
                    );
                }
            }

            $this->line(sprintf('%s: %d sincronizados', $collectionName, $rows->count()));
        }

        $this->info('Core módulo 3 sincronizado en Mongo.');

        return self::SUCCESS;
    }
}
