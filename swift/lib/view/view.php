<?php

/**
 * Swift
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

/* 
	@todo	Move this somewhere else 
*/

include APP_DIR . "helpers.php";
include LIB_DIR . "view/helpers.php";

/**
 * Swift View Class
 *
 * This class is resposible for all rendering
 *
 * @package			Swift
 * @subpackage	View
 * @author			Swift dev team
 */

class View {
	var $layout = 'application';
	var $render = true;
	var $config;
	var $action_caches = array();
	static $instance;

	/**
	 * Constructor
	 * @access	public
	 * @return	void
	 */
	function __construct() {
		$this -> config =& $GLOBALS[ 'config' ] -> options;
		ob_start( 'gz_handler' );	
	}

	/**
	 * Destructor
	 * @access	public
	 * @return	void
	 */
	function __destruct() {
		echo ob_get_clean();
	}

	/**
	 * This function is responsible for rendering and caching
	 * @access	public
	 * @param		string	c	Controller
	 * @param		string	a	Action
	 * @return	void
	 * @todo		fix caching, filename should be current url
	 */
	function render( $c = NULL, $a = NULL ) {
		global $config, $controller;

		if( $this -> render === FALSE ) return;
		else if( $this -> render === TRUE ) {
			if( empty( $c ) ) $c = $controller -> controller;
			if( empty( $a ) ) $a = $controller -> action;

			$path = $c . '/' . $a . '.php';
		} else
			$path = $this -> render . '.php';

		$cache = str_replace( '/', '_', $path );
		if( file_exists( TMP_DIR . "caches/$cache.php" ) ) {
			include TMP_DIR . "caches/$cache.php";
			return;
		} 

		if( !file_exists( TMP_DIR . "/views/$cache" ) || !$config -> options[ 'other' ][ 'cache_views' ] )
			View_Haml::getInstance() -> parse( VIEWS_DIR . $path, TMP_DIR . "/views/$path" );

		if( isset( $controller -> instance ) )
			extract( $controller -> instance -> globals );

		ob_start();
		$view = View::getInstance();
		include TMP_DIR . '/views/' . $path;
		if( in_array( array( $c, $a ), $this -> action_caches ) ) {
			$content = ob_get_clean();
			file_put_contents( TMP_DIR . "caches/$cache.php", $content );
			echo $content;
		} else {
			ob_end_flush();
		}
	}

	/**
	 * Singleton
	 * @access	public
	 * @return	object
	 */
	static function getInstance() {
		if( empty( self::$instance ) ) self::$instance = new View;
		return self::$instance;
	}
}

?>
