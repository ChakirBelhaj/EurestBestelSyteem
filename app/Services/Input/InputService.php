<?php

namespace App\Services\Input;

use App\Services\Service;

class InputService extends Service
{
    /**
     * Adds the InputService to the app()
     */
    public function boot()
    {
        $this->app->bind('input', new Input());
    }
}