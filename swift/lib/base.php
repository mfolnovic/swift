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
 * Swift Base Class
 *
 * Each class is inherited by this one
 * Provides before_filter and after_filter (rails-like)
 *
 * @package			Swift
 * @subpackage	Base
 * @author			Swift dev team
 */

class Base {
	var $before_filters = array();
	var $after_filters  = array();

	/**
	 * Constructor
	 * Runs before_filters
	 * @access	public
	 * @return	void
	 */
	function __construct() {
		foreach( $this -> before_filters as $function )
			call_user_func( array( $this, $function ) );
	}

	/**
	 * Destructor
	 * Runs after_filters
	 * @access	public
	 * @return	void
	 */
	function __destruct() {
		foreach( $this -> after_filters as $function )
			call_user_func( array( $this, $function ) );
	}

	/**
	 * Adds new before_filter
	 * @access	public
	 * @param		string	function	Function which should be run as before_filter
	 * @return	void
	 * @todo	Options as last argument?
	 */
	function before_filter() {
		$functions = func_get_args();
		if( is_array( end( $functions ) ) ) $options = array_pop( $functions );

		$this -> before_filters += $function;
	}

	/**
	 * Adds new after filter
	 * @access	public
	 * @param		string	function	Function which should be run as after_filter
	 * @return	void
	 * @todo	Options as last argument?
	 */
	function after_filter( $function ) {
		$functions = func_get_args();
		if( is_array( end( $functions ) ) ) $options = array_pop( $functions );

		$this -> after_filters += $function;
	}

	/**
	 * Searches through all plugins and finds that function
	 * @access	public
	 * @param		string	name	Function name
	 * @param 	array		args	Arguments
	 * @return	void
	 */
	function __call( $name, $args ) {
		$extends = Plugins::instance() -> extends[ get_class( $this ) ];
		foreach( $extends as $class ) {
			$class_name = (string)$class -> name;
			$object = new $class_name;

			if( method_exists( $object, $name ) )
				call_user_func_array( array( $object, $name ), $args );
		}
	}

	/**
	 * Same as above, but for static calls
	 * @access	public
	 * @param		string	name	Function name
	 * @param 	array		args	Arguments
	 * @return	void
	 * @todo		Optimize, call_user-func_array is slow!
	 */
	static function __callStatic( $name, $args ) {
		call_user_func_array( array( __CLASS__, $name ), $args );
	}

}

?>
