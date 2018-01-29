<?php

namespace App\Services\Messenger;

use App\Services\Service;

class MessengerService extends Service
{
    // Router instance
    protected $messenger;

    /**
     * Loads the router
     */
    public function boot()
    {
        // Initialize the router
        $this->messenger = new Messenger();

        // Liftoff
        $this->app->bind('messenger', $this->messenger);
    }
}