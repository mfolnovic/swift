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
	 * Returns singleton instance of adapter $adapter
	 * @access	public
	 * @param		string	$adapter	Adapter name
	 * @return	object
	 */
	static function getInstance( $adapter ) {
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
