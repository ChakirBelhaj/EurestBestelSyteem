<?php

namespace App\Services\Resizer;

use App\Services\Service;

class ResizerService extends Service
{
    // Image resizer instance
    protected $resizer;

    /**
     * Loads the image resizer
     */
    public function boot()
    {
        // Initialize the image resizer
        $this->resizer = new Resizer();

        // Liftoff
        $this->app->bind('resizer', $this->resizer);
    }
}