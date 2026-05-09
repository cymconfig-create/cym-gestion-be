# Módulo IA — API y MongoDB (paralelo a SQL)

Esta carpeta agrupa la **documentación** del módulo orientado a integraciones y análisis (prefijo de rutas `api/ia`). El código vive en Laravel bajo `App\Http\Controllers\Ia`, `App\Services\Ia` y `App\Ia\Mongo`.

## MongoDB Atlas (estructura espejo del modelo relacional)

- **Documentación de mapeo SQL → colecciones:** [MONGODB_STRUCTURE.md](MONGODB_STRUCTURE.md)
- **Pruebas y plantilla de resultados:** [pruebas/README.md](pruebas/README.md)
- **Configuración:** `config/mongodb.php` y variables `MONGODB_URI`, `MONGODB_DATABASE` en `.env` (ver `.env.example`).
- **Comando para crear colecciones / esquemas:** `php artisan ia:mongo-init-schemas` (opciones `--dry-run`, `--no-validators`).

La conexión **SQL** del proyecto (`DB_CONNECTION`, `config/database.php`) **no se sustituye** ni se modifica para el driver por defecto.

---

# API — Cumplimiento de documentos

Esta sección describe el endpoint HTTP bajo `api/ia`.

## Endpoint disponible

### Cumplimiento de carga de documentos

| Método | Ruta |
|--------|------|
| `GET` | `/api/ia/documents/upload-completion` |

**Autenticación:** igual que el resto de la API: header `Authorization: Bearer {token}` (JWT).

**Descripción:** calcula el **porcentaje de avance** según los tipos de documento requeridos en el sistema (`documents` activos) frente a los **adjuntos** (`attachments`) que cumplen el filtro de contexto (empleado, empresa o usuario que subió el archivo).

## Parámetros de consulta (query string)

| Parámetro | Obligatorio | Valores | Comportamiento |
|-----------|-------------|---------|----------------|
| `scope` | No | `auto` (defecto), `employee`, `company`, `all` | `auto`: si el usuario tiene `employee_id`, el ámbito es empleado; si no, empresa. |
| `employee_id` | Condicional | número | Empleado a evaluar. Si no se envía y el usuario tiene empleado vinculado, se usa el suyo. En `scope=employee` es obligatorio si el usuario no es empleado. |
| `company_id` | Condicional | número | Empresa para documentos de compañía (adjuntos con ese `company_id` y `employee_id` nulo). Con `scope=company` puede omitirse si el usuario tiene empleado con `company_id`. Si se envía en la petición, **tiene prioridad** sobre el filtro por empleado (útil para administradores con usuario–empleado). |

## Cómo se calcula el porcentaje

1. **Documentos requeridos:** registros en `documents` con `status = 1`, filtrados por ámbito mediante códigos definidos en `App\Util\DocumentScopeConstants` (listas `EMPLOYEE_DOCUMENT_CODES` y `COMPANY_DOCUMENT_CODES`, alineadas con el seeder).
2. **Considerado “subido”:** existe al menos un `attachment` con ese `document_id` que cumple el filtro activo:
   - **Empresa:** `company_id` indicado y `employee_id` nulo.
   - **Empleado:** `employee_id` indicado.
   - **Modo libre** (sin empresa ni empleado en filtro): `created_by` coincide con el nombre del usuario **o** `employee_id` coincide con el del usuario.
3. **Fórmula:**
   - Si **todos** los documentos requeridos tienen el campo `percentage` informado en base de datos, el total es la **suma ponderada** de los porcentajes de los tipos que ya tienen archivo.
   - Si no (caso habitual con el seeder actual), se usa **peso uniforme:** `(cantidad de tipos con al menos un archivo / cantidad de tipos requeridos) × 100`, redondeado a dos decimales.

## Formato de respuesta

Misma envoltura que el resto de servicios (`App\Services\Service::resolve`):

```json
{
  "error": false,
  "message": "Resumen de cumplimiento de documentos",
  "status": 200,
  "data": {
    "percentage_total": 41.67,
    "total_required": 12,
    "total_uploaded": 5,
    "scope": "employee",
    "employee_id": 3,
    "company_id": null,
    "documents": [
      {
        "document_id": 6,
        "code": "DOC_IDENTIDAD",
        "name": "Documento de Identidad",
        "uploaded": true,
        "percentage": null
      }
    ]
  }
}
```

## Permisos

- Consultar el cumplimiento de **otro** `employee_id` o de **otra** `company_id` solo si el perfil del usuario es `SUPER`, `ADMIN`, `SGSST` o `GEREN`, o si coincide con el propio empleado o la empresa del empleado vinculado.

## Ejemplos de llamada

```http
GET /api/ia/documents/upload-completion
GET /api/ia/documents/upload-completion?scope=employee&employee_id=12
GET /api/ia/documents/upload-completion?scope=company&company_id=4
GET /api/ia/documents/upload-completion?scope=all&company_id=4
```

## Archivos de implementación

| Ruta en el repo | Rol |
|-----------------|-----|
| `app/Http/Controllers/Ia/DocumentCompletionController.php` | Entrada HTTP |
| `app/Services/Ia/DocumentUploadCompletionService.php` | Lógica de negocio |
| `app/Util/DocumentScopeConstants.php` | Códigos de documento por ámbito |
| `app/Util/DocumentConstants.php` | Mensajes de error y textos |
| `app/Ia/Mongo/*` | Cliente Mongo y creación de colecciones |
| `routes/api.php` | Grupo de rutas con prefijo `ia` |

---

*Nota:* la ruta antigua bajo `/api/document/upload-completion` se retiró; use únicamente el prefijo `/api/ia/...` descrito arriba.
