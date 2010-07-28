<?php

include "haml.php";
include APP_DIR . "helpers.php";
include "helpers.php";

class View extends ViewHelpers {
	var $layout = 'application';
	var $render = true;
	var $config;
	
	function __construct() {
		$this -> config =& $GLOBALS[ 'config' ] -> options;
		ob_start( 'gz_handler' );	
	}
	
	function __destruct() {
		echo ob_get_clean();
	}
	
	function render( $c = null, $a = null ) {
		if( !$this -> render ) return;
		global $haml, $config, $controller;
	
		if( $c == null ) $c = $controller -> controller;
		if( $a == null ) $a = $controller -> action;

		$path = "$c/$a.php";

		if( !file_exists( TMP_DIR . "/views/$path" ) || !$config -> options[ 'other' ][ 'cache_views' ] )
			$haml -> parse( VIEWS_DIR . $path, TMP_DIR . "/views/$path" );

		extract( $controller -> instance -> globals );

		include TMP_DIR . '/views/' . $path;
	}
	
	function _cacheView( $file, $content ) {
		list( $dir, $file ) = explode( '/', $file );
		if( !file_exists( TMP_DIR . "/views/$dir" ) )
			mkdir( TMP_DIR . "/views/$dir" );
		file_put_contents( TMP_DIR . "/views/$dir/$file", $content );
		
		if( extension_loaded( 'apc' ) )	apc_compile_file( TMP_DIR . "/views/$dir/$file" );
	}
}

$view = new View;

?>
