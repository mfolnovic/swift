<?php

class Cache_Apc extends Base {
	function __construct( $options ) {

	}

	function set( $index, $value ) {
		apc_store( $index, $value );
		
		return $value;
	}
	
	function get( $index )  {
		return apc_fetch( $index );
	}
	
	function delete( $index ) {
		apc_delete( $index );
	}
	
	function exists( $index ) {
		return $this -> get( $index ) !== FALSE;
		return apc_exists( $index );
	}
	
	function clear( $type = 'user' ) {
		apc_clear_cache( $type );
	}
}

?>
