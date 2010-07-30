<?php

class Cache {
	static $instances = array();
	
	static function getInstance( $name ) {
		global $config;
		
		if( !isset( self::$instances[ $name ] ) ) {
			$conf = $config -> options[ 'cache' ][ $name ];
			include LIB_DIR . "cache/{$conf[ 'driver' ]}.php";
			$driver = "Cache_" . ucfirst( $conf[ 'driver' ] );
			self::$instances[ $name ] = new $driver( $conf );
		}
		
		return self::$instances[ $name ];
	}
}

?>
