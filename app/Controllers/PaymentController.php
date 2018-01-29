<?php

namespace App\Controllers;

use App\Services\Login\LoginService;
use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use App\Services\Mailer\UserMailer;

class PaymentController extends Controller {

	/*
		The pay route, in the form of /payment/pay/{id}.
		Allows the user to view the total price and choose a payment method in order to get redirected to their payment provider.
	 */ 
	public function pay() {

		// Get the order id from the URL
		$id = app()->getRouteInfo()['order_id'];

		// Get the order, or fail with a 404
		$order = Order::findOrFail($id, function ($query) {
			return $query->where('user_id', LoginService::getCurrentUser()->id)->where('payment_status', Payment::OPEN)->orWhere('payment_status', Payment::PENDING);
		});

		// Get the total price and payment methods for the order
		$paymentMethods = app('payment_manager')->getPaymentMethodsForOrder($order);

		// Render the pay page with the details and payment methods
		return $this->view('frontend/payment/pay.twig', ['payment_methods' => $paymentMethods, 'totalprice' => $order->effectiveTotalPrice()]);
	}

	/*
		The POST /payment/pay/{id} route that handles redirection to the payment page
	 */
	public function postPay() {

		// Get the order ID from the URL
		$orderId = app()->getRouteInfo()['order_id'];
		$paymentMethodId = app()->request->get('payment_method');

		// Try to find the order, else 404
		$order = Order::findOrFail($orderId, function($query) {
			return $query->where(['user_id' => LoginService::getCurrentUser()->id]);
		});

		// We can still pay for this order, e.g. not already paid for or pending payment
		if ($order->statusDetails()->canPay()) {

			// Check wether the user would like to pay online or in story
			if (!empty(app()->request->get('online'))) {

				// Create a new payment for the order and with the given payment method with our payment provider
				$payment = app('payment_manager')->createPaymentForOrder($order, $paymentMethodId);

				// If the payment was created successfully
				if ($payment) {

					// Redirect the user to the payment page of choise
					return $this->redirect($payment->links->paymentUrl);
				}
			}
			else if (!empty(app()->request->get('in_store'))) {

				// Mark this payment as in_store and save the order
				app('database')->table('orders')->where('id', $order->id)->update(['payment_status' => Payment::IN_STORE]);

				// Return the user to the return page
				return $this->redirect('/payment/return/' . $order->id);
			}
		}
		else {

		}
	}

	/*
		The GET /payment/return/{order_id} route. The user will go to this URL after they've paid or canceled the payment.
	 */
	public function return() {

		// Get the order ID from the URL
		$orderId = app()->getRouteInfo()['order_id'];
		$order = Order::findOrFail($orderId);
		$user = User::findOrFail($order->user_id);

		// Check wether the user made an online payment or wants to pay in store
		if ($order->payment()->status == Payment::IN_STORE) {

			// Send the order confirmation email
			$invoice = app('invoice')->new($user, $order);
			UserMailer::sendOrderConfirmationEmail($user, $order, $invoice->output(false));

			// Render the thankyou page
			return $this->view('frontend/payment/return.twig', ['payment_method' => Payment::IN_STORE, 'order_id' => $order->id]);
		}
		else {
			// Get the payment details to show to the user
			$payment = app('payment_manager')->getPaymentForOrder($order);

			// Only update the status to pending if Mollie didn't already tell us it has been paid
			if ($order->payment()->status == Payment::OPEN && $payment->isOpen()) {

				// Try to update the payment to status payment pending
				app('payment_manager')->updatePaymentStatusForOrder($order, Payment::PENDING);

			} else if (!$payment) {
				return $this->redirect('/account/orders');
			}
			
			// Render the thankyou page
			return $this->view('frontend/payment/return.twig', ['payment_method' => 'online', 'payment' => $payment, 'order_id' => $order->id]);
		}
	}

	/*
		This POST /payment/process/{order_id} route gets called by our payment provider to trigger us to update the payment status
	 */
	public function process() {

		// Make sure we got the mollie_id from the payment provider in the POST body
		if (app()->request->get('id') == NULL) {
			return $this->json(['success' => false, 'error' => 'Missing ID']);
		}

		// Get the order ID from the url, and payment ID from the POST body
		$orderId = app()->getRouteInfo()['order_id'];
		$mollieId = app()->request->get('id');

		// Get the order from the database
		$order = Order::findOrFail($orderId, function($query) use ($mollieId) {
			return $query->where('mollie_id', $mollieId);
		});
		$user = User::findOrFail($order->user_id);

		// Get the Mollie Payment
		$payment = app('payment_manager')->getPaymentForOrder($order);

		// Check if we have the order
		if (!$payment) {
			return $this->json(['success' => false, 'error' => 'Payment not found']);
		}

		// Order hasn't been paid yet, so we should check if it's paid now
		if (($order->payment()->status == Payment::OPEN || $order->payment()->status == Payment::PENDING) && $payment->isPaid()) {

			// Update the order payment with the paid status
			app('payment_manager')->updatePaymentStatusForOrder($order, Payment::PAID);

			$order->payment_status = Payment::PAID;

			// Generate the invoice
			$invoice = app('invoice')->new($user, $order);

			// Send the order confirmation email
			UserMailer::sendOrderConfirmationEmail($user, $order, $invoice->output(false));

			// Return a success json
			return $this->json(['success' => true]);
		}
		else if (($order->payment()->status == Payment::OPEN || $order->payment()->status == Payment::PENDING) && $payment->isExpired()) {

			// Update the order payment with the open status, allow the user to pay again
			app('payment_manager')->updatePaymentStatusForOrder($order, Payment::OPEN);

			// Return a success json
			return $this->json(['success' => true]);
		}

		// Return a error json body
		return $this->json(['success' => false]);
	}
}