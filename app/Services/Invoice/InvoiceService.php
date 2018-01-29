<?php

namespace App\Services\Invoice;

use App\Services\Service;

/*
	Wrapts the Invoice class in a service
 */
class InvoiceService extends Service {
	public function boot() {
		$this->app->bind('invoice', new Invoice());
	}
}