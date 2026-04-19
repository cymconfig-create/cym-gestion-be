# Pruebas — MongoDB (módulo IA)

## Objetivo

Verificar que el comando `ia:mongo-init-schemas` puede **crear las colecciones** (y validadores opcionales) en Atlas **sin tocar** la base SQL del proyecto.

## Requisitos

1. `composer install` en la raíz del backend.
2. Extensión PHP `mongodb` instalada y habilitada (`php -m` debe listar `mongodb`).
3. En `.env`, variables `MONGODB_URI` y `MONGODB_DATABASE` (ver `.env.example`).

## Comandos ejecutados (plantilla)

```bash
php artisan ia:mongo-init-schemas --dry-run
php artisan ia:mongo-init-schemas
php artisan test --testsuite=Ia
```

## Resultados

Los resultados concretos de tu máquina (salida de consola o de PHPUnit) pueden copiarse en `resultado_prueba_mongo.txt` para dejar constancia en el repositorio **sin incluir contraseñas ni URIs completas**.
