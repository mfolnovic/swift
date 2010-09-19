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
 * Swift Cache Class - Memcache
 *
 * This class allows using memcache as cache
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Cache
 */

class Cache_Memcache extends Base {
	/**
	 * Resource which contains connection to memcache
	*/
	var $conn;

	/**
	 * Constructor
	 * Connects to memcache
	 
	 * @access public
	 * @param  string $options Options for this adapter
	 * @return void
	 */
	public function __construct($options) {
		$this -> conn = memcache_pconnect($options['host'], $options['port']);
	}

	/**
	 * Destructor
	 * Disconnects from memcache
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		memcache_close($this -> conn);
	}

	/**
	 * Returns value of $key from cache
	 *
	 * @access public
	 * @param  string $key Key
	 * @return object
	 */
	public function get($key) {
		return $this -> conn -> get($key);
	}

	/**
	 * Sets $key to $value, and also puts $expire
	 *
	 * @access public
	 * @param  string $ke     Key
	 * @param  string $value  New Value
	 * @param  int    $expire Expires in $expire seconds
	 * @return object
	 */
	public function set($key, $value, $expire = 0) {
		$result = $this -> conn -> replace($key, $value, 0, $expire);

		if($result == FALSE) {
			$this -> conn -> set($key, $value, 0, $expire);
		}

		return $this;
	}

	/**
	 * Returns true if $key exists in cache
	 *
	 * @access public
	 * @param  string $key Key
	 * @return bool
	 */
	public function exists($key) {
		return $this -> get($key) === FALSE;
	}

	/**
	 * Deletes value for $key
	 *
	 * @access public
	 * @param  string $key Key
	 * @return object
	 */
	public function delete($key) {
		$this -> conn -> delete($key); 

		return $this;
	}

	/**
	 * Clears whole cache
	 *
	 * @access public
	 * @param	 string $type Type of cache it clears, default user
	 * @return object
	 */
	public function clear() {
		$this -> conn -> flush();

		return $this;
	}

	/**
	 * Pushes $what to array with key $key
	 *
	 * @access public
	 * @param	 string $key   Key in memcache
	 * @param	 mixed  $value Value which should be pushed
	 * @return object
	*/
	public function push($key, $what) {
		$array = $this -> $key;

		if(!is_array($array)) {
			$array = array();
		}

		$array[]      = $what;
		$this -> $key = $curr;

		return $this;
	}
}

?>
