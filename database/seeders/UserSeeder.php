<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'willinton',
                'password' => '$2y$12$DthNdXP5SV4VFsUDdPEkw.SR1O2P5ej0zoDAULmcrYu8hhaZ2bvBq',
                'profile_id' => 1,
                'created_by' => '',
                'updated_by' => 'elazo',
                'status' => 1,
                'created_at' => '2025-03-09 00:20:34',
                'updated_at' => '2025-07-02 00:10:01',
            ],
            [
                'name' => 'elazo',
                'password' => '$2y$12$TEwWc3XjV9fAUrdk/MHlF.5eP89qeo.1bWA9LVVKh02JCdbVg9ysO',
                'profile_id' => 1,
                'created_by' => 'elazo',
                'updated_by' => 'elazo',
                'status' => 1,
                'created_at' => '2025-02-10 08:38:53',
                'updated_at' => '2025-08-23 17:07:49',
            ],
        ]);
    }
}
