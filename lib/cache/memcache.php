<?php

class CacheDriver extends Base {
	var $conn;

	function __construct() {
		global $config;
	
		$this -> conn = memcache_pconnect( $config -> options[ 'cache' ][ 'host' ], $config -> options[ 'cache' ][ 'port' ] );
	}
	
	function __destruct() {
		memcache_close( $this -> conn );
	}
	
	function __get( $index ) {
		return $this -> conn -> get( $index );
	}
	
	function __set( $index, $value ) {
		$this -> conn -> set( $index, $value );
		
		return $this;
	}
	
	function delete( $index ) {
		$this -> conn -> flush( $index ); 
	}
}

?>
