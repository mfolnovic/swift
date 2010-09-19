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
 * Factory for all models, and stores all rows in one place
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
	 * @param  string $name Name of model
	 * @return object
	 */
	public function factory($name) {
		if(!isset($this -> tables[$name])) {
			try {
				App::load('model', $name);
			} catch(AppException $e) {
				throw new ModelException("Model $name doesn't exist!");
			}

			$this -> tables[$name] = array();
		}

		return new $name;
	}

	public static function schema($name, $field = NULL) {
		$schema = model($name) -> schema;

		if(!empty($field)) {
			// just an workaround for now, since getting schema doesn't work for joins
			$schema = isset($schema[$field]) ? $schema[$field] : array('type' => 'integer');
		}

		return $schema;
	}
}

class ModelException extends Exception {}

?>
