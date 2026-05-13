<?php

namespace App\Console\Commands;

use App\Ia\Mongo\MongoClientFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;

class IaMongoSyncCoreModule2FromSqlCommand extends Command
{
    protected $signature = 'ia:mongo-sync-core-module2-from-sql {--truncate : Limpia companies/employees/documents/attachments antes de sincronizar}';

    protected $description = '[IA] Sincroniza módulos core bloque 2 (companies, employees, documents, attachments) desde MySQL hacia MongoDB.';

    public function handle(): int
    {
        $db = MongoClientFactory::database();
        $collections = [
            'companies' => ['table' => 'companies', 'pk' => 'company_id'],
            'employees' => ['table' => 'employees', 'pk' => 'employee_id'],
            'documents' => ['table' => 'documents', 'pk' => 'document_id'],
            'attachments' => ['table' => 'attachments', 'pk' => 'attachment_id'],
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
                foreach ([
                    'company_id',
                    'employee_id',
                    'document_id',
                    'attachment_id',
                    'selector_person_type',
                    'selector_tax_regime',
                    'department_address',
                    'city_address',
                    'quantity_employees',
                    'selector_risk_level',
                    'selector_arl',
                    'legal_representative_id',
                    'system_manager_id',
                    'selector_identification',
                    'selector_academic_level',
                    'selector_eps',
                    'selector_pension_fund',
                    'selector_severance_fund',
                    'selector_type_of_contract',
                    'selector_blood_type',
                    'selector_civil_status',
                    'selector_identification_contact',
                    'user_id',
                    'profile_id',
                ] as $intField) {
                    if (array_key_exists($intField, $data) && $data[$intField] !== null) {
                        $data[$intField] = (int) $data[$intField];
                    }
                }
                foreach (['status', 'is_eliminated'] as $boolField) {
                    if (array_key_exists($boolField, $data) && $data[$boolField] !== null) {
                        $data[$boolField] = (bool) $data[$boolField];
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

        $this->info('Core módulo 2 sincronizado en Mongo.');

        return self::SUCCESS;
    }
}

