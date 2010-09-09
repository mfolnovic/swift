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
 * Swift Model Class
 *
 * Singleton for all models, and stores all rows in one place
 * In future, this will provide easier caching
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Model
 */

class Model extends Base {
	/**
	 * Model_Table instances for all tables
	*/
	var $tables = array();

	/**
	 * Creates new table
	 *
	 * @access public
	 * @param  string tableName Name of table
	 * @return object
	 */
	function create( $tableName ) {
		if( !isset( $this -> tables[ $tableName ] ) ) {
			$path = MODEL_DIR . $tableName . '.php';
			if( file_exists( $path ) ) {
				include $path;
				$this -> tables[ $tableName ] = array();
			} else {
				trigger_error( "Model $tableName doesn't exist!", ERROR );
			}
		}

		return new $tableName;
	}

	static function schema( $tableName, $field = NULL ) {
		$schema = model( $tableName ) -> schema;

		if( !empty( $field ) ) {
			// just an workaround for now, since getting schema doesn't work for joins
			$schema = isset( $schema[ $field ] ) ? $schema[ $field ] : array( 'type' => 'integer' );
		}

		return $schema;
	}
}

?>
