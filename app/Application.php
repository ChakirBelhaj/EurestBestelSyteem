<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Application extends Container
{
    public $routeVariables;
    public $path;

    /**
     * All the application's services.
     */
    protected $services = [
        \App\Services\Exceptions\Handler::class,
        \App\Services\Environment::class,
        \App\Services\Database::class,
        \App\Services\Renderer::class,
        \App\Services\Mailer\Mailer::class,
        \App\Services\Session\SessionService::class,
        \App\Services\Messenger\MessengerService::class,
        \App\Services\GateKeeper\GateKeeperService::class,
        \App\Services\Router\RouterService::class,
        \App\Services\Login\LoginService::class,
        \App\Services\Payment\PaymentService::class,
        \App\Services\Invoice\InvoiceService::class,
        \App\Services\Input\InputService::class,
        \App\Services\Resizer\ResizerService::class,
    ];

    /**
     * Boot an instance of the application.
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Bootstrap the application.
     * @return void
     */
    public function boot()
    {
        $this->loadServices();
    }

    /**
     * Run the application.
     * @return void
     */
    public function run()
    {
        $this->boot();

        $this->request = Request::createFromGlobals();

        $this->resolve('router')->handle();

        $_SESSION['redirect_url'] = $this->request->getRequestUri();

        $_SESSION['old'] = $this->request->request;
    }

    /**
     * Stops the application process and sends an error response.
     * @param integer $code
     * @param string $message
     * @param array $headers
     * @return void
     */
    public function abort($code, $message = '', array $headers = [])
    {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        }

        throw new HttpException($code, $message, null, $headers);
    }

    /**
     * Sets the route variables as
     *
     * @param array $data
     * @return void
     */
    public function setRouteInfo($data = array())
    {
        $this->routeVariables = $data;
    }

    /**
     * Returns an array of all the route info set by the setRouteInfo
     *
     * @return array
     */
    public function getRouteInfo()
    {
        return $this->routeVariables;
    }
}