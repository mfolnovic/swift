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
 * Swift Model Class
 *
 * Proxy for all models, and stores all rows in one place
 * In future, this will provide easier caching
 *
 * @package			Swift
 * @subpackage	Model
 * @author			Swift dev team
 */

class Model {
	var $tables = array();
	static $instance = NULL;

	/**
	 * Creates new table
	 * @access	public
	 * @param		string	tableName	Name of table
	 * @param		bool		newRow		Is it new row?
	 * @return	object
	 */
	function create( $tableName, $newRow = NULL ) {
		if( !isset( $this -> tables[ $tableName ] ) ) {
			include_once MODEL_DIR . $tableName . '.php'; // crashes if I don't put _once :S
			$this -> tables[ $tableName ] = array();
		}

		return new $tableName( $tableName, $newRow );
	}

	/**
	 * Singleton
	 * @access	public
	 * @return	object
	 */
	static function getInstance() {
		if( empty( self::$instance ) ) self::$instance = new Model;
		return self::$instance;
	}
}

?>
