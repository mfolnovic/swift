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
 * Swift Logger - File
 *
 * This class allows writing logs in files
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Log
 */

class Log_File extends Base {
	var $options = NULL;
	var $output = '';

	/**
	 * Constructor
	 *
	 * @access public
	 * @param  array options Options
	 * @return void
	 */
	function __construct( &$options ) {
		$this -> options = $options;
	}

	/**
	 * Destructor
	 *
	 * @access public
	 * @return void
	 */
	function __destruct() {
		file_put_contents( LOG_DIR . $this -> options[ 'file' ], $this -> output, FILE_APPEND );
	}

	/**
	 * Writes message
	 *
	 * @access public
	 * @param  string $message Message to write
	 * @return return
	 */
	function write( $message ) {
		$this -> output .= $message . PHP_EOL;
	}
}

?>
