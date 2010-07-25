<?php

class Cache_apc extends Base {
	static function set( $index, $value ) {
		apc_store( $index, $value );
		
		return $value;
	}
	
	static function get( $index )  {
		return apc_fetch( $index );
	}
	
	static function delete( $index ) {
		apc_delete( $index );
	}
	
	static function exists( $index ) {
		return apc_exists( $index );
	}
}

?>
