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
 * Swift Cache Class
 *
 * This class is responsible for all caches, e.g. memcache
 * apc, xcache, file etc.
 *
 * Also, this class is responsible for page caching
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Cache
 */

class Cache extends Base {
	/**
	 * This array contains all factory instances of each caching mechanism
	 * application tried to use.
	 */
	static $adapters = array();

	/**
	 * If cache for page with url. $url exists, load it.
   *
	 * @access public
	 * @param  string $name description
	 * @static
	 * @return return
	 */
	public static function pageCache($url) {
		$path = TMP_DIR . "caches/" . str_replace('/', '_', $url);

		if(file_exists($path)) {
			include $path;
			Log::write($url, "CACHE");
			exit;
		}
	}

	/**
	 * Returns factory instance of adapter $adapter
	 *
	 * @access public
	 * @param  string $adapter Adapter name
	 * @static
	 * @return object
	 */
	public static function factory($adapter) {
		if(!isset(self::$adapters[$adapter])) {
			$options = Config::get('cache', $adapter);
			$class   = "Cache_" . ucfirst($options['adapter']);

			self::$adapters[$adapter] = new $class($options);
		}

		return self::$adapters[$adapter];
	}
}

?>
