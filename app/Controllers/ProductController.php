<?php

namespace App\Controllers;

use App\Models\Product;

class ProductController extends Controller {	

	/*
		Display's a product detail page
	 */
	public function viewProduct() {
		$product = Product::findOrFail(app()->getRouteInfo()['product_id']);
		return $this->view('frontend/products/view.twig', ['product' => $product]);
	}

}