<?php

namespace App\Controllers;

use App\Services\Resizer;

class ImageController extends Controller
{
	// Show image
	public function index()
	{
		// Call ImageResizer to format requested image
		app()->resolve('resizer')->resize();
	}
}