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

	/**
	 * Constructor
	 * @access	public
	 * @param		array	options	Options
	 * @return	void
	 */
	function __construct( $options ) {
		$this -> options	= $options;
		$this -> handle		= fopen( LOG_DIR . $options[ 'file' ], "w" );
	}

	/**
	 * Destructor
	 * @access	public
	 * @return	void
	 */
	function __destruct() {
		fclose( $this  -> handle );
	}

	/**
	 * Writes message to a file
	 * @access	public
	 * @param		string	message	Message to write
	 * @return	return
	 */
	function write( $message ) {
		fwrite( $this -> handle, $message . PHP_EOL );
	}
}

?>
