<?php

namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database extends Service
{
    public function boot() {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => env('DB_DRIVER'),
            'host'      => env('DB_HOST'),
            'database'  => env('DB_NAME'),
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $this->app->bind('database', $capsule);

        $capsule->setAsGlobal();
    }
}