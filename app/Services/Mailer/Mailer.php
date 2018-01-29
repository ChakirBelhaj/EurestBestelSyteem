<?php

namespace App\Services\Mailer;

use App\Services\Service;

/*
	Wraps the usermailer in a service
 */
class Mailer extends Service {

	public function boot() {
		$this->app->bind('mailer', new \SendGrid(env('SENDGRID_API_KEY')));
	}

}