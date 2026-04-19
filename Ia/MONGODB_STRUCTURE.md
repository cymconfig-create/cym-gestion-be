# Estructura MongoDB equivalente al modelo SQL (CyM Gestión)

La aplicación **sigue usando** la conexión definida por `DB_CONNECTION` (por defecto **MySQL** en `.env.example`; **PostgreSQL** está disponible como driver `pgsql` en `config/database.php` pero no se ha modificado).  
MongoDB es una **base paralela** para nuevos flujos (IA, analítica, réplicas desnormalizadas), configurada con `MONGODB_URI` y `MONGODB_DATABASE`.

## Cómo se conecta la app a la base SQL hoy

1. **Variables** en `.env`: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (y opcionalmente `DATABASE_URL`).
2. **Configuración** en `config/database.php`: array `connections` con drivers `mysql`, `pgsql`, etc.
3. **Uso en código**: Eloquent y Query Builder usan la conexión por defecto `config('database.default')` salvo que se indique otra.

## Cómo se conecta a MongoDB

1. **Variables** en `.env`: `MONGODB_URI` (cadena SRV de Atlas), `MONGODB_DATABASE` (nombre lógico de la base, p. ej. `cymDatabase`).
2. **Configuración** en `config/mongodb.php` (archivo nuevo, no altera `database.php`).
3. **Código**: `App\Ia\Mongo\MongoClientFactory::database()` devuelve `MongoDB\Database` usando el paquete oficial `mongodb/mongodb` (requiere extensión PHP `mongodb`).

## Tablas SQL → colecciones MongoDB

Cada tabla principal tiene una **colección** con el mismo nombre en plural (convención 1:1 con las migraciones):

| Tabla / colección | Notas de modelado no relacional |
|-------------------|-----------------------------------|
| `users` | Referencias `profile_id`, `employee_id` como enteros (o `ObjectId` si migran IDs). Sin FK a nivel servidor en Mongo. |
| `profiles` | Catálogo de perfiles. |
| `companies` | `legal_representative_id` / `system_manager_id` → referencias a `employees` o documentos embebidos mínimos. |
| `employees` | `company_id`, `user_id` como referencias. |
| `documents` | Catálogo de tipos de documento. |
| `attachments` | `document_id`, `company_id`, `employee_id` referencias; `route_file` string. |
| `conversations`, `messages` | Opción futura: mensajes embebidos en conversación; aquí se mantiene 1:1 con tablas para equivalencia. |
| `conversation_user` | Tabla pivote → colección con pares `{ conversation_id, user_id }` o array en `conversations`. |
| `menus`, `sub_menus`, `menu_profiles` | Menú por perfil; en Mongo se puede anidar `sub_menus` dentro de `menus` en una fase posterior. |
| `actions`, `action_profiles` | Permisos por perfil. |
| `selectors` | Catálogos. |
| `events` | Modelo Eloquent apunta a `events` (si la migración existe en otro entorno). |

## Validación de esquema en MongoDB

`App\Ia\Mongo\MongoSchemaInitializer` crea colecciones con **JSON Schema** (`$jsonSchema`) en modo `validationAction: warn` para colecciones clave (`users`, `companies`, `employees`, `attachments`, `documents`, `profiles`); el resto usa un esquema permisivo (`object` con `additionalProperties: true`).

## Comandos útiles

```bash
php artisan ia:mongo-init-schemas --dry-run
php artisan ia:mongo-init-schemas
php artisan ia:mongo-init-schemas --no-validators
```

## Requisitos

- Extensión PHP **mongodb** habilitada.
- Paquete **mongodb/mongodb** instalado (`composer install`).
