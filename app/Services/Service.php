<?php

namespace App\Services;

use App\Application;

class Service
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}