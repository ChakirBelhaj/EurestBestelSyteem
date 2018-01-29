<?php

namespace App\Controllers;

use App\Services\Login\LoginService;
use App\Services\Mailer\UserMailer;
use App\Models\Order;
use App\Models\User;
use App\Models\Payment;

class AccountOrdersController extends Controller {

	/*
		Displays the user's orders
	 */
	public function index() {
		$user = LoginService::getCurrentUser();
		$orders = [];
		$orderData = app('database')->table('orders')->select()->where(['user_id' => $user->id])->where('status', '!=', Order::INCOMPLETE)->get();

		foreach ($orderData as $order) {
			$orders[] = new Order($order);
		}

		return $this->view('frontend/account/orders/index.twig', ['orders' => $orders]);
	}

	/*
		Display's a single order of a user.
	 */
	public function order() {
		$orderId = app()->getRouteInfo()['order_id'];
		$order = Order::findOrFail($orderId);

		return $this->view('frontend/account/orders/view.twig', ['order' => $order]);
	}

	/*
		Generates and display's the invoice for an order
	 */
	public function invoice() {
		$orderId = app()->getRouteInfo()['order_id'];
		$order = Order::findOrFail($orderId);
		
		$invoice = app('invoice')->new(LoginService::getCurrentUser(), $order);
		$invoice->output();
		exit;
	}

	/*
		Handles clicking a button on the order detail page, like canceling the order or reordering the products from the invoice
	 */
	public function action() {
		$orderId = app()->getRouteInfo()['order_id'];
		$order = Order::findOrFail($orderId, function($query) {
			return $query->where('user_id', LoginService::getCurrentUser()->id);
		});

		// User clicked the button to reorder a order
		if (app()->request->get('reorder') != NULL) {
			$order->reorder();
		}

		// User clicked the button to cancel the current order
		else if (app()->request->get('cancel') != NULL) {
			$success = false;
			$error = 'Bestelling succesvol geannuleerd';
			if ($order->statusDetails()->canCancel()) {
				if ($order->payment()->status == Payment::PAID) {
					if (app('payment_manager')->refundOrder($order)) {
						$success = true;
						$error .= ', het bedrag wordt binnen enkele werkdagen op uw rekening teruggestort';
					}
				} else {
					$success = (bool) app('database')->table('orders')->where('id', $order->id)->update(['status' => Order::CANCELLED]);
				}
			}

			if ($success) {
				UserMailer::sendOrderCanceledEmail(User::find($order->user_id), $order);
				message($error);
			}
			else {
				error('Er ging iets mis bij het annuleren van uw bestelling. Neem bij aanblijven contact met ons op');
			}

			// Back to the order
			return $this->redirect('/account/orders/' . $orderId);
		}
	}

	/*
		Redirects the user back to the order detail if the action failed
	 */
	public function actionGet() {
		return $this->redirect('/account/orders/' . app()->getRouteInfo()['order_id']);
	}
}