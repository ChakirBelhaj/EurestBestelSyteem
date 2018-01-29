<?php

namespace App\Services\Router;

use App\Services\Service;

class RouterService extends Service
{
    // Router instance
    protected $router;

    /**
     * Loads the router
     */
    public function boot()
    {
        // Initialize the router
        $this->router = new Router();

        // Liftoff
        $this->app->bind('router', $this->router);
    }
}