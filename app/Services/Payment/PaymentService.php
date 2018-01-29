<?php

namespace App\Services\Payment;

use App\Services\Service;

class PaymentService extends Service {

	public function boot() {

		$paymentManager = new PaymentManager();

		$this->app->bind('payment_manager', $paymentManager);
	}
}