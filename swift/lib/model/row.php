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
	var $row = array();
	var $tableName = NULL;
	/**
	 * Constructor
	 *
	 * @access public
	 * @param  array row Row
	 * @return void
	 */
	function __construct( $tableName, $row = array() ) {
		$this -> tableName = $tableName;
		$schema = Model::schema( $tableName );

		foreach( $row as $index => $value ) {
			if( isset( $schema[ $index ] ) && $schema[ $index ][ 'type' ] == 'timestamp' ) {
				$value = new DateTime( $value );
				$value = $value -> format( 'U' );
			}

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
	function __get( $index ) {
		if( !isset( $this -> row[ $index ] ) ) return NULL;

		$field = Model::schema( $this -> tableName, $index );
		$value = $this -> row[ $index ];
		if( $field[ 'type' ] == 'timestamp' && !( $value instanceof DateTime ) ) $value = DateTime::createFromFormat( 'U', $value );

		return $value;
	}

	/**
	 * Sets $index to $value
	 *
	 * @access  public
	 * @param   mixed $index Index
	 * @param   mixed $value New value
	 * @return  object
	 */
	function __set( $index, $value ) {
		$field = Model::schema( $this -> tableName, $index );
		if( $field[ 'type' ] == 'timestamp' && $value instanceof DateTime ) $value = $value -> format( "%u" );

		$this -> row[ $index ] = $value;
		return $this;
	}

	/**
	 * Isset
	 * @access  public
	 * @param   string $index Index
	 * @return  return
	 */
	function __isset( $index ) {
		return isset( $this -> row[ $index ] );
	}

	/**
	 * Allows iterating through model
	 *
	 * @access public
	 * @return object
	 */
	function getIterator() {
		return new ArrayIterator( $this -> row );
	}
}

?>
