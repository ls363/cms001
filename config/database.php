<?php
return [
    'driver'      => 'mysql',
    'host'        => env('DB_HOST', '127.0.0.1'),
    'port'        => env('DB_PORT', '3306'),
    'database'    => env('DB_DATABASE', 'cms001'),
    'username'    => env('DB_USERNAME', 'root'),
    'password'    => env('DB_PASSWORD', 'root'),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset'     => 'utf8mb4',
    'collation'   => 'utf8mb4_unicode_ci',
    'prefix'      => env('DB_PREFIX', 'gs_'),
    'strict'      => true,
    'engine'      => null,
    'debug' => env('DB_DEBUG', false)
];