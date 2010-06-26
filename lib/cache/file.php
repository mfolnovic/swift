<?php

class Cache extends Base {
	var $cache = array();
	var $changed = false;
	
	function __construct() {
		$this -> readFromFile();
	}

	function __destruct() {
		$this -> writeToFile();
	}

	function __set( $index, $value ) {
		$this -> cache[ $index ] = $value;
		$this -> changed = true;
	}
	
	function __get( $index ) {
		if( !$this -> read ) $this -> readFromFile();
		
		return $this -> cache[ $index ];
	}
	
	private function readFromFile() {
		$f = fopen( CACHE_PATH, "w+" );
		while( $line = fgets( $f, 4096 ) ) {
			list( $index, $value ) = explode( "=", $line );
			$this -> cache[ $index ] = unserialize( $value );
		}
		fclose( $f );
	}
	
	private function writeToFile() {
		if( !$this -> changed ) return;
		$f = fopen( CACHE_PATH, "w" );
		foreach( $this -> cache as $id => $value )
			fwrite( $f, $id . '=' . serialize( $value ) . PHP_EOL );
		fclose( $f );
	}
}

?>
