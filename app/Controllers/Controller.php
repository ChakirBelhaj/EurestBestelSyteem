<?php

namespace App\Controllers;

use App\Services\Redirect;
use App\Services\Login\LoginService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Redirect user to url
     *
     * @param  string   $url    URL you want the user to be redirected to
     */
    protected function redirect($url)
    {
        Redirect::to($url);
    }

    /**
     * Shows twig page to the user
     *
     * @param  string   $view       The view you want to load
     * @param  array    $values     Array with data that should be availible in the twig template
     */
    protected function view(string $view, array $values = [])
    {
        $messages = app()->resolve('messenger')->getMessagesToDisplay();

        $data = array(
            'loggedIn' => LoginService::isLoggedIn(),
            'messages' => $messages['messages'],
            'errors' => $messages['errors'],
            'input' => app('input'),
        );

        // Set default data
        if(LoginService::isLoggedIn()) {
            $data['currentUser'] = LoginService::getCurrentUser();
        }

        // Merge given values and default values
        $values = array_merge($data, $values);

        // Render page
        $contents = app('renderer')->render($view, $values);

        return new Response($contents, Response::HTTP_OK, ['content-type' => 'text/html']);
    }

    /**
     * Returns JSON to the browser
     *
     * @param  array    $content    Array with data you want to be converted to JSON
     */
    protected function json($content)
    {
        return new JsonResponse($content);
    }
}