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
 * Swift Database Class _ Migration
 *
 * This class is used for running migrations
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Db
 */


class Db_Migration {
	var $connection = 'default';

	/**
	 * Creates table $table with schema $schema
	 *
	 * @access public
	 * @param  string $table  Table name
	 * @param  array  $schema Table schema
	 * @return void
	 */
	public function create_table($table, $schema) {
		$schema = array_merge(array('id' => array('type' => 'int', 'size' => 11, 'not_null' => true, 'auto_increment' => true)), $schema);
		Db::factory($this -> connection) -> createTable($table, $schema);
		Db_Migrations::instance() -> addToSchema($table, $schema);
	}
	/**
	 * Drops table $table
	 *
	 * @access public
	 * @param  string $table Table name
	 * @return void
	 */
	public function drop_table($table) {
	}
}


?>
