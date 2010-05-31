<?php

class Log extends Base {
	var $type = null; // file, output
	var $args = null;

	var $handle = null; // file handle

	function __construct( $type, $args ) {
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

	function log( $message ) {
		fwrite( $this -> handle, $message . PHP_EOL );
	}

	function error( $message ) {
		$this -> log( "[ERROR] $message" );
	}

	function notice( $message ) {
		$this -> log( "[NOTICE] $message" );
	}
}

$log = new Log( "file", "application" );

?>
