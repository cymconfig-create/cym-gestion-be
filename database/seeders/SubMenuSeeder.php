<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sub_menus')->insert([
            ['code' => 'COMPANY_LIST', 'name' => 'Listado de Empresas', 'route' => 'companyList', 'position' => 10, 'menu_id' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'COMPANY_MANAGEMENT', 'name' => 'Crear Empresa', 'route' => 'companyManagement', 'position' => 20, 'menu_id' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'GENERATE_REPORTS', 'name' => 'Generar Informes', 'route' => 'generateReport', 'position' => 30, 'menu_id' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'EMPLOYEE_LIST', 'name' => 'Listado de Empleados', 'route' => 'employeesList', 'position' => 10, 'menu_id' => 3, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CREATE_EMPLOYEE', 'name' => 'Crear Empleado', 'route' => 'createEmployee', 'position' => 20, 'menu_id' => 3, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'MESSAGING', 'name' => 'Gestión de Mensajes', 'route' => 'messageManagement', 'position' => 10, 'menu_id' => 5, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'USER_MANEGEMENT', 'name' => 'Gestión de Usuarios', 'route' => 'userManagement', 'position' => 10, 'menu_id' => 6, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'TICKET_MANAGEMENT', 'name' => 'Gestión de Tickets', 'route' => 'ticketManagement', 'position' => 10, 'menu_id' => 7, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'HELP_TUTORIAL', 'name' => 'Tutoriales de Ayuda', 'route' => 'helpTutorials', 'position' => 20, 'menu_id' => 7, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'DOCUMENTATION', 'name' => 'Documentación', 'route' => 'documentation', 'position' => 30, 'menu_id' => 7, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}