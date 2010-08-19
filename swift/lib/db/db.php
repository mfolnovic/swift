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
 * Swift Database Class
 *
 * This class works as singleton class for all cache adapters
 *
 * @package			Swift
 * @subpackage	Database
 * @author			Swift dev team
 */

class Db extends Base {
	static $instances = array();

	/**
	 * Returns singleton instance of adapter $adapter
	 * @access	public
	 * @return	object
	 */
	static function instance( $adapter ) {
		global $config;

		if( !isset( self::$instances[ $adapter ] ) ) {
			$conf			= $config -> options[ 'database' ][ $adapter ];
			$adapter	= 'Db_' . ucfirst( $conf[ 'adapter' ] );

			self::$instances[ $adapter ] = new $adapter( $conf );
		}

		return self::$instances[ $adapter ];
	}
}

?>
