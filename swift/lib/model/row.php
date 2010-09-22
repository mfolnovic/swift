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
 * Swift Model Class - Row
 *
 * Simple class for a database row
 * Gives class like access, like $row -> name instead of $row['name']
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Model
 */

class Model_Row implements IteratorAggregate {
	var $row       = array();
	var $parent    = NULL;
	/**
	 * Constructor
	 *
	 * @access public
	 * @param  array row Row
	 * @return void
	 */
	public function __construct(&$parent, $row = array()) {
		$this -> parent = $parent;

		foreach($row as $index => $value) {
			$this -> $index = $value;
		}
	}

	/**
	 * Gets value with index $index
	 *
	 * @access  public
	 * @param   string $index Index
	 * @return  mixed
	 */
	public function __get($index) {
		return isset($this -> row[$index]) ? $this -> row[$index] : NULL;
	}

	/**
	 * Sets $index to $value
	 *
	 * @access  public
	 * @param   mixed $index Index
	 * @param   mixed $value New value
	 * @return  object
	 */
	public function __set($index, $value) {
		$field = Db::getSchema($this -> parent -> tableName, $index);
		if($field['type'] == 'timestamp' && !($value instanceof Model_Type_Timestamp)) {
			$value = new Model_Type_Timestamp($value);
		}

		$this -> row[$index] = $value;
		return $this;
	}

	/**
	 * Isset
	 *
	 * @access  public
	 * @param   string $index Index
	 * @return  return
	 */
	public function __isset($index) {
		return isset($this -> row[$index]);
	}

	/**
	 * Allows iterating through model
	 *
	 * @access public
	 * @return object
	 */
	public function getIterator() {
		return new ArrayIterator($this -> row);
	}
}

?>
