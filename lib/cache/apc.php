<?php

class Cache extends Base {
	static function set( $index, $value ) {
		apc_store( $index, $value );
	}
	
	static function get( $index )  {
		return apc_fetch( $index );
	}
	
	static function delete( $index ) {
		apc_delete( $index );
	}
}

$cache = new Cache;

?>
