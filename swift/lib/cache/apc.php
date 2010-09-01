<?php

/**
 * Swift framework
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

/**
 * Swift Cache Class - APC (Alternative PHP Cache)
 *
 * This class gives APC features to Swift Cache Class
 *
 * @author      Swift dev team
 * @package     Swift
 * @subpackage  Cache
 */

class Cache_Apc extends Base {
	/**
	 * Returns value of $key from cache
	 *
	 * @access public
	 * @param  string $key Key
	 * @return object
	 */
	function get( $key ) {
		return apc_fetch( $key );
	}

	/**
	 * Sets $key to $value
	 * Makes it expire in $expire seconds
	 *
	 * @access public
	 * @param  string $key    Key
	 * @param  string $value  New Value
	 * @param  int    $expire Expires in $expire seconds
	 * @return mixed
	 */
	function set( $key, $value, $expire = 0 ) {
		apc_store( $key, $value, $expire );

		return $value;
	}

	/**
	 * Returns true if $key exists in cache
	 *
	 * @access public
	 * @param  string $key Key
	 * @return bool
	 */
	function exists( $key ) {
		return $this -> get( $key ) !== FALSE;
	}

	/**
	 * Deletes value with key $key
	 *
	 * @access public
	 * @param  string $key Key
	 * @return void
	 */
	function delete( $key ) {
		apc_delete( $key );
	}

	/**
	 * Clears cache with type $type
	 *
	 * @access public
	 * @param	 string $type Type of cache it clears, default user
	 * @return void
	 */
	function clear( $type = 'user' ) {
		apc_clear_cache( $type );
	}
}

?>
