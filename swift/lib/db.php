<?php

/**
 * Swift
 *
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 * @package		Swift
 */

/**
 * Swift Database Class
 *
 * This class works as factory class for all cache adapters
 *
 * @author			Swift dev team
 * @package			Swift
 * @subpackage	Database
 */

class Db extends Base {
	/**
	 * This array contains all factory instances of each database adapters
	 * application tried to use.
	 */
	static $adapters = array();

	/**
	 * Returns factory instance of adapter $adapter
	 *
	 * @access public
	 * @param  string $adapter Adapter name
	 * @return object
	 * @static
	 */
	static function factory( $adapter ) {
		if( !isset( self::$adapters[ $adapter ] ) ) {
			$options = Config::instance() -> get( 'database', $adapter );
			$adapter = 'Db_' . ucfirst( $options[ 'adapter' ] );

			self::$adapters[ $adapter ] = new $adapter( $options );
		}

		return self::$adapters[ $adapter ];
	}
}

?>
