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
 * Swift Model Class -Type - Timestamp
 *
 * This class extends php DateTime used for manipulating dates.
 *
 * @author      Swift dev team
 * @package     Swift
 * @subpackage  Model
 */

class Model_Type_Timestamp extends DateTime {
	/**
	 * Better constructor
	 *
	 * @access  public
	 * @param   mixed value Value
	 * @return  void
	 */
	function __construct( $value = NULL ) {
		if( empty( $value ) ) $value = time();
		if( is_numeric( $value ) ) $value = "@$value";

		return parent::__construct( $value, new DateTimeZone( Config::instance() -> get( 'timezone' ) ) );
	}
	/**
	 * Ran when you try to echo instance of this class
	 *
	 * @access public
	 * @return string
	 */
	function __toString() {
		return $this -> format( Config::instance() -> get( 'format_date' ) );
	}
	/**
	 * Database format
	 *
	 * @access public
	 * @return string
	 */
	function toDatabase() {
		return $this -> format( "Y-m-d H:i:s" );
	}
	/**
	 * Unix timestamp
	 * @access public
	 * @return int
	 */
	function unixTimestamp() {
		return $this -> format( "U" );
	}
}

?>
