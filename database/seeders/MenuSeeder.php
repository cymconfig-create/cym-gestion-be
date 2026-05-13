<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menus')->insert([
            [
                'code' => 'CONTROL_PANEL',
                'name' => 'Panel de Control',
                'route' => 'controlPanel',
                'position' => 10,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'COMPANY',
                'name' => 'Empresas',
                'route' => 'companies',
                'position' => 20,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'EMPLOYEE',
                'name' => 'Empleados',
                'route' => 'employees',
                'position' => 30,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SGSST',
                'name' => 'Sistema de Gestión SST',
                'route' => 'employees',
                'position' => 40,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'IN_COMUNICATION',
                'name' => 'Comunicación Interna',
                'route' => 'internalCommunication',
                'position' => 50,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'USER_PROFILE',
                'name' => 'Perfiles de Usuarios',
                'route' => 'userProfiles',
                'position' => 60,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SUPPORT_HELP',
                'name' => 'Soporte y Ayuda',
                'route' => 'supportAndHelp',
                'position' => 70,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ACTIVITY_MONTH',
                'name' => 'Actividades del Mes',
                'route' => 'activitiesMonth',
                'position' => 80,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'LOG_OUT',
                'name' => 'Cerrar Sesión',
                'route' => 'logOut',
                'position' => 90,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}