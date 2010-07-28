<?php

class Log extends Base {
	var $type = NULL; // file, output
	var $args = NULL;
	var $handle = NULL; // file handle
	static $instance = NULL;
	
	static function getInstance() {
		if( self::$instance == NULL ) self::$instance = new Log( "file", "application" ); // temporary
		return self::$instance;
	}

	function __construct( $type, $args ) {
		global $config;
		
		if( !$config -> options[ 'other' ][ 'log' ] ) return;
		$this -> type = $type;
		$this -> args = $args;

		if( $this -> type == 'file' )
			$this -> fileConstruct();
	}

	function __destruct() {
		if( $this -> type == 'file' )
			$this -> fileDestruct();
	}

	private function fileConstruct() {
		$this -> handle = fopen( LOG_DIR . $this -> args . ".log", "w" );
	}

	private function fileDestruct() {
		fclose( $this -> handle );
	}

	function write( $message ) {
		global $config;
		
		if( !$config -> options[ 'other' ][ 'log' ] ) return;
		fwrite( $this -> handle, $message . PHP_EOL );
	}

	function error( $message ) {
		$this -> write( "[ERROR] $message" );
	}

	function notice( $message ) {
		$this -> write( "[NOTICE] $message" );
	}
}

?>
