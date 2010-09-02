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

class Model_Row {
	/**
	 * Constructor
	 *
	 * @access public
	 * @param  array row Row
	 * @return void
	 */
	function __construct( $row = array() ) {
		foreach( $row as $index => $value ) 
			$this -> $index = $value;
	}
}

?>
