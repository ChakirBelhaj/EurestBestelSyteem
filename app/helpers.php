<?php

if (!function_exists('app')) {
    function app($service = NULL) {
        global $app;

        if (!$service) {
            return $app;
        }
        return $app->resolve($service);
    }
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('env')) {
    function env($key, $default = null) {
        return getenv($key) ?? $default;
    }
}

if (!function_exists('dd')) {
    function dd(...$arguments) {
        var_dump(...$arguments);
        die();
    }
}

if (!function_exists('pp')) {
    function pp(...$arguments) {
        echo '<pre>';
        print_r(...$arguments);
        echo '</pre>';
        die();
    }
}

if (!function_exists('request')) {
    function request($parameters = null) {
        if (isset($parameters) && is_array($parameters)) {
            $returnParameters = [];

            foreach ($parameters as $parameter) {
                $returnParameters[$parameter] = app()->request->get($parameter);
            }

            return $returnParameters;
        }

        if (isset($parameters)) {
            return app()->request->get($parameters);
        }

        return app()->request;
    }
}

if (!function_exists('session')) {
    function session($key = null) {
        $session = app()->session;

        if (isset($key)) {
            return $session->get($key);
        }

        return $session;
    }
}

if (!function_exists('baseUrl')) {
    function baseUrl() {
        return env('BASE_URL') != NULL ? env('BASE_URL') : 'http://localhost';
    }
}

if (!function_exists('old')) {
    function old($key, $default = '') {
        return app('input')->old($key, $default);
    }
}

if (!function_exists('message')) {
    function message($message) {
        app('messenger')->createMessage($message);
    }
}

if (!function_exists('error')) {
    function error($error) {
        app('messenger')->createError($error);
    }
}