<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Redirect
{
    public static function to($url)
    {
        $response = new RedirectResponse($url);
        $response->send();
    }

    public static function back()
    {
        static::to($_SESSION['redirect_url']);
    }
}