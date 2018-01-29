<?php

// Load dependencies
require(__DIR__ . '/vendor/autoload.php');

// Create a new app instance
$env = new \Symfony\Component\Dotenv\Dotenv();

$env->load(__DIR__.'/.env');

return [
    'paths' => [
        'migrations' => __DIR__.'/database/migrations',
        'seeds' => __DIR__.'/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => 'development',
        'development' => [
            'adapter' => getenv('DB_DRIVER'),
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_NAME'),
            'user' => getenv('DB_USERNAME'),
            'pass' => getenv('DB_PASSWORD'),
            'port' => '3306',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci'
        ]
    ],
    'version_order' => 'creation'
];