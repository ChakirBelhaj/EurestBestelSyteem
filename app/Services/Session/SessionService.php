<?php

namespace App\Services\Session;

use App\Services\Service;

class SessionService extends Service
{
    /**
     * Loads the router
     */
    public function boot()
    {
        Session::init();
    }
}