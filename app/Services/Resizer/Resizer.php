<?php

namespace App\Services\Resizer;

use App\Services\Service;

class Resizer extends Service
{
	// Constructor
	public function __construct()
	{

	}

	/**
	 * Scales image to requested size with keeping the aspect ratio
	 *
	 * @return image
	 */
	public function resize()
	{
		// Get type of request
		$type = $_GET['t'];

		// Get a maximum height and width
		$requested_width = $_GET['w'];
		$requested_height = $_GET['h'];

		// We can't resize without sizes
		if(empty($requested_width) || empty($requested_height)) {
			exit('No width and or height supplied');
		}

		if($type == 'image')
		{
			// Get filename from image
			$filename = $_SERVER['DOCUMENT_ROOT'] . '/images/' .$_GET['img'];

			// Get the image id
			if(!file_exists($filename)) {
				return $this->showPlaceholder($requested_width,$requested_height);
			}

			// Get dimensions of current image
			list($width_orig, $height_orig, $type_orig, $attr_orig) = getimagesize($filename);
			
			// Check for supported image
			if($type < 1 && $type > 3) {
				exit('Invalid image type, only JPG, JPEG, PNG and GIF are supported');
			}

			// Convert image to GD object
			if($type_orig == 1) {
				// GIF
				$image_old = imagecreatefromgif($filename);
			} elseif($type == 2) {
				// JPEG
				$image_old = imagecreatefromjpeg($filename);
			} elseif($type == 3) {
				// PNG
				$image_old = imagecreatefrompng($filename);
			}
		} 
		elseif($type == 'product')
		{
			// Get the image id
			if (!isset($_GET['id']))
			{
				$this->showPlaceholder($requested_width,$requested_height);
			}

			$id = $_GET['id'];

			// Get image from the database
			$tmp = app('database')->table('products')->where('id', $id)->first();
			if (!$tmp)
			{
				exit('ID Not found');
			} 
			elseif (!$tmp->image)
			{
				// Show a default placeholder width the right size
				$this->output(imagecreatefromstring(file_get_contents("http://via.placeholder.com/{$requested_width}x{$requested_height}")));
			}

			// Decode image
			$image = base64_decode($tmp->image);

			// Get dimensions of current image
			list($width_orig, $height_orig) = getimagesizefromstring($image);

			// Get image
			$image_old = imagecreatefromstring($image);
		}
		else
		{
			exit('No valid request type!');
		}	

		// Calculate aspect ratio
		$ratio_orig = $width_orig/$height_orig;

		// Calculate new height and width
		if($requested_width / $requested_height > $ratio_orig) {
		   $width = $requested_height * $ratio_orig;
		   $height = $requested_height;
		} else {
		   $height = $requested_width / $ratio_orig;
		   $width = $requested_width;
		}

		// Create new empty image with new size
		$image_new = imagecreatetruecolor($width, $height);
		$image_wrapper = imagecreatetruecolor($requested_width, $requested_height);

		// Resample image in new image
		imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

		// Set background color to white
		$white = imagecolorallocatealpha($image_wrapper, 0, 0, 0, 127);

		// Fill background with transparent color
		imagefill($image_wrapper, 0, 0, $white);

		// Allow transparency to be saved
		imagesavealpha($image_wrapper, true);
		imagesavealpha($image_new, true);

		// Calculate the start position of the resampled image in the image
		$startX = ($requested_width - $width) / 2;
		$startY = ($requested_height - $height) / 2;

		// Add resampled image to blank image with correct requested sizes
		imagecopyresampled($image_wrapper, $image_new, $startX, $startY, 0, 0, $width, $height, $width, $height);

		// Output the image
		$this->output($image_wrapper);
	}

	/**
	 * Shows placeholder image with the requested size
	 *
	 * @param  integer $requested_width  	With of the image
	 * @param  integer $requested_height 	Height of the image
	 */
	private function showPlaceholder($requested_width = 500,$requested_height = 500)
	{
		// Show a default placeholder width the right size
		$this->output(imagecreatefromstring(file_get_contents("http://via.placeholder.com/{$requested_width}x{$requested_height}")));
	}

	/*
		Returns image and kills the script execution
	*/
	private function output($image)
	{
		// Content type
		header('Content-Type: image/jpeg');
		imagepng($image, null, 9);
		exit();
	}
}