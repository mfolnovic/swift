<?php

include "haml.php";
include APP_DIR . "helpers.php";
include "helpers.php";

class View extends ViewHelpers {
	var $layout = 'application';
	var $render = true;
	var $config;
	var $action_caches = array();
	
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

		if( file_exists( TMP_DIR . "caches/{$c}_{$a}.php" ) ) {
			include TMP_DIR . "caches/{$c}_{$a}.php";
			return;
		} 

		$path = "$c/$a.php";

		if( !file_exists( TMP_DIR . "/views/$path" ) || !$config -> options[ 'other' ][ 'cache_views' ] )
			$haml -> parse( VIEWS_DIR . $path, TMP_DIR . "/views/$path" );

		extract( $controller -> instance -> globals );

		ob_start();
		include TMP_DIR . '/views/' . $path;
		if( in_array( array( $c, $a ), $this -> action_caches ) ) {
			$content = ob_get_clean();
			file_put_contents( TMP_DIR . 'caches/' .$c . "_" . $a . ".php", $content );
			echo $content;
		} else {
			ob_end_flush();
		}
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
