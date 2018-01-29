<?php

namespace App\Services;

use Symfony\Component\Dotenv\Dotenv;

class Environment extends Service
{
    public function boot() {
        $environment = new Dotenv();
        $environment->load($this->app->path.'/.env');

        $this->app->bind('environment', $environment);
    }
}