<?php

class CacheDriver extends Base {
	function __construct() {

	}
	
	function __destruct() {

	}
	
	function set( $index, $value ) {
		apc_store( $index, $value );
	}
	
	function get( $index )  {
		return apc_fetch( $index );
	}
	
	function delete( $index ) {
		apc_delete( $index );
	}
}

?>
