<?php

namespace App;

abstract class Container
{
    /**
     * Services to be loaded.
     */
    protected $services = [];

    /**
     * All the loaded services.
     */
    protected $loadedServices = [];

    /**
     *  Bind a service to the application.
     * @param string $name
     * @param Object $service
     * @return void
     */
    public function bind($name, $service)
    {
        $this->loadedServices[$name] = $service;
    }

    /**
     *  Resolve a service from the application.
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function resolve($name)
    {
        if (!isset($this->loadedServices[$name])) {
            throw new \Exception("The {$name} service failed to load.");
        }

        return $this->loadedServices[$name];
    }

    /**
     * Load all services.
     * @return void
     */
    protected function loadServices()
    {
        foreach ($this->services as $service) {
            $this->loadService(new $service($this));
        }
    }

    /**
     * Load a service into the container.
     * @param string $service
     * @return void
     */
    protected function loadService($service)
    {
        if (method_exists($service, 'register')) {
            $service->register();
        }

        if (method_exists($service, 'boot')) {
            $service->boot();
        }
    }
}