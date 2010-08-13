<?php

/**
 * Swift
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

/**
 * Swift Logger - File
 *
 * This class allows writing logs in files
 *
 * @package			Swift
 * @subpackage	Log
 * @author			Swift dev team
 */

class Log_File extends Base {
	var $handle = NULL;
	var $options = NULL;
	var $output = '';

	/**
	 * Constructor
	 * @access	public
	 * @param		array	options	Options
	 * @return	void
	 */
	function __construct( $options ) {
		$this -> options	= $options;

		$this -> write( '' ); // empty line
		$this -> write( date( 'm.d.y H:m:s' ) );
	}

	/**
	 * Destructor
	 * @access	public
	 * @return	void
	 */
	function __destruct() {
		$handle = fopen( LOG_DIR . $this -> options[ 'file' ], "a" );
		fwrite( $handle, $this -> output );
		fclose( $handle );
	}

	/**
	 * Writes message
	 * @access	public
	 * @param		string	message	Message to write
	 * @return	return
	 */
	function write( $message ) {
		$this -> output .= $message . PHP_EOL;
	}
}

?>
