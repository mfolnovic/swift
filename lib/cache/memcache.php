<?php

class Cache_Memcache extends Base {
	var $conn;

	function __construct( $options ) {
		global $config;
	
		$this -> conn = memcache_pconnect( $options[ 'host' ], $options[ 'port' ] );
	}
	
	function __destruct() {
		memcache_close( $this -> conn );
	}
	
	function get( $index ) {
		return $this -> conn -> get( $index );
	}
	
	function set( $index, $value ) {
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
