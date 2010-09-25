<?php

/**
 * Swift
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

/**
 * Swift Database Class
 *
 * This class works as factory class for all cache adapters
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Database
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
	public static function factory($adapter) {
		$options = Config::get('database', $adapter);
		$adapter = 'Db_' . ucfirst($options['adapter']);

		if(!isset(self::$adapters[$adapter])) {
			self::$adapters[$adapter] = new $adapter($options);
		}

		return self::$adapters[$adapter];
	}


	/**
	 * Searches through all tables specified in $tables, looking for table which
	 * has field $field. If it finds it, returns schema for that field
	 *
	 * @access public
	 * @param  string $tables description
	 * @return array
	 */
	public static function getSchema($tables, $field) {
		$schema =& Db_Migrations::instance() -> schema;

		if(is_string($tables)) {
			// current workaround
			return isset($schema[$tables][$field]) ? $schema[$tables][$field] : array('type' => 'integer');
		}

		foreach($tables as $table) {
			if(isset($schema[$table][$field])) {
				return $schema[$table][$field];
			}
		}

		return NULL;
	}
}

?>
