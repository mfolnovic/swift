<?php

/**
 * Swift framework
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

/**
 * Swift Cache Class - APC
 *
 * This class gives APC features to Swift Cache Class
 *
 * @package			Swift
 * @subpackage	Cache
 * @author			Swift dev team
 */

class Cache_Apc extends Base {
	/**
	 * Returns value of $key from cache
	 * @access	public
	 * @param		string	$key	Key
	 * @return	object
	 */
	function get( $key )  {
		return apc_fetch( $key );
	}

	/**
	 * Sets $key to $value, and also puts $expire
	 * @access	public
	 * @param		string	$key	Key
	 * @param 	string	$value	New Value
	 * @param		integer	$expire	Expires in $expire seconds
	 * @return	object
	 */
	function set( $key, $value, $expire = 0 ) {
		apc_store( $key, $value, $expire );

		return $value;
	}

	/**
	 * Returns TRUE if $key exists in cache
	 * @access	public
	 * @param		string	$key	Key
	 * @return	bool
	 */
	function exists( $key ) {
		return $this -> get( $key ) !== FALSE;
	}

	/**
	 * Deletes value for $key
	 * @access	public
	 * @param		string	$key	Key
	 * @return	void
	 */
	function delete( $key ) {
		apc_delete( $key );
	}

	/**
	 * Clears whole cache
	 * @access	public
	 * @param		string	$type	Type of cache it clears, default user
	 * @return	void
	 */
	function clear( $type = 'user' ) {
		apc_clear_cache( $type );
	}
}

?>
