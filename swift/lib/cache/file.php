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
 * Swift Cache Class - File
 *
 * This class allows using files as cache
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Cache
 */

class Cache_File extends Base {
	/**
	 * Internal cache to speed up file cache
	*/
	var $cache   = array();
	/**
	 * Used to know if application tried to change some variable
	*/
	var $changed = false;

	/**
	 * Constructor
	 * Reads cache content from  file
	 *
	 * @access public
	 * @param  string $options Options
	 * @return void
	 */
	public function __construct($options) {
		$f = fopen(CACHE_PATH, "w+");

		while($line = fgets($f, 4096)) {
			list($index, $value)   = explode("=", $line);
			$this -> cache[$index] = unserialize($value);
		}

		fclose($f);
	}

	/**
	 * Destructor
	 * Writes cache content to file
	 *
	 * @access public
	 * @return void
	 */
	function __destruct() {
		if(!$this -> changed) {
			return;
		}

		$f = fopen(CACHE_PATH, "w");

		foreach($this -> cache as $id => $value)
			fwrite($f, $id . '=' . serialize($value) . PHP_EOL);

		fclose($f);
	}

	/**
	 * Gets value for $index in cache
	 *
	 * @access public
	 * @param  mixed  $index Index to search for
	 * @return mixed
	 */
	function get($index) {
		if(!$this -> read) {
			$this -> readFromFile();
		}

		return $this -> cache[$index];
	}

	/**
	 * Sets value with index $index to $value
	 *
	 * @access public
	 * @param  mixed  $index Index to search for
	 * @param  mixed  $value New value
	 * @return void
	 */
	function set($index, $value) {
		$this -> cache[$index] = $value;
		$this -> changed       = true;
	}
}

?>
