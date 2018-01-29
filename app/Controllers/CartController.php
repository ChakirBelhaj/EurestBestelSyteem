<?php

namespace App\Controllers;

class CartController extends Controller {

	/*
		Shows the cart to the user
	 */
	public function index() {
		return $this->view('frontend/cart/index.twig');
	}
}