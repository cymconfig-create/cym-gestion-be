<?php

namespace App\Console\Commands;

use App\Ia\Mongo\MongoClientFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;

class IaMongoSyncUsersFromSqlCommand extends Command
{
    protected $signature = 'ia:mongo-sync-users-from-sql {--truncate : Limpia users/profiles en Mongo antes de sincronizar}';

    protected $description = '[IA] Sincroniza usuarios y perfiles desde MySQL hacia MongoDB para autenticación.';

    public function handle(): int
    {
        $db = MongoClientFactory::database();
        $usersCollection = $db->selectCollection('users');
        $profilesCollection = $db->selectCollection('profiles');

        if ($this->option('truncate')) {
            $usersCollection->deleteMany([]);
            $profilesCollection->deleteMany([]);
        }

        $profiles = DB::table('profiles')->get();
        foreach ($profiles as $profile) {
            $profilesCollection->updateOne(
                ['profile_id' => (int) $profile->profile_id],
                ['$set' => [
                    'profile_id' => (int) $profile->profile_id,
                    'code' => (string) $profile->code,
                    'name' => (string) $profile->name,
                    'description' => $profile->description,
                    'status' => (bool) $profile->status,
                    'created_at' => new UTCDateTime((int) (strtotime((string) $profile->created_at) * 1000)),
                    'updated_at' => new UTCDateTime((int) (strtotime((string) $profile->updated_at) * 1000)),
                ]],
                ['upsert' => true]
            );
        }

        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $usersCollection->updateOne(
                ['user_id' => (int) $user->user_id],
                ['$set' => [
                    'user_id' => (int) $user->user_id,
                    'name' => (string) $user->name,
                    'email' => $user->email,
                    'password' => (string) $user->password,
                    'profile_id' => (int) $user->profile_id,
                    'employee_id' => isset($user->employee_id) ? (int) $user->employee_id : null,
                    'status' => (bool) $user->status,
                    'created_by' => $user->created_by ?? null,
                    'updated_by' => $user->updated_by ?? null,
                    'created_at' => new UTCDateTime((int) (strtotime((string) $user->created_at) * 1000)),
                    'updated_at' => new UTCDateTime((int) (strtotime((string) $user->updated_at) * 1000)),
                ]],
                ['upsert' => true]
            );
        }

        $this->info(sprintf('Sincronizados %d perfiles y %d usuarios en Mongo.', $profiles->count(), $users->count()));

        return self::SUCCESS;
    }
}

