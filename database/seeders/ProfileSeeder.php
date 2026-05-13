<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('profiles')->insert([
            [
                'code' => 'SUPER',
                'name' => 'Super Admin',
                'description' => 'Administrador absoluto del sistema.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ADMIN',
                'name' => 'Administrador',
                'description' => 'Encargado de  la administración general del Sistema de Gestión',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SGSST',
                'name' => 'Responsable del SGSST',
                'description' => 'Responsable del SGSST',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GEREN',
                'name' => 'Gerencia',
                'description' => 'Solo puede ver y no manipular',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
