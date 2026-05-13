<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('documents')->insert([
            ['document_id' => 1, 'code' => 'LOGO', 'name' => 'Logo de la Empresa', 'percentage' => null, 'status' => 1, 'created_at' => '2025-02-24 02:30:32', 'updated_at' => '2025-04-19 04:12:45'],
            ['document_id' => 2, 'code' => 'CAMARA_COMERCIO', 'name' => 'Cámara de Comercio', 'percentage' => null, 'status' => 1, 'created_at' => '2025-02-24 02:30:32', 'updated_at' => '2025-04-19 04:13:30'],
            ['document_id' => 3, 'code' => 'ACTA_CONSTITUCION', 'name' => 'Acta de Constitución', 'percentage' => null, 'status' => 1, 'created_at' => '2025-02-24 02:30:32', 'updated_at' => '2025-04-19 04:17:23'],
            ['document_id' => 4, 'code' => 'RUT', 'name' => 'Registro DIAN (RUT)', 'percentage' => null, 'status' => 1, 'created_at' => '2025-02-24 02:30:32', 'updated_at' => '2025-04-19 04:15:01'],
            ['document_id' => 5, 'code' => 'REPRESENTANTE_LEGAL', 'name' => 'Representante Legal', 'percentage' => null, 'status' => 1, 'created_at' => '2025-02-24 02:30:32', 'updated_at' => '2025-04-19 04:17:19'],
            ['document_id' => 6, 'code' => 'DOC_IDENTIDAD', 'name' => 'Documento de Identidad', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:08:48'],
            ['document_id' => 7, 'code' => 'HOJA_VIDA', 'name' => 'Hoja de Vida', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:08:53'],
            ['document_id' => 8, 'code' => 'CONTRATO_LABORAL', 'name' => 'Contrato Laboral', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:08:55'],
            ['document_id' => 9, 'code' => 'CERT_EPS', 'name' => 'Certificado EPS', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:08:59'],
            ['document_id' => 10, 'code' => 'CERT_PENSIONES', 'name' => 'Certificado Pensiones', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:09:02'],
            ['document_id' => 11, 'code' => 'CERT_CESANTIAS', 'name' => 'Certificado Cesantías', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:09:05'],
            ['document_id' => 12, 'code' => 'CERT_CAJA_COMP_FAM', 'name' => 'Certificado Caja Compensación Familiar', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:09:08'],
            ['document_id' => 13, 'code' => 'CERT_ARL', 'name' => 'Certificado ARL', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:09:11'],
            ['document_id' => 14, 'code' => 'CERT_MEDICO_INGRESO', 'name' => 'Certificado Médico de Ingreso', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:09:13'],
            ['document_id' => 15, 'code' => 'RECOMEN_OCUPACIONAL', 'name' => 'Recomendaciones Ocupacionales', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:09:17'],
            ['document_id' => 16, 'code' => 'CERT_MEDICO_EGRESO', 'name' => 'Certificado Médico de Egreso', 'percentage' => null, 'status' => 1, 'created_at' => '2025-04-21 05:07:19', 'updated_at' => '2025-04-21 05:09:19'],
            ['document_id' => 17, 'code' => 'PHOTO_PROFILE', 'name' => 'Imagen del perfil', 'percentage' => null, 'status' => 1, 'created_at' => '2025-05-03 06:56:26', 'updated_at' => '2025-05-03 06:56:26'],
            ['document_id' => 18, 'code' => 'SLIDES', 'name' => 'Slides', 'percentage' => null, 'status' => 1, 'created_at' => '2025-02-24 02:30:32', 'updated_at' => '2025-04-19 04:12:45'],
        ]);
    }
}