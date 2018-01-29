<?php

namespace App\Services\GateKeeper;

use App\Services\Service;

class GateKeeperService extends Service
{
    // Router instance
    protected $gatekeeper;

    /**
     * Loads the router
     */
    public function boot()
    {
        // Initialize the router
        $this->gatekeeper = new GateKeeper();

        // Liftoff
        $this->app->bind('gatekeeper', $this->gatekeeper);
    }
}