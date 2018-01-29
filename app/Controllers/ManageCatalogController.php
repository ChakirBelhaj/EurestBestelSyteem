<?php

namespace App\Controllers;

use App\Services\Redirect;
use App\Models\Category;
use App\Models\Product;

class ManageCatalogController extends Controller {

	/*
		Redirects to /manage/products, nothing here
	 */
	public function index() {
		return new RedirectResponse('/manage/products');
	}

	/*
		Lists all products
	 */
	public function products() {
		return $this->view('frontend/manage/products/index.twig', ['products' => Product::all()]);
	}

	/*
		Lists a single product
	 */
	public function product() {
		$product = Product::findOrFail(app()->getRouteInfo()['product_id']);
		return $this->view('frontend/manage/products/edit.twig', ['product' => $product, 'categories' => Category::all()]);
	}

	/*
		handles product update at POST /manage/products/product/{product_id}
	 */
	public function postProduct() {
		$product = Product::findOrFail(app()->getRouteInfo()['product_id']);
		if (empty(app()->request->get('name')) || empty(app()->request->get('category_id')) || empty(app()->request->get('price'))) {
			error('Vul alle velden in');
			return Redirect::back();
		}

		$price = $this->convertStringPrice(app()->request->get('price'));
		if (!is_int($price)) {
			error('Prijs is ongeldig');
			return Redirect::back();
		}

		$salePrice = $this->convertStringPrice(app()->request->get('sale_price'));
		$salePrice = is_int($salePrice) ? $salePrice : NULL;

		$image = NULL;

		// The user uploaded an image
		if($this->uploadedImage()) {
			// Returns a base64 encoded image, or a redirect response, so we check
			$image = $this->handleProductImageUpload();
			if (!is_string($image)) {
				return $image;
			}
		}

		$date = new \DateTime();

		$product_data = [
			'name' => app()->request->get('name'),
			'description' => app()->request->get('description'),
			'price' => $price,
			'sale_price' => $salePrice,
			'category_id' => app()->request->get('category_id'),
			'updated_at' => $date->format('Y-m-d H:i:s')
		];

		// Only add image if it has been changed
		if(!empty($image)) {
			$product_data['image'] = $image;
		}

		// Update project
		$product->update($product_data);

		message('Product bijgewerkt.');
		return Redirect::back();
	}

	/*
		Displays the new product view at /manage/products/new
	 */
	public function newProduct() {
		return $this->view('frontend/manage/products/new.twig', ['categories' => Category::all()]);
	}

	/*
		Handles product creation at POST /manage/products/new
	 */
	public function postNewProduct() {
		if (empty(app()->request->get('name')) || empty(app()->request->get('category_id')) || empty(app()->request->get('price'))) {
			error('Vul alle velden in');
			return Redirect::back();
		}

		$price = $this->convertStringPrice(app()->request->get('price'));
		if (!is_int($price)) {
			error('Prijs is ongeldig');
			return Redirect::back();
		}

		$salePrice = $this->convertStringPrice(app()->request->get('sale_price'));
		$salePrice = is_int($salePrice) ? $salePrice : NULL;

		$image = NULL;
		// The user uploaded an image
		if ($this->uploadedImage()) {

			// Returns a base64 encoded image, or a redirect response, so we check
			$image = $this->handleProductImageUpload();
			if (!is_string($image)) {
				return $image;
			}
		}
		
		// Insert the product
		Product::insert([
			'name' => app()->request->get('name'),
			'category_id' => app()->request->get('category_id'),
			'price' => $price,
			'sale_price' => $salePrice,
			'image' => $image,
			'description' => app()->request->get('description')
		]);
		message('Product is toegevoegd');

		return Redirect::to('/manage/products');
	}

	/*
		Handles product deletion
	 */
	public function postProducts() {
		if (empty(app()->request->get('delete'))) {
			return Redirect::back();
		}

		// Delete the category and redirect back
		app('database')->table('products')->where('id', app()->request->get('delete'))->delete();
		message('De product is verwijderd');
		return Redirect::back();
	}

	/*
		Handles GET /manage/categories, displays the categories
	 */
	public function categories() {
		return $this->view('frontend/manage/categories/index.twig', ['categories' => Category::all()]);
	}

	/*
		Handles category deletion
	 */
	public function postCategories() {
		if (empty(app()->request->get('delete'))) {
			return Redirect::back();
		}

		// Check wether there are still products in this category
		$products = app('database')->table('products')->where('category_id', app()->request->get('delete'))->get();
		
		if (count($products) != 0) {
			error('Je kan alleen een categorie verwijderen wanneer er zich geen producten in de categorie bevinden. Verwijder of verplaats eerst de producten alvorens de categorie te verwijderen');
			return Redirect::back();
		}

		// Delete the category and redirect back
		app('database')->table('categories')->where('id', app()->request->get('delete'))->delete();
		message('De categorie is verwijderd');
		return Redirect::back();
	}

