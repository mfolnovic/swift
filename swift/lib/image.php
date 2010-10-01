<?php

/**
 * Swift
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

/**
 * Swift Image Class
 *
 * Provides image manipulation like cropping, resizing etc. to Swift
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Image
 */


class Image extends Base {
	var $image;
	var $name;
	var $dir;
	var $width;
	var $height;

	/**
	 * Constructor
	 *
	 * @access public
	 * @param  string $path Path to image
	 * @return void
	 */
	public function __construct($path) {
		$this -> dir    = dirname($path) . '/';
		$this -> name   = filename($path);
		$this -> image  = new Imagick($path);
		$this -> width  = $this -> image -> getImageWidth();
		$this -> height = $this -> image -> getImageHeight();
	}

	/**
	 * Write current image to $name
	 *
	 * @access public
	 * @param  string $name Name of new image
	 * @return void
	 */
	public function write($path) {
		$this -> image -> writeImage($path);

		return $this;
	}

	/**
	 * Resize image proportionally, and then crop what's redundant
	 *
	 * @access public
	 * @param  int $width  Width of new image
	 * @param  int $height Height of new image
	 * @return object
	 */
	public function resizeAndCrop($width, $height) {
		$this -> image -> cropThumbnailImage($width, $height);

		return $this;
	}

	/**
	 * Resizes image to width $width anad height $height
	 *
	 * @param  int $width  Width of new image
	 * @param  int $height Height of new image
	 * @return object
	 */
	public function resize($width, $height) {
		$this -> image -> scaleImage($width, $height);

		$this -> width  = $this -> image -> getImageWidth();
		$this -> height = $this -> image -> getImageHeight();

		return $this;
	}

	/**
	 * Compresses image to compress rate $rate
	 *
	 * @param  int $rate Compress rate
	 * @return object
	 */
	function compress($rate = 80) {
		switch(strtolower(extension($this -> name))) {
			case 'jpg':
				$compression = Imagick::COMPRESSION_JPEG;
			default:
				$compression = Imagick::COMPRESSION_BZIP;
		}

		$this -> image -> setImageCompression($compression);
		$this -> image -> setImageCompressionQuality($rate);

		return $this;
	}
}

?>
