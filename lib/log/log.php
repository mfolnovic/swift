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
 * Swift Logger
 *
 * Maintains application log
 *
 * @package			Swift
 * @subpackage	Log
 * @author			Swift dev team
 */

class Log extends Base {
	var $type = NULL; // file, output
	var $args = NULL;
	var $handle = NULL; // file handle
	static $instance = NULL;

	/**
	 * Constructor
	 * @access	public
	 * @param		string	type	Which type of log
	 * @param		array		args	Configurations
	 * @return	void
	 * @todo		Create adapters, like cache and db
	 */
	function __construct( $type, $args ) {
		global $config;

		if( !$config -> options[ 'other' ][ 'log' ] ) return;
		$this -> type = $type;
		$this -> args = $args;

		if( $this -> type == 'file' )
			$this -> fileConstruct();
	}

	/**
	 * Destructor
	 * @access	public
	 * @return	void
	 */
	function __destruct() {
		if( $this -> type == 'file' )
			$this -> fileDestruct();
	}

	/**
	 * Singleton
	 * @access	public
	 * @static
	 * @return	object
	 */
	static function getInstance() {
		if( self::$instance == NULL ) self::$instance = new Log( "file", "application" ); // temporary
		return self::$instance;
	}

	/**
	 * File constructor
	 * @access	public
	 * @return	void
	 * @todo		Move to file adapter
	 */
	private function fileConstruct() {
		$this -> handle = fopen( LOG_DIR . $this -> args . ".log", "w" );
	}

	/**
	 * File destructor
	 * @access	public
	 * @return	void
	 * @todo		Move to file adapter
	 */
	private function fileDestruct() {
		fclose( $this -> handle );
	}

	/**
	 * Writes to log
	 * @access	public
	 * @param		string	message	Message to write
	 * @return	void
	 * @todo Support multiple adapters
	 */
	function write( $message ) {
		global $config;

		if( !$config -> options[ 'other' ][ 'log' ] ) return;
		fwrite( $this -> handle, $message . PHP_EOL );
	}

	/**
	 * Write error to log
	 * @access	public
	 * @param		string	message	Message to write with flag error
	 * @return	void
	 */
	function error( $message ) {
		$this -> write( "[ERROR] $message" );
	}

	/**
	 * Write notice to log
	 * @access	public
	 * @param		string	message	Message to write with flag notice
	 * @return	void
	 */
	function notice( $message ) {
		$this -> write( "[NOTICE] $message" );
	}
}

?>
