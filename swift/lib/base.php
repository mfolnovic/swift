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
 * Swift Base Class
 *
 * Each class is inherited by this one
 * Provides before_filter and after_filter (rails-like)
 *
 * @author      Swift dev team
 * @package     Swift
 * @subpackage  Base
 */

class Base {
	/**
	 * Contains all before_filters
	 */
	var $before_filters = array();
	/**
	 * Contains all after_filters
	 */
	var $after_filters = array();
	/**
	 * Contains all singleton instances
	*/
	static $instance = array();

	/**
	 * Constructor
	 * Runs before_filters
	 *
	 * @access public
	 * @return void
	 * @todo   before_filters should be called before every call, not just constructing
	 */
	function __construct() {
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
	function __destruct() {
		foreach( $this -> after_filters as $function )
			call_user_func( array( $this, $function ) );
	}

	/**
	 * Adds new before_filter
	 *
	 * @access public
	 * @param	 string function Function which should be run as before_filter
	 * @return void
	 * @todo   Options as last argument?
	 * @todo   More DRY between before_filter and after_filter
	 */
	function before_filter() {
		$functions = func_get_args();
		if( is_array( end( $functions ) ) ) $options = array_pop( $functions );

		$this -> before_filters += $function;
	}

	/**
	 * Adds new after filter
	 *
	 * @access public
	 * @param  string function Function which should be run as after_filter
	 * @return void
	 * @todo   Options as last argument?
	 */
	function after_filter( $function ) {
		$functions = func_get_args();
		if( is_array( end( $functions ) ) ) $options = array_pop( $functions );

		$this -> after_filters += $function;
	}

	/**
	 * Searches through all plugins and finds that function
	 *
	 * @access public
	 * @param  string name Function name
	 * @param  array  args Arguments
	 * @return void
	 */
	function __call( $name, $args ) {
		$plugins = Plugins::instance();
		$classes = get_parent_classes( get_class( $this ) );

		foreach( $classes as $class ) {
			$extends = $plugins -> extensions( $class );

			foreach( $extends as $object ) {
				$class_name = (string)$object -> name;
				$object = new $class_name( $this );

				if( method_exists( $object, $name ) ) {
					call_user_func_array( array( $object, $name ), $args );
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Same as above, but for static calls
	 *
	 * @access public
	 * @param  string name Function name
	 * @param  array  args Arguments
	 * @return void
	 * @todo   Implement it
	 * @todo   Optimize, call_user-func_array is slow!
	 */
	static function __callStatic( $name, $args ) {
//		call_user_func_array( array( __CLASS__, $name ), $args );
	}

	/**
	 * Global singleton
	 *
	 * @access public
	 * @param  string args Optional arguments passed to constructor
	 * @return object
	 */
	static function instance( $args = NULL ) {
		$name = get_called_class();
		if( !isset( self::$instance[ $name ] ) ) self::$instance[ $name ] = new $name( $args );
		return self::$instance[ $name ];
	}
}

?>
