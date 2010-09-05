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
	 * @param  array/null   newRow    If not NULL, then it signals it's a new row
	 * @return object
	 * @todo   __invoke?
	 * @todo  Avoid new row
	 */
	function create( $tableName, $newRow = NULL ) {
		$tableName = strtolower( $tableName );
		if( !isset( $this -> tables[ $tableName ] ) ) {
			$path = MODEL_DIR . $tableName . '.php';
			if( file_exists( $path ) ) {
				include_once $path; // crashes if I don't put _once :S
				$this -> tables[ $tableName ] = array();
			} else {
				trigger_error( "Model $tableName doesn't exist!", ERROR );
			}
		}

		return new $tableName( $tableName, $newRow );
	}

	static function schema( $tableName, $field = NULL ) {
		$schema = model( $tableName ) -> schema;
		if( !empty( $field ) ) $schema = isset( $schema[ $field ] ) ? $schema[ $field ] : array( 'type' => 'integer' ); // just an workaround for now, doesn't work for joins

		return $schema;
	}
}

?>
