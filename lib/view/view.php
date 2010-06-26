<?php

include "haml.php";
include "helpers.php";

class View extends ViewHelpers {
	var $layout = 'application';
	
	function __construct() {
		ob_start( 'gz_handler' );	
	}
	
	function __destruct() {
		echo ob_get_clean();
	}
	
	function render( $c = null, $a = null ) {
		global $haml, $config, $controller;
	
		if( $c == null ) $c = $controller -> controller;
		if( $a == null ) $a = $controller -> action;

		$path = "$c/$a.php";

		if( !file_exists( TMP_DIR . "/views/$path" ) || !$config -> options[ 'other' ][ 'cache_views' ] )
			$haml -> parse( $path );
			
		extract( $controller -> globals );
		
		include TMP_DIR . '/views/' . $path;
	}
	
	function cacheView( $file, $content ) {
		list( $dir, $file ) = explode( '/', $file );
		@mkdir( TMP_DIR . "/views/$dir" );
		file_put_contents( TMP_DIR . "/views/$dir/$file", $content );
		apc_compile_file( TMP_DIR . "/views/$dir/$file" );
	}
}

$view = new View;

?>