	/*
		Shows the edit category page
	 */
	public function category() {
		$category = Category::findOrFail(app()->getRouteInfo()['category_id']);
		return $this->view('frontend/manage/categories/edit.twig', ['category' => $category]);
	}

	/*
		Handles editting the category
	 */
	public function postCategory() {
		$category = Category::findOrFail(app()->getRouteInfo()['category_id']);
		if (empty(app()->request->get('name'))) {
			error('De categorie naam kan niet leeg zijn');
			return Redirect::back();
		} 

		$date = new \DateTime();
		$category->update(['name' => app()->request->get('name'), 'updated_at' => $date->format('Y-m-d H:i:s')]);

		// Only allow 1 sale category
		$saleCategory = app('database')->table('categories')->select()->where('is_sale', 1)->first();
		if (app()->request->get('is_sale') == 'on' && $saleCategory != NULL && $saleCategory->id != $category->id) {
			error('Er kan maar 1 sale categorie tegelijk actief zijn');
			return Redirect::back();
		}
		
		$isSale = app()->request->get('is_sale') == 'on' ? 1 : 0;
		$isEvent = app()->request->get('is_event') == 'on' ? 1 : 0;

		// You can either set the sale property or event property, not both
		if ($isSale && $isEvent) {
			error('Een sale category kan niet exclusief voor een evenement zijn en vice versa.');
			return Redirect::back();
		}

		$category->update(['is_sale' => $isSale, 'is_event' => $isEvent]);
		message('Categorie bijgewerkt');
		return Redirect::back();
	}

	/*
		Displays the new category view at /manage/categories/new
	 */
	public function newCategory() {
		return $this->view('frontend/manage/categories/new.twig');
	}

	/*
		Handles the creation of a new category at POST /manage/categories/new
	 */
	public function postNewCategory() {
		if (empty(app()->request->get('name'))) {
			error('De categorie naam kan niet leeg zijn');
			return Redirect::back();
		} 

		// Only allow 1 sale category
		$saleCategory = app('database')->table('categories')->select()->where('is_sale', 1)->first();
		if (app()->request->get('is_sale') == 'on' && $saleCategory != NULL) {
			error('Er kan maar 1 sale categorie tegelijk actief zijn');
			return Redirect::back();
		}
		
		$isSale = app()->request->get('is_sale') == 'on' ? 1 : 0;
		$isEvent = app()->request->get('is_event') == 'on' ? 1 : 0;

		// You can either set the sale property or event property, not both
		if ($isSale && $isEvent) {
			error('Een sale category kan niet exclusief voor een evenement zijn en vice versa.');
			return Redirect::back();
		}

		Category::insert(['name' => app()->request->get('name'), 'is_sale' => $isSale, 'is_event' => $isEvent]);
		message('Categorie aangemaakt');
		return Redirect::back();
	}

	/*
		Method that checks wether an image file has been submitted
	 */
	private function uploadedImage() {
		return (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name']));
	}

	/*
		Checks the uploaded file and returns either a base64 encoded string of the image, or a redirectresponse and a flash message on error
	 */
	private function handleProductImageUpload() {
		$file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $file_error = $_FILES['image']['error'];
        $expensions = array("image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp");

        if ($file_error !== UPLOAD_ERR_OK) {
            error('There was an error uploading your file: ' . $file_error);
            return Redirect::back();
        }

        if (in_array($file_type, $expensions) === false) {
            error("extension not allowed, please choose a JPEG or PNG file.");
            return Redirect::back();
        }

        if($file_size > 2097152) {
            error('File size must be exactly 2 MB');
            return Redirect::back();
		}

		// Get the image data
        return base64_encode(file_get_contents($_FILES['image']['tmp_name']));
	}

	/*
		Converts a string price into a integer price in cents for the database
	 */
	private function convertStringPrice($price = '') {
		if (strpos($price, ',') !== false || strpos($price, '.') !== false ) {
			$price = str_replace('.', '', str_replace(',', '', $price));
			$price = intval($price);
		}
		else {
			$price = intval($price);
			if (is_int($price)) {
				$price = $price * 100;
			}
		}

		return $price;
	}

	/*
		Overriding the default controller view method, because we would like to display the category and products count on overy subpage of /manage, so we pass them by default
	 */
	protected function view(string $view, array $values = []) {
		$values = array_merge($values, [
			'number_of_products' => count(Product::all()),
			'number_of_categories' => count(Category::all()),
		]);
		return parent::view($view, $values);
	}
}