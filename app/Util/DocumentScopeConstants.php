<?php

namespace App\Util;

/**
 * Códigos de documento por ámbito (alineados con DocumentSeeder).
 * `all` no usa listas: incluye todos los documentos activos.
 */
class DocumentScopeConstants
{
    public const SCOPE_AUTO = 'auto';

    public const SCOPE_EMPLOYEE = 'employee';

    public const SCOPE_COMPANY = 'company';

    public const SCOPE_ALL = 'all';

    /** Documentos típicos de expediente de empleado */
    public const EMPLOYEE_DOCUMENT_CODES = [
        'DOC_IDENTIDAD',
        'HOJA_VIDA',
        'CONTRATO_LABORAL',
        'CERT_EPS',
        'CERT_PENSIONES',
        'CERT_CESANTIAS',
        'CERT_CAJA_COMP_FAM',
        'CERT_ARL',
        'CERT_MEDICO_INGRESO',
        'RECOMEN_OCUPACIONAL',
        'CERT_MEDICO_EGRESO',
        'PHOTO_PROFILE',
    ];

    /** Documentos típicos de constitución / datos de empresa */
    public const COMPANY_DOCUMENT_CODES = [
        'LOGO',
        'CAMARA_COMERCIO',
        'ACTA_CONSTITUCION',
        'RUT',
        'REPRESENTANTE_LEGAL',
        'SLIDES',
    ];
}
