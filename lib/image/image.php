<?php

class Image extends Base {
	var $image, $name, $dir, $width, $height;
	
	function __construct( $path ) {
		$this -> dir = dirname( $path ) . '/';

		$this -> image = new Imagick( $path );
		$this -> width = $this -> image -> getImageWidth();
		$this -> height = $this -> image -> getImageHeight();
	}
	
	function write( $name ) {
		$this -> image -> writeImage( $this -> dir . $name );
		
		return $this;
	}
	
	function resizeAndCrop( $width, $height ) {
		$this -> image -> cropThumbnailImage( $width, $height );
		
		return $this;
	}
}

?>
