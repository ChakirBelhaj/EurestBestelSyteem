<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Order;

class PaymentManager {

	// The mollie instance
	private $mollie;

	public function __construct() {

		// Setup the Mollie client
		$mollie = new \Mollie_API_Client();
		$mollie->setApiKey(env('MOLLIE_API_KEY'));
		$this->mollie = $mollie;
	}

	/**
	 * Creates a payment for the given order
	 * @param  Order $order            	The order
	 * @param  string $paymentMethod 	The id of the Mollie payment method
	 * @return mixed                	Either a new Mollie payment object or false
	 */	
	public function createPaymentForOrder(Order $order, $paymentMethod) {

		// Wrap this in a try-catch block
		try {

			// Create the array of payment data that we are going to send to Mollie
			$paymentData = [
			    "amount"      => (float) $order->effectiveTotalPrice() / 100,
			    "description" => "Eurest bestelling #" . $order->id,
			    "redirectUrl" => baseURL() . '/payment/return/' . $order->id,
			    "webhookUrl"  => baseURL() . '/payment/process/' . $order->id,
			    "metadata" 	  => [
			    	"order_id" => $order->id
			    ]
			] + $this->filterPaymentMethod($paymentMethod);

			// Get the payment created by Mollie, can throw an exception
			$payment = $this->mollie->payments->create($paymentData);

			// Update the order in the database with the newly created mollie_id
			app('database')->table('orders')->where('id', $order->id)->update(['mollie_id' => $payment->id]);

			// Return the payment
			return $payment;
		}

		// Catch any exceptions that Mollie might throw
		catch (\Mollie_API_Exception $e) {

			// Return false when we get an exception
			return false;
		}
	}

	/**
	 * Gets and returns the payment methods for a given order
	 * @param  Order $order The order
	 * @return array    	Array of payment method strings
	 */
	public function getPaymentMethodsForOrder($order) {

		// Fetch the total order value
		$totalOrderValue = $order->totalPrice();

		// Create a new array in which we are going to place the payment methods and an ideal issuers array
		$filteredMethods = ['methods' => [], 'ideal_issuers' => []];
		try {

			// Get the payment methods
			$methods = $this->mollie->methods->all();

			// Set the iDeal issuers (banks)
			$filteredMethods['ideal_issuers'] = $this->mollie->issuers->all()->data;

			// Loop through the methods
			foreach ($methods as $method) {

				// Check if this payment method can be use (must be in range of the minumum and maximum order value)
				if ($totalOrderValue >= $method->amount->minimum && $totalOrderValue <= $method->amount->maximum) {

					// Add it to the payment methods
					$filteredMethods['methods'][] = $method;
				}
			}
			
			// Return the payment methods
			return $filteredMethods;
		}

		// Catch any exception that Mollie might throw
		catch (\Mollie_API_Exception $e) {

			// Return the methods (empty array)
			return $filteredMethods;
		}
	}

	/**
	 * Updates the status for the given order
	 * @param  Order $order   The order 
	 * @param  string $status The string status for the order
	 * @return bool           Wether the update call was successful
	 */
	public function updatePaymentStatusForOrder(Order $order, $status) {

		// Update the order status
		return (bool) app('database')->table('orders')->where(['id' => $order->id])->update(['payment_status' => $status]);
	}

	/**
	 * Extracts the payment method id into a payment method and possible iDeal issuer code
	 * @param  string $paymentMethod   The payment method ID from Mollie
	 * @return array  $filteredMethod  The filtered method
	 */
	private function filterPaymentMethod($paymentMethod) {
		$filteredMethod = ['method' => $paymentMethod, 'issuer' => NULL];

		// Check if this is an iDeal payment method
		if (strpos($paymentMethod, strtolower(\Mollie_API_Object_Method::IDEAL)) !== false) {

			// Add the filtered method
			$filteredMethod['method'] = \Mollie_API_Object_Method::IDEAL;
			$filteredMethod['issuer'] = $paymentMethod;
		}

		// Return the filtered method
		return $filteredMethod;
	}

	/**
	 * Verifies the order payment with Molie
	 * @param  int $orderId      The ID of the order
	 * @param  string $paymentId The Mollie payment ID
	 * @return bool              Wether the payment was paid
	 */
	public function verifyOrderPayment($orderId, $paymentId) {

		// Get the order from the database
		$order = app('database')->table('orders')->select()->where(['id' => $orderId, 'mollie_id' => $paymentId])->first();

		// If the order wasn't found, return false
		if (!$order) {
			return false;
		}

		// Wrap this in a try-catch block, Mollie can throw an exception
		try {

			// Fetch the payment from mollie to get the details and status
			$payment = $this->mollie->payments->get($order->mollie_id);

			// Extra check to verify the order_id matches with the current payment
			if ($payment->metadata->order_id != $orderId) {
				return false;
			}

			// Check if the order has been paid
			return $payment->isPaid();
		}

		// Catch any exceptions Mollie might throw
		catch (\Mollie_API_Exception $e) {

			// Return false
			return false;
		}
	}

	/**
	 * Returns the payment form Mollie for the given order, or false when not found
	 * @param  Order $order              The order
	 * @return Mollie_Payment | false    The payment or false when not found
	 */
	public function getPaymentForOrder(Order $order) {

		// Try catch, because mollie can throw exceptions
		try {

			// Return the fetched payment
			return $this->mollie->payments->get($order->payment()->mollie_id);
		}
		catch (\Mollie_API_Exception $e) {
			return false;
		} 
	}

	/**
	 * Refund a given order
	 * @param  Order $order              The order
	 * @return boolean    				Wether the refund was succesful
	 */
	public function refundOrder(Order $order) {
		$payment = $this->getPaymentForOrder($order);
		if ($payment) {

			try {
				$refund = $this->mollie->payments->refund($payment);

				if ($refund->isPending()) {
					return (bool) app('database')->table('orders')->where('id', $order->id)->update(['status' => Order::CANCELLED, 'payment_status' => Payment::REFUNDED]);
				}
			}
			catch (\Mollie_API_Exception $e) {}
		}
	}
}