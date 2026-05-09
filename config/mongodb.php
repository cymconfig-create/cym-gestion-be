<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MongoDB (Atlas) — conexión paralela
    |--------------------------------------------------------------------------
    |
    | No modifica la conexión SQL por defecto (DB_CONNECTION). Requiere la
    | extensión PHP "mongodb". URI y base en variables de entorno.
    |
    */

    'uri' => env('MONGODB_URI', ''),

    'database' => env('MONGODB_DATABASE', 'cymDatabase'),

];
