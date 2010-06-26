<?php

class Cache extends Base {
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
		$result = $this -> conn -> replace( $index, $value );
		if( $result == false )
			$this -> conn -> set( $index, $value );
			
		return $this;
	}
	
	function delete( $index ) {
		$this -> conn -> flush( $index ); 
	}
	
	function push( $index, $what ) {
		$curr = $this -> $index;
		if( !is_array( $curr ) ) $curr = array();
		$curr[] = $what;
		$this -> $index = $curr;
	}
}

?>
