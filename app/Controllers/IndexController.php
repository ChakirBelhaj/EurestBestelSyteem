<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\Login\LoginService;

class IndexController extends Controller {
    /**
     * Function haalt alle categori
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function index()
	{
        $products = $this->filterProducts(Product::all());

        return $this->view('frontend/index/index.twig', [
            'categories' => $this->structureProductData($products)
        ]);
	}

    /**
     * Filtert producten uit db.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filter()
    {
        $_SESSION['old'] = request()->request;

        $query = Product::table();

        if (request('categories')) {
            $query->whereIn('category_id', request('categories'));
        }

        if (request('min_price')) {
            $query->where('price', '>=', request('min_price') * 100);
        }

        if (request('max_price')) {
            $query->where('price', '<=', request('max_price') * 100);
        }

        $products = $query->get();

        $products = $this->filterProducts($products);

        // Returned de gefilterde producten als er gefilterd is.
        return $this->view('frontend/index/index.twig', [
            'categories' => $this->structureProductData($products)
        ]);
    }

    /**
     * Filter all products according to event type.
     * @param $products
     * @return mixed
     */
    public function filterProducts($products)
    {
        if (LoginService::isLoggedIn() && LoginService::getCurrentUser()->currentOrder()->is_event) {
            $products = $products->filter(function ($product) {
                return (new Product($product))->category()->is_event;
            });
        } else {
            $products = $products->filter(function ($product) {
                return !(new Product($product))->category()->is_event;
            });
        }

        return $products;
    }

    /**
     * Combines category data with product data
     *
     * @param  array    $products   products you want to combine
     */
    private function structureProductData($products)
    {
        $categories = [];
        foreach (Category::all() as $category) {
            $newCategory = (array) $category;
            $newCategory['products'] = $products->filter(function($elm) use ($category) {
                return $category->id == $elm->category_id;
            });
            $categories[] = $newCategory;
        }

        usort($categories, function($a, $b) {
            return $a['is_sale'] < $b['is_sale'];
        });
        return $categories;
    }
}