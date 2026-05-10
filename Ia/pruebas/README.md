# Pruebas — MongoDB (módulo IA)

## Objetivo

Verificar que el comando `ia:mongo-init-schemas` puede **crear las colecciones** (y validadores opcionales) en Atlas **sin tocar** la base SQL del proyecto.

## Requisitos

1. `composer install` en la raíz del backend (paquete `mongodb/mongodb` **^2.0** con extensión PHP **mongodb 2.x**).
2. Extensión PHP `mongodb` instalada y habilitada (`php -m` debe listar `mongodb`). En Windows, binarios en [releases mongo-php-driver](https://github.com/mongodb/mongo-php-driver/releases).
3. En `.env`, variables `MONGODB_URI` y `MONGODB_DATABASE` (ver `.env.example`).
4. Opcional: con Docker en ejecución, ver `docker_mongo_init.sh` en esta carpeta.

## Comandos ejecutados (plantilla)

```bash
php artisan ia:mongo-init-schemas --dry-run
php artisan ia:mongo-init-schemas
php artisan test --testsuite=Ia
```

## Resultados

Los resultados concretos de tu máquina (salida de consola o de PHPUnit) pueden copiarse en `resultado_prueba_mongo.txt` para dejar constancia en el repositorio **sin incluir contraseñas ni URIs completas**.
