<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SelectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('selectors')->truncate(); // Truncate the table for a clean re-seed

        // Define all selectors with parent codes instead of IDs
        $selectors = [
            // Tipo de identificación
            ['code' => 'TYPE_ID', 'name' => 'Tipo de identificación', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CC', 'name' => 'Cédula de ciudadanía', 'order' => 20, 'dad_selector_code' => 'TYPE_ID', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CE', 'name' => 'Cédula de extranjería', 'order' => 40, 'dad_selector_code' => 'TYPE_ID', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Pasaporte', 'order' => 60, 'dad_selector_code' => 'TYPE_ID', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Tipo de persona
            ['code' => 'PERSON_TYPE', 'name' => 'Tipo de persona', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Natural', 'order' => 20, 'dad_selector_code' => 'PERSON_TYPE', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Jurídica', 'order' => 40, 'dad_selector_code' => 'PERSON_TYPE', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Régimen fiscal
            ['code' => 'TAX_REGIME', 'name' => 'Régimen Tributario', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Común', 'order' => 20, 'dad_selector_code' => 'TAX_REGIME', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Simplificado', 'order' => 40, 'dad_selector_code' => 'TAX_REGIME', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Especial', 'order' => 60, 'dad_selector_code' => 'TAX_REGIME', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Nivel académico
            ['code' => 'ACADEMIC_BG', 'name' => 'Nivel académico', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Bachiller', 'order' => 20, 'dad_selector_code' => 'ACADEMIC_BG', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Técnico', 'order' => 40, 'dad_selector_code' => 'ACADEMIC_BG', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Tecnólogo', 'order' => 60, 'dad_selector_code' => 'ACADEMIC_BG', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Profesional', 'order' => 80, 'dad_selector_code' => 'ACADEMIC_BG', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Especialización', 'order' => 100, 'dad_selector_code' => 'ACADEMIC_BG', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Maestría', 'order' => 120, 'dad_selector_code' => 'ACADEMIC_BG', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Tipo de contrato de trabajo
            ['code' => 'EMPLOY_CONTRACT', 'name' => 'Tipo de contrato de trabajo', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Contrato a término indefinido', 'order' => 20, 'dad_selector_code' => 'EMPLOY_CONTRACT', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Contrato a término fijo', 'order' => 40, 'dad_selector_code' => 'EMPLOY_CONTRACT', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Contrato de obra o labor', 'order' => 60, 'dad_selector_code' => 'EMPLOY_CONTRACT', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Contrato de aprendizaje', 'order' => 80, 'dad_selector_code' => 'EMPLOY_CONTRACT', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Contrato ocasional, accidental o transitorio', 'order' => 100, 'dad_selector_code' => 'EMPLOY_CONTRACT', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Estado civil
            ['code' => 'MARITAL_STATUS', 'name' => 'Estado civil', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Soltero', 'order' => 20, 'dad_selector_code' => 'MARITAL_STATUS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Casado', 'order' => 40, 'dad_selector_code' => 'MARITAL_STATUS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Unión libre', 'order' => 60, 'dad_selector_code' => 'MARITAL_STATUS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Separado', 'order' => 80, 'dad_selector_code' => 'MARITAL_STATUS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Viudo', 'order' => 100, 'dad_selector_code' => 'MARITAL_STATUS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Riesgos laborales
            ['code' => 'LABOR_RISKS', 'name' => 'Riesgos laborales (Clasificación ARL)', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Clase I - Riesgo Mínimo', 'order' => 20, 'dad_selector_code' => 'LABOR_RISKS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Clase II - Riesgo Bajo', 'order' => 40, 'dad_selector_code' => 'LABOR_RISKS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Clase III - Riesgo Medio', 'order' => 60, 'dad_selector_code' => 'LABOR_RISKS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Clase IV - Riesgo Alto', 'order' => 80, 'dad_selector_code' => 'LABOR_RISKS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Clase V - Riesgo Máximo', 'order' => 100, 'dad_selector_code' => 'LABOR_RISKS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Administradora de Riesgos Laborales (ARL)
            ['code' => 'ARL', 'name' => 'Administradora de Riesgos Laborales (ARL)', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Axa Colpatria', 'order' => 20, 'dad_selector_code' => 'ARL', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Colmena Seguros', 'order' => 40, 'dad_selector_code' => 'ARL', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'La Equidad Seguros', 'order' => 60, 'dad_selector_code' => 'ARL', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Liberty Seguros', 'order' => 80, 'dad_selector_code' => 'ARL', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Mapfre Colombia Vida Seguros S.A.', 'order' => 100, 'dad_selector_code' => 'ARL', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Positiva Compañía de Seguros', 'order' => 120, 'dad_selector_code' => 'ARL', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Seguros de Vida Alfa S.A.', 'order' => 140, 'dad_selector_code' => 'ARL', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Seguros Bolívar S.A.', 'order' => 160, 'dad_selector_code' => 'ARL', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'SURA', 'order' => 180, 'dad_selector_code' => 'ARL', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Compañía de Seguros de Vida Aurora S.A.', 'order' => 200, 'dad_selector_code' => 'ARL', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Entidades Promotoras de Salud (EPS)
            ['code' => 'EPS', 'name' => 'Entidades Promotoras de Salud (EPS)', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Coosalud EPS', 'order' => 20, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Nueva EPS', 'order' => 40, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Mutual Ser EPS', 'order' => 60, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Salud Mía EPS', 'order' => 80, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Compensar EPS', 'order' => 100, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Famisanar EPS', 'order' => 120, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Capital Salud EPS', 'order' => 140, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Aliansalud EPS', 'order' => 160, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Salud Total EPS', 'order' => 180, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'EPS Sanitas', 'order' => 200, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Sura EPS', 'order' => 220, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Servicio Occidental de Salud (SOS)', 'order' => 240, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Comfenalco Valle', 'order' => 260, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'EPM - Empresas Públicas de Medellín', 'order' => 280, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Fondo de Pasivo Social de Ferrocarriles Nacionales de Colombia', 'order' => 300, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Salud Bolívar EPS', 'order' => 320, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Cajacopi Atlántico', 'order' => 340, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Capresoca EPS', 'order' => 360, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Comfachocó', 'order' => 380, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Comfaoriente', 'order' => 400, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'EPS Familiar de Colombia', 'order' => 420, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Asmet Salud', 'order' => 440, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Emssanar', 'order' => 460, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Savia Salud EPS', 'order' => 480, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Dusakawi EPSI', 'order' => 500, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Asociación Indígena del Cauca EPSI', 'order' => 520, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Anas Wayuu EPSI', 'order' => 540, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Mallamas EPSI', 'order' => 560, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Pijaos Salud EPSI', 'order' => 580, 'dad_selector_code' => 'EPS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Fondos de pensión
            ['code' => 'PENSION_FUNDS', 'name' => 'Fondos de pensión', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Colpensiones', 'order' => 20, 'dad_selector_code' => 'PENSION_FUNDS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Porvenir', 'order' => 40, 'dad_selector_code' => 'PENSION_FUNDS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Protección', 'order' => 60, 'dad_selector_code' => 'PENSION_FUNDS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Colfondos', 'order' => 80, 'dad_selector_code' => 'PENSION_FUNDS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Skandia', 'order' => 100, 'dad_selector_code' => 'PENSION_FUNDS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Fondos de cesantías
            ['code' => 'SEVERANCE_FUNDS', 'name' => 'Fondos de cesantías', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Porvenir', 'order' => 20, 'dad_selector_code' => 'SEVERANCE_FUNDS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Protección', 'order' => 40, 'dad_selector_code' => 'SEVERANCE_FUNDS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Colfondos', 'order' => 60, 'dad_selector_code' => 'SEVERANCE_FUNDS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Skandia', 'order' => 80, 'dad_selector_code' => 'SEVERANCE_FUNDS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => null, 'name' => 'Fondo Nacional del Ahorro - FNA', 'order' => 100, 'dad_selector_code' => 'SEVERANCE_FUNDS', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Tipos de sangre
            ['code' => 'BLOOD_TYPE', 'name' => 'Tipos de sangre', 'order' => null, 'dad_selector_code' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'A+', 'name' => 'A+', 'order' => 20, 'dad_selector_code' => 'BLOOD_TYPE', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'A-', 'name' => 'A-', 'order' => 40, 'dad_selector_code' => 'BLOOD_TYPE', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'B+', 'name' => 'B+', 'order' => 60, 'dad_selector_code' => 'BLOOD_TYPE', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'B-', 'name' => 'B-', 'order' => 80, 'dad_selector_code' => 'BLOOD_TYPE', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'AB+', 'name' => 'AB+', 'order' => 100, 'dad_selector_code' => 'BLOOD_TYPE', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'AB-', 'name' => 'AB-', 'order' => 120, 'dad_selector_code' => 'BLOOD_TYPE', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'O+', 'name' => 'O+', 'order' => 140, 'dad_selector_code' => 'BLOOD_TYPE', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'O-', 'name' => 'O-', 'order' => 160, 'dad_selector_code' => 'BLOOD_TYPE', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('selectors')->insert($selectors);
    }
}
