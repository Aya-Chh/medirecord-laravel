<?php
return [
    'default' => env('APP_ENV') === 'testing' ? env('DB_CONNECTION', 'sqlite') : 'sqlite',
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('APP_ENV') === 'testing'
                ? env('DB_DATABASE', ':memory:')
                : database_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'medirecord'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB',
        ],
    ],
    'migrations' => 'migrations',
];
