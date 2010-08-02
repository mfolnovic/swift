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
	 * @todo	Allow to pass multiple function
	 * @todo	Options as last argument?
	 */
	function before_filter( $function ) {
		$this -> before_filters[] = $function;
	}

	/**
	 * Adds new after filter
	 * @access	public
	 * @param		string	function	Function which should be run as after_filter
	 * @return	void
	 * @todo	Allow to pass multiple function
	 * @todo	Options as last argument?
	 */
	function after_filter( $function ) {
		$this -> after_filters[] = $function;
	}
}

?>
