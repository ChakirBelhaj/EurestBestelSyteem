<?php

namespace App\Services\Config;

class ConfigRepository
{
    protected $config = [];

    /**
     * Get a value from the config
     * @param String $key
     * @param Mixed $default
     */
    public function get($key, $default)
    {
        //
    }

    /**
     * Load all config files in a directory.
     * @param String $path
     */
    public function loadDirectory($path)
    {
        //
    }

    /**
     * Load a single config file.
     * @param String $path
     */
    public function loadFromPath($path)
    {
        $file = pathinfo($path);

        dd($file);
//        $this->config[$file['']]
    }
}