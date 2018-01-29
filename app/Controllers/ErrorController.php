<?php

namespace App\Controllers;

class ErrorController extends Controller
{
    // Show 403 page
    public function forbidden()
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);
        return $this->view('frontend/errors/403.twig');
    }

    // Show 404 page
    public function notFound()
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
        return $this->view('frontend/errors/404.twig');
    }

    // Show 500 page
    public function internalServerError()
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        return $this->view('frontend/errors/500.twig');
    }
}