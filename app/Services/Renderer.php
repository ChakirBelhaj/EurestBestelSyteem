<?php

namespace App\Services;

/*
    Responsible for rendering HTML templates using Twig
 */
class Renderer extends Service {

	public function boot() {
		$viewsPath = __DIR__ . '/../../views';
		$loader = new \Twig_Loader_Filesystem($viewsPath);
		$twig = new \Twig_Environment($loader, array(
		    // 'cache' => $viewsPath . '/cache',
		));

        // Add's a money filter, which divides the value from the database (cents) to euro's and rounds the value to make it readable
		$moneyFilter = new \Twig_Filter('money', function ($string) {
			$amount = intval($string);
            if ($amount == 0) {
                return '';
            }
		    return number_format(($amount / 100),2,",",".");
		});
		$twig->addFilter($moneyFilter);
		$twig->addExtension(new TwigExtension($_SERVER['REQUEST_URI']));

		$this->app->bind('renderer', $twig);
	}
}

/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Twig-View
 * @copyright Copyright (c) 2011-2015 Josh Lockhart
 * @license   https://github.com/slimphp/Twig-View/blob/master/LICENSE.md (MIT License)
 */

class TwigExtension extends \Twig_Extension
{
    /**
     * @var string|\Slim\Http\Uri
     */
    private $uri;

    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    public function getName()
    {
        return 'app';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('is_current_path', array($this, 'isCurrentPath')),
        ];
    }

    public function isCurrentPath($name, $stripLastPart = false)
    {
        $uri = $this->uri;
        if ($uri == $name) {
            return true;
        }
        else if ($stripLastPart) {
            $res = explode('/', $uri);
            unset($res[count($res) - 1]);

            $uri = '';
            foreach ($res as $part) {
                $uri .= $part . '/';
            }
            $uri = substr($uri, 0, strlen($uri) - 1);
        }
    }
}