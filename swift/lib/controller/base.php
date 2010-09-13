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
 * Swift Controller Class - Base
 *
 * This class gives methods in all application's controllers
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Controller
 */

class Controller_Base extends Base {
	/**
	 * Contains all before_filters
	 */
	var $before_filters = array();
	/**
	 * Contains all after_filters
	 */
	var $after_filters = array();
	/**
	 * Contains all $_POST data
	 */
	var $data;
	/**
	 * Contains reference to configuration
	 * @todo Needed?
	 */
	var $config;
	/**
	 * All vars available in views are here
	 */
	var $globals = array();
	/**
	 * Name of controller
	 * @todo Needed?
	 */
	var $controllerName = "";

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		global $config, $controller;

		$this -> globals = array();
		$this -> config =& $config -> options;
		$this -> controllerName = strtolower( substr( get_class( $this ), 0, -10 ) );
		$this -> current_time = time(); // needed?
		$this -> csrf_token = Security::instance() -> csrf_token;
	}

	/**
	 * Runs before_filters
	 *
	 * @access public
	 * @return void
	 * @todo   before_filters should be called before every call, not just constructing
	 */
	function run_before_filters() {
		foreach( $this -> before_filters as $function )
			call_user_func( array( $this, $function ) );
	}

	/**
	 * Destructor
	 * Runs after_filters
	 *
	 * @access public
	 * @return void
	 * @todo   after_filters should be called after every call, not just destructing
	 */
	function run_after_filters() {
		foreach( $this -> after_filters as $function )
			call_user_func( array( $this, $function ) );
	}

	/**
	 * Adds new before_filter
	 *
	 * @access public
	 * @param	 string $function1, ... Function which should be run as before_filter
	 * @return void
	 * @todo   Options as last argument?
	 * @todo   More DRY between before_filter and after_filter
	 */
	function before_filter() {
		$functions = func_get_args();
		if( is_array( end( $functions ) ) ) $options = array_pop( $functions );
		else $options = array();

		foreach( $functions as $function )
			$this -> before_filters += array( $function, $options );
	}

	/**
	 * Adds new after filter
	 *
	 * @access public
	 * @param  string $function1, ... Function which should be run as after_filter
	 * @return void
	 * @todo   Options as last argument?
	 */
	function after_filter( $function ) {
		$functions = func_get_args();
		if( is_array( end( $functions ) ) ) $options = array_pop( $functions );
		else $options = array();

		foreach( $functions as $function )
			$this -> after_filters += array( $function, $options );
	}

	/**
	 * Used for changing layout
	 *
	 * @access public
	 * @param  string $layout Name of new layout
	 * @return void
	 */
	function layout( $layout ) {
		View::instance() -> layout = $layout;
	}

	/**
	 * Redirects to $url
	 *
	 * @access public
	 * @param  string $url Url to redirect to
	 * @return void
	*/
	function redirect( $url ) {
		if( isAjax() ) {
			header( "X-Redirect: $url" );
			Router::instance() -> route( $url );
		}	else {
			header( "Location:" . URL_PREFIX . $url );
		}
	}

	/**
	 * Changes what it should be rendered
	 *
	 * @access public
	 * @param  string $url Path which should be rendered
	 * @return return
	 */
	function render( $path ) {
		View::instance() -> render = $path;
	}

	/**
	 * Tells router that it missed action and should continue finding right one
	 *
	 * @access public
	 * @return void
	 */
	function notFound() {
		Router::instance() -> continueRouting = true;
	}

	/**
	 * Caches actions specified as arguments
	 *
	 * @access public
	 * @param  string $action,... Names of actions to cache
	 * @return return
	 */
	function caches_action() {
		$view = View::instance();
		foreach( func_get_args() as $action )
			$view -> action_caches[] = array( &$this -> controllerName, $action );
	}

	/**
	 * Gets global var with index $index
	 *
	 * @access public
	 * @param  mixed $index Index
	 * @return mixed
	 */
	function &__get( $index ) {
		return $this -> globals[ $index ];
	}

	/**
	 * Sets var $index to value $value
	 *
	 * @access public
	 * @param  mixed $index Index
	 * @param  mixed $value Value
	 * @return object
	 */
	function __set( $index, $value ) {
		$this -> globals[ $index ] = $value;
		return $this;
	}

	/**
	 * Tests if global var with index $index exists
	 *
	 * @access public
	 * @param  mixed $index Index
	 * @return object
	*/
	function __isset( $index ) {
		return isset( $this -> globals[ $index ] );
	}
};

?>
