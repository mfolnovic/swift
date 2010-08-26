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
 * Swift Cache Class
 *
 * This class works as singleton class for all cache adapters
 *
 * @package			Swift
 * @subpackage	Cache
 * @author			Swift dev team
 */

class Cache extends Base {
	static $instances = array();

	/**
	 * Checks if page cache exists, if yes, load it and stop
	 * @access	public
	 * @param		string	name	description
	 * @return	return
	 */
	static function pageCache( $url ) {
		$path = TMP_DIR . "caches/" . str_replace( '/', '_', $url );
		if( file_exists( $path ) ) {
			include $path;
			exit;
		}
	}

	/**
	 * Returns singleton instance of adapter $adapter
	 * @access	public
	 * @param		string	$adapter	Adapter name
	 * @return	object
	 */
	static function factory( $adapter ) {
		if( !isset( self::$instances[ $adapter ] ) ) {
			global $config;

			$conf		= $config -> options[ 'cache' ][ $adapter ];
			$driver	= "Cache_" . ucfirst( $conf[ 'adapter' ] );

			self::$instances[ $adapter ] = new $driver( $conf );
		}

		return self::$instances[ $adapter ];
	}
}

?>
