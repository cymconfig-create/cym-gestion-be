# CyM Gestión — Backend (API REST)

Backend del sistema de gestión **CyM Gestión SGSST**, construido con **Laravel 10** y autenticación mediante **JWT**.

---

## Requisitos

| Herramienta | Versión mínima |
|-------------|---------------|
| PHP         | 8.1+          |
| Composer    | 2.x           |
| MySQL       | 8.0+          |

---

## Despliegue local

### 1. Instalar dependencias

```bash
composer install --ignore-platform-reqs
```

> Si el sistema tiene PHP 8.5+, el flag `--ignore-platform-reqs` es necesario porque algunas dependencias declaran soporte hasta PHP 8.4.

### 2. Configurar el entorno

```bash
cp .env.example .env
```

Editar `.env` con las credenciales de la base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cymgestionsgsst
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 3. Crear la base de datos en MySQL

```sql
CREATE DATABASE cymgestionsgsst CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Ejecutar migraciones y seeders

```bash
php artisan migrate --force
php artisan db:seed --force
```

Los seeders crean:
- Los 4 perfiles de usuario (Super Admin, Administrador, Responsable SGSST, Gerencia)
- 2 usuarios base (`willinton`, `elazo`)
- Menús, submenús y permisos por perfil
- Documentos y selectores del sistema

### 5. Generar el JWT secret

```bash
php artisan jwt:secret
```

### 6. Crear el enlace de almacenamiento

```bash
php artisan storage:link
```

### 7. Levantar el servidor

```bash
php artisan serve --port=8000
```

La API queda disponible en: `http://localhost:8000`

---

## Usuarios disponibles (seeders)

| Usuario    | Perfil     | Contraseña      |
|------------|------------|-----------------|
| willinton  | Super Admin | (hash — desconocida) |
| elazo      | Super Admin | (hash — desconocida) |

Para crear un usuario con contraseña conocida:

```bash
php artisan tinker --execute="
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
DB::table('users')->insert([
    'name' => 'admin',
    'password' => Hash::make('admin123'),
    'profile_id' => 1,
    'status' => 1,
    'created_by' => 'system',
    'updated_by' => 'system',
    'created_at' => now(),
    'updated_at' => now(),
]);
"
```

---

## Arquitectura

### Stack

- **Framework:** Laravel 10
- **Autenticación:** JWT (`tymon/jwt-auth v2`)
- **Base de datos:** MySQL 8 con Eloquent ORM
- **Almacenamiento de archivos:** filesystem local (`storage/app/public`)

### Estructura de directorios relevante

```
cym-back/
├── app/
│   ├── Http/
│   │   └── Controllers/       # Controladores de la API
│   └── Models/                # Modelos Eloquent
├── database/
│   ├── migrations/            # 27 migraciones (tablas + llaves foráneas)
│   └── seeders/               # Datos iniciales del sistema
├── routes/
│   └── api.php                # Definición de todos los endpoints
└── storage/
    └── app/public/            # Archivos subidos (attachments)
```

### Modelos principales

| Modelo        | Descripción                              |
|---------------|------------------------------------------|
| `User`        | Usuarios del sistema                     |
| `Profile`     | Perfiles/roles (SUPER, ADMIN, SGSST, GEREN) |
| `Company`     | Empresas gestionadas                     |
| `Employee`    | Empleados de cada empresa                |
| `Document`    | Documentos requeridos por empleado       |
| `Attachment`  | Archivos adjuntos a documentos           |
| `Conversation`/ `Message` | Sistema de mensajería interna |
| `Event`       | Actividades del mes / calendario         |
| `Menu` / `SubMenu` / `MenuProfile` | Control de menú por perfil |
| `Selector`    | Catálogos de valores (listas desplegables) |
| `Action` / `ActionProfile` | Acciones permitidas por perfil |

### Perfiles de usuario

| Código | Nombre               | Descripción                                      |
|--------|----------------------|--------------------------------------------------|
| SUPER  | Super Admin          | Acceso total al sistema                          |
| ADMIN  | Administrador        | Administración general                           |
| SGSST  | Responsable del SGSST| Gestión del sistema de seguridad y salud         |
| GEREN  | Gerencia             | Solo visualización                               |

### Endpoints de la API

Todos los endpoints excepto `/api/auth/authenticate` requieren el header:

```
Authorization: Bearer {token}
```

| Prefijo             | Métodos              | Descripción                        |
|---------------------|----------------------|------------------------------------|
| `POST /api/auth/authenticate` | —          | Login (devuelve JWT)               |
| `GET /api/auth/me`  | —                    | Usuario autenticado                |
| `GET /api/auth/refresh` | —                | Refrescar token                    |
| `GET /api/auth/logout` | —                 | Cerrar sesión                      |
| `/api/user`         | GET, POST, PUT, DELETE | CRUD de usuarios                 |
| `/api/profile`      | GET                  | Consulta de perfiles               |
| `/api/company`      | GET, POST, PUT, DELETE | CRUD de empresas                 |
| `/api/employee`     | GET, POST, PUT, DELETE | CRUD de empleados                |
| `/api/document`     | GET                  | Consulta de documentos             |
| `/api/attachment`   | GET, POST, PUT, DELETE | CRUD de adjuntos + descarga      |
| `/api/conversation` | GET, POST, PUT, DELETE | Sistema de mensajería            |
| `/api/event`        | GET, POST, PUT, DELETE | Actividades del mes              |
| `/api/selector`     | GET                  | Catálogos del sistema              |
