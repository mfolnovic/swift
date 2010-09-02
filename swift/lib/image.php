<?php

/**
 * Swift
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

/**
 * Swift Image Class
 *
 * Provides image manipulation like cropping, resizing etc. to Swift
 *
 * @package			Swift
 * @subpackage	Image
 * @author			Swift dev team
 */


class Image extends Base {
	var $image, $name, $dir, $width, $height;

	/**
	 * Constructor
	 * @access	public
	 * @param		string	path	Path to image
	 * @return	void
	 */
	function __construct( $path ) {
		$this -> dir = dirname( $path ) . '/';

		$this -> image = new Imagick( $path );
		$this -> width = $this -> image -> getImageWidth();
		$this -> height = $this -> image -> getImageHeight();
	}

	/**
	 * Write current image to $name
	 * @access	public
	 * @param		string	name	Name of new image
	 * @return	void
	 */
	function write( $name ) {
		$this -> image -> writeImage( $this -> dir . $name );
		
		return $this;
	}

	/**
	 * Resize image proportionally, and then crop what's redundant
	 * @access	public
	 * @param		int	width	Width of new image
	 * @param		int	height	Height of new image
	 * @return	object
	 */
	function resizeAndCrop( $width, $height ) {
		$this -> image -> cropThumbnailImage( $width, $height );
		
		return $this;
	}
}

?>
