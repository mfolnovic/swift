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
 * Swift Database Class - Migrations
 *
 * Responsible for running migrations
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Db
 */

class Db_Migrations extends Base {
	/**
	 * Contains current schema
	 */
	var $schema     = array();

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this -> schema = Yaml::parse(MIGRATIONS_DIR . '.schema');
	}

	/**
	 * Runs migration in file $migration
	 *
	 * @access public
	 * @parram string $migration Name of file where migration is
	 * @return void
	 */
	public function run($migration) {
		$class_name = $this -> getName($migration);

		include MIGRATIONS_DIR . $migration;
		$instance = new $class_name;
		$instance -> up();
	}

	/**
	 * Gets number of last migration which was run last
	 *
	 * @access public
	 * @return int
	 */
	public function getMigrationNumber() {
		return (int) file_get_contents(MIGRATIONS_DIR . '.migration');
	}

	/**
	 * Gets list of migrations (files)
	 *
	 * @access public
	 * @return array
	 */
	public function getMigrations() {
		$ret = Dir::files(MIGRATIONS_DIR);
		natsort($ret);

		return $ret;
	}

	/**
	 * Returns class name of migration in file $migration
	 *
	 * @access public
	 * @param  string $migration File where migration is
	 * @return string
	 */
	public function getName($migration) {
		return underscoreToCamelCase(filename(substr($migration,strlen((int) $migration) + 1)));
	}

	/**
	 * Adds schema $schema of table $table to $this->schema
	 *
	 * @access public
	 * @param  string $table  Table name
	 * @param  array  $schema Changes in schema
	 * @return void
	 */
	public function addToSchema($table, $schema) {
		if(!isset($this -> schema[$table])) {
			$this -> schema[$table] = array();
		}

		$this -> schema[$table] = array_merge($this -> schema[$table], $schema);
	}

	/**
	 * Writes $this->schema to file
	 *
	 * @access public
	 * @return void
	 */
	public function writeSchema() {
		Yaml::write(MIGRATIONS_DIR . '.schema', $this -> schema);
	}
}

?>
