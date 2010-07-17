<?php

class Image extends Base {
	var $image, $name, $dir;
	
	function __construct( $path ) {
		$this -> name = basename( $path );
		$this -> dir = dirname( $path ) . '/';

		$this -> image = new Imagick( $path );
	}
	
	function write( $name, $ext = false ) {
		if( $ext === false ) $ext = substr( $this -> name, -3 );
		$this -> image -> writeImage( $this -> dir . $name . '.' . $ext );
	}
	
	function resizeAndCrop( $width, $height ) {
		$this -> image -> cropThumbnailImage( $widht, $height );
	}
}

?>
