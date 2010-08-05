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
 * Swift Controller Class - Flash messages
 *
 * Gives Rails-like flash messages
 *
 * @package			Swift
 * @subpackage	Controller
 * @author			Swift dev team
 */

class Controller_Flash extends Base {
	static $next = array();
	static $now = array();

	/**
	 * Discards all flash messages
	 * @access	public
	 * @return	void
	 * @todo		Implement it!
	 */
	function discard() {
	}

	/**
	 * Keeps all flash messages for one more action
	 * @access	public
	 * @return	void
	 * @todo		Implement it!
	 */
	function keep() {
	}

	/**
	 * Gets flash message with key $key
	 * @access	public
	 * @param		string	$key	Key of message to return
	 * @return	object
	 * @todo		Implement it!
	 */
	function get( $key, $type = 'next' ) {
	}

	/**
	 * Sets flash message with key $key to $value
	 * @access	public
	 * @param		string	$key		Key of message
	 * @param		mixed		$value	Value of message
	 * @return	void
	 * @todo		Implement it!
	 */
	function set( $key, $value, $type = 'next' ) {
	}
}

?>

