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
 * Swift Cache Class - Memcache
 *
 * This class gives Memcache features to Swift Cache Class
 *
 * @package			Swift
 * @subpackage	Cache
 * @author			Swift dev team
 */

class Cache_Memcache extends Base {
	var $conn;

	/**
	 * Constructor
	 * Connects to memcache
	 
	 * @access	public
	 * @param		string	$options	Options for this adapter
	 * @return	void
	 */
	function __construct( $options ) {
		$this -> conn = memcache_pconnect( $options[ 'host' ], $options[ 'port' ] );
	}

	/**
	 * Destructor
	 * Disconnects from memcache
	 * @access	public
	 * @return	void
	 */
	function __destruct() {
		memcache_close( $this -> conn );
	}

	/**
	 * Returns value of $key from cache
	 * @access	public
	 * @param		string	$key	Key
	 * @return	object
	 */
	function get( $key ) {
		return $this -> conn -> get( $key );
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
		$result = $this -> conn -> replace( $key, $value, 0, $expire );
		if( $result == FALSE )
			$this -> conn -> set( $key, $value, 0, $expire );

		return $this;
	}

	/**
	 * Returns TRUE if $key exists in cache
	 * @access	public
	 * @param		string	$key	Key
	 * @return	bool
	 */
	function exists( $key ) {
		return $this -> get( $key ) === FALSE;
	}

	/**
	 * Deletes value for $key
	 * @access	public
	 * @param		string	$key	Key
	 * @return	void
	 */
	function delete( $key ) {
		$this -> conn -> delete( $key ); 
	}

	/**
	 * Clears whole cache
	 * @access	public
	 * @param		string	$type	Type of cache it clears, default user
	 * @return	void
	 */
	function clear() {
		$this -> conn -> flush();
	}

	function push( $key, $what ) {
		$curr = $this -> $key;
		if( !is_array( $curr ) ) $curr = array();
		$curr[] = $what;
		$this -> $key = $curr;
	}
}

?>
