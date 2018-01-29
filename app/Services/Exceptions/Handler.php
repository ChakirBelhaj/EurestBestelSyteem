<?php

namespace App\Services\Exceptions;

use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

class Handler
{
    public function boot()
    {
        Debug::enable();
        ErrorHandler::register();
        ExceptionHandler::register();
    }
}