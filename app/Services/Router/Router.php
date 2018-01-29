<?php

namespace App\Services\Router;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Router
{
    private $baseRoute = '';
    private $routeInfo = array();
    private $routes = array(
        'GET' => array(),
        'POST' => array(),
        'PUT' => array(),
        'PATCH' => array(),
        'DELETE' => array()
    );

    /**
     * Router constructor.
     */
    public function __construct()
    {

    }

    /**
     * Add GET request to the $routes array
     *
     * @param $pattern
     * @param $func
     */
    public function get($pattern, $func, $guards = [])
    {
        $this->match('GET', $pattern, $func, $guards);
    }

    /**
     * Add POST request to the $routes array
     *
     * @param $pattern
     * @param $func
     */
    public function post($pattern, $func, $guards = [])
    {
        $this->match('POST', $pattern, $func, $guards);
    }

    /**
     * Adds PUT request to the $routes array
     *
     * @param $pattern
     * @param $func
     */
    public function put($pattern, $func, $guards = [])
    {
        $this->match('PUT', $pattern, $func, $guards);
    }

    /**
     * Add PATCH request to the $routes array
     *
     * @param $pattern
     * @param $func
     */
    public function patch($pattern, $func, $guards = [])
    {
        $this->match('PATCH', $pattern, $func, $guards);
    }

    /**
     * Add DELETE request to the $routes array
     *
     * @param $pattern
     * @param $func
     */
    public function delete($pattern, $func, $guards = [])
    {
        $this->match('DELETE', $pattern, $func, $guards);
    }

    /**
     * Add route with multiple request methods to the $routes array
     *
     * @param $methods
     * @param $pattern
     * @param $func
     */
    public function match($methods, $pattern, $func, $guards = [])
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach(explode('|', $methods) as $method)
        {
            $this->routes[$method][] = array(
                'pattern' => $pattern,
                'func' => $func,
                'guards' => $guards
            );
        }
    }

    /**
     * Searches for the current URL in the given loaded routes array and executes the given function for the route
     *
     * @throws Exception, when no route exists, or controller cannot be found
     */
    public function handle()
    {
        // Load all routes
        require(app()->path . '/app/routes.php');

        // Variable setup
        $request_method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getBaseURL();
        $routes = array();
        $routeFound = false;
        $errorController = new \App\Controllers\ErrorController();

        // Check if string contains GET variables
        if(substr_count($uri, '?') > 0)
        {
            // Remove GET variables from string
            $uri = substr($uri, 0, strpos($uri, "?"));
        }
        // Loop through all routes
        foreach($this->routes[$request_method] as $key => $route)
        {
            // Transform route patterns to valid regex
            $pattern = $this->getRegexForRoute($route['pattern']);

            // Check if route exists
            if(preg_match_all('#^' . $pattern . '$#', $uri, $matches, PREG_OFFSET_CAPTURE))
            {
                // Prevent 404
                $routeFound = true;

                // Handle the guards
                app()->resolve('gatekeeper')->addGuards($route['guards']);
                app()->resolve('gatekeeper')->handle();

                // Check if there are variables
                if(!empty($matches))
                {
                    // Set route info
                    $this->routeInfo = $this->getDynamicRouteVariables($route['pattern'], $uri);

                    // Set route variables
                    app()->setRouteInfo($this->routeInfo);
                }

                // Save routes
                $routes[$pattern] = $route['func'];

                // Check if the router $route['func'] is callable or is a
                if(is_callable($route['func']))
                {
                    // Call function
                    call_user_func($route['func'])->send();
                }
                // Check if the string is directly calling a class@function
                elseif(stripos($route['func'], '@') !== false)
                {
                    // Split the class and function
                    list($controller, $method) = explode('@', $route['func']);

                    // Add namespace to the controller
                    $controller = '\\App\\Controllers\\'.$controller;

                    // Check if the class exists
                    if(class_exists($controller))
                    {
                        // Call class and method
                        $class = new $controller();
                        call_user_func(array($class, $method))->send();
                    }
                    else
                    {
                        // Class not found!
                        $errorController->internalServerError()->send();
                    }
                }
                else
                {
                    // No callable function found!
                    $errorController->internalServerError()->send();
                }
            }
        } #end foreach

        // 404  page not found!
        if($routeFound == false)
        {
            // Page not found, throw not found exception
            $errorController->notFound()->send();
        }
    }

    /**
     * Transforms a route '{parameter}' to a regex
     *
     * @param string $pattern
     *
     * @return bool|mixed|string, returns regex when match pattern is correct, otherwise false
     */
    private function getRegexForRoute($pattern = "")
    {
        // Skip empty pattern
        if($pattern == "") {
            return false;
        }

        // Get all variables
        preg_match_all('/{(.*?)}/s', $pattern, $matches);

        //
        if(!empty($matches[0]) && !empty($matches[1]))
        {
            return str_replace($matches[0], '[a-zA-Z0-9_\-@.]+', $pattern);
        }
        else
        {
            return $pattern;
        }
    }

    /**
     * Gets the '{parameter}' items from the pattern and returns an array with there corresponding values
     *
     * @param string $pattern
     * @param string $uri
     *
     * @return array|bool, returns false if $pattern or $uri is empty, else returns an array with the route variables
     */
    private function getDynamicRouteVariables($pattern = "", $uri = "")
    {
        //
        $values = array();

        // Skip empty pattern
        if($pattern == "" || $uri == "") {
            return false;
        }

        // Split the $pattern & $uri op de /
        $exploded_pattern = explode('/', $pattern);
        $exploded_uri = explode('/', $uri);

        // Loop through the $pattern and $uri
        for($i = 0; $i < count($exploded_pattern); $i++)
        {
            if(substr($exploded_pattern[$i], 0, 1) == '{' && substr($exploded_pattern[$i], -1) == '}')
            {
                $values[substr($exploded_pattern[$i], 1, -1)] = $exploded_uri[$i];
            }
        }

        return $values;
    }

    /**
     * Get the base URL for the server to start the routing from
     *
     * @return bool|string, returns base url
     */
    private function getBaseURL()
    {
        $uri = $_SERVER['REQUEST_URI'];

        // Remove "/public" from string
        if(substr($uri, 0 , 7) == '/public')
        {
            $uri = substr($uri, 7 , (strlen($uri) - 7));
            if(strlen($uri) == 0)
            {
                $uri = '/';
            }
        }
        return $uri;
    }
}