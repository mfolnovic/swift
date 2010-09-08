<?php

/**
 * Swift
 *
 * @package   Swift
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
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
 * @package    Swift
 * @subpackage View
 * @author     Swift dev team
 */

class View extends Base {
	var $layout = 'application';
	var $render = true;
	var $action_caches = array();
	var $output = '';
	var $view_path = NULL;

	/**
	 * Destructor
	 *
	 * @access public
	 * @return void
	 */
	function __destruct() {
		if( !empty( Errors::$errors ) ) return;

		ob_start( 'gz_handler' );
		echo $this -> output;
		ob_end_flush();
	}

	/**
	 * This function is responsible for rendering and caching
	 *
	 * @access public
	 * @param  string	$controller	Controller
	 * @param  string	$action	Action
	 * @return void
	 * @todo   fix caching, filename should be current url
	 */
	function render( $controller = NULL, $action = NULL ) {
		$view_id = Benchmark::start();

		if( $this -> render === FALSE ) {
			return;
		} else if( $this -> render === TRUE ) {
			if( empty( $controller ) ) $controller = Controller::instance() -> controller;
			if( empty( $action ) ) $action = Controller::instance() -> action;

			$path = $controller . '/' . $action . '.php';
		} else {
			$path = $this -> render . '.php';
		}

		$cache    = TMP_DIR . "caches/" . str_replace( '/', '_', $path );
		$compiled = TMP_DIR . "views/$path";
		$template = ( $this -> view_path ? $this -> view_path : VIEWS_DIR ) . $path;

		if( file_exists( $cache ) ) {
			include $cache;
			return;
		}

		if( !file_exists( $compiled ) || !Config::instance() -> get( 'cache_views' ) ) {
			$haml_id = Benchmark::start();
			View_Haml::instance() -> parse( $template, $compiled );
			Log::write( $path, 'HAML', $haml_id );
		}

		if( isset( Controller::instance() -> object ) )
			extract( Controller::instance() -> object -> globals );

		$this -> end();
		ob_start();
		include $compiled;

		if( in_array( array( $controller, $action ), $this -> action_caches ) ) {
			$content = ob_get_clean();
			$this -> output .= $content;

			Dir::make_dir( $cache );
			file_put_contents( $cache, $content );
		} else {
			$this -> end();
		}

		ob_start();

		Log::write( $path, 'Render', $view_id );
	}

	/**
	 * Outputs and clears all ob buffers
	 *
	 * @access public
	 * @return void
	*/
	function end() {
		while( ob_get_level() > 0 )
			$this -> output .= ob_get_clean();
	}
}

?>
