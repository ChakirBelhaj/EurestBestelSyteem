<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\Session\Session;
use App\Services\Mailer\UserMailer;

class LiveOverviewController extends Controller
{
	/**
	 * Show template for Live Orders Overview
	 */
	public function show()
	{
		return $this->view('frontend/orders/index.twig');
	}

	/**
	 * Return all orders that need to be processed yet
	 */
	public function getOrders()
	{
		$last_order_id = 0;

		// Set last pull time
		Session::set('time',time(),'LiveOverview');

		$results = array();
		$orders = app('database')->table('orders')->where('status', ORDER::COMPLETED)->orWhere('status', ORDER::PROCESSING)->get();

		// Loop through all the items to only give the required info
		foreach($orders as $item)
		{
			$results[] = array(
				'id' => $item->id,
				'status' => $item->status,
				'items' => $this->getOrderItems($item->id),
				'user_name' => User::find($item->user_id)->fullname()
			);

			if($item->id > $last_order_id) {
				$last_order_id = $item->id;
			}
		}

		Session::set('last_order_id',$last_order_id,'LiveOverview');

		// return
		return $this->json($results);
	}

	/**
	 * Return all new orders since fetch() or update() was called
	 */
	public function updateOrders()
	{
		$results = array();
		$time = Session::get('time','LiveOverview');
		$last_order_id = Session::get('last_order_id','LiveOverview');

		// Check getOrders() has already been called
		if(empty($time) || $time == null) {
			header("HTTP/1.1 500 Internal Server Error");
			exit('You need to fetch before updating!');
		}

		// Get timestamp
		Session::set('time',time(),'LiveOverview');

		// Convert timestamp
		$time = date("Y-m-d H:i:s", $time);

		// Get orders
		$orders = app('database')->table('orders')->where([
			['id', '>', $last_order_id],
			['status', '=', ORDER::COMPLETED]]
		)->orWhere([
			['id', '>', $last_order_id],
			['status', '=', ORDER::PROCESSING]
		])->get();

		// Loop through all the items to only give the required info
		foreach($orders as $item)
		{
			$results[] = array(
				'id' => $item->id,
				'status' => $item->status,
				'items' => $this->getOrderItems($item->id)
			);

			if($item->id > $last_order_id) {
				$last_order_id = $item->id;
			}
		}

		Session::set('last_order_id',$last_order_id,'LiveOverview');

		// Return
		return $this->json($results);
	}

	/**
	 * Start the update for the order status
	 */
	public function updateOrder()
	{
		// Get the new status
		$status = request('status');

		// Call correct function for updating the status
		if($status == 'processing')
		{
			$return = $this->setOrderStatusProcessing();
		}
		elseif($status == 'complete')
		{
			$return = $this->setOrderStatusComplete();
		}
		elseif($status == 'cancel')
		{
			$return = $this->setOrderStatusCanceled();
		}
		else
		{
			return $this->json(array('status'=>'failed','reason'=>'Invalid status'));
		}

		// Return result in JSON format
		if($return == true) {
			return $this->json(array('status'=>'success'));
		} else {
			return $this->json(array('status'=>'failed','reason'=>'Failed to update status'));
		}
	}

	/**
	 * Update the order status
	 */
	private function setOrderStatusProcessing()
	{
		// Get orderID
		$id = intval(request('id'));

		// Update order status in the database
		app('database')->table('orders')->where('id', $id)->update(['status' => ORDER::PROCESSING]);

		return true;
	}

	/**
	 * Update the order status
	 */
	private function setOrderStatusComplete()
	{
		// Get OrderID
		$order_id = intval(request('id'));

		// Get UserID
		$user = app('database')->table('orders')->where('id', $order_id)->get();
		$user_id = $user{0}->user_id;

		// Get User and Order object
		$order = Order::find($order_id);
		$user = User::find($user_id);

		// Update order status in the database
		app('database')->table('orders')->where('id', $order_id)->update(['status' => ORDER::FINISHED]);

		// Send mail that order is ready for pickup
		$mail = UserMailer::sendOrderReadyEmail($user, $order);

		return $mail;
	}

	private function setOrderStatusCanceled()
	{
		// Get orderID
		$order_id = intval(request('id'));

		// Get UserID
		$user = app('database')->table('orders')->where('id', $order_id)->get();
		$user_id = $user{0}->user_id;

		// Get User and Order object
		$order = Order::find($order_id);
		$user = User::find($user_id);

		// Update order status in the database
		app('database')->table('orders')->where('id', $order_id)->update(['status' => ORDER::CANCELLED]);

		// Send mail that order is ready for pickup
		$mail = UserMailer::sendOrderCanceledEmail($user, $order);

		return $mail;
	}

	/**
	 * Get all order items and quantity
	 */
	private function getOrderItems($id)
	{
		$results = array();

		// Update order status in the database
		$items = app('database')->table('order_items')->where('order_id', $id)->get();

		// Loop through all the items to only give the required info
		foreach($items as $item)
		{
			$results[] = array(
				'name' => $item->name,
				'image' => $item->image,
				'amount' => $item->amount,
				'item_id' => $item->item_id,
				'remarks' => $item->remarks
			);
		}

		// return
		return $results;
	}
}