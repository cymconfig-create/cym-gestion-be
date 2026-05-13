<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menu_profiles')->insert([
            ['profile_id' => 1, 'menu_id' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 1, 'menu_id' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 1, 'menu_id' => 3, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 1, 'menu_id' => 4, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 1, 'menu_id' => 5, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 1, 'menu_id' => 6, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 1, 'menu_id' => 7, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 1, 'menu_id' => 8, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('menu_profiles')->insert([
            ['profile_id' => 2, 'menu_id' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 2, 'menu_id' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 2, 'menu_id' => 3, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 2, 'menu_id' => 4, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 2, 'menu_id' => 5, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 2, 'menu_id' => 6, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 2, 'menu_id' => 7, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 2, 'menu_id' => 8, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('menu_profiles')->insert([
            ['profile_id' => 3, 'menu_id' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 3, 'menu_id' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 3, 'menu_id' => 3, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 3, 'menu_id' => 4, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 3, 'menu_id' => 5, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 3, 'menu_id' => 6, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 3, 'menu_id' => 7, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['profile_id' => 3, 'menu_id' => 8, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
