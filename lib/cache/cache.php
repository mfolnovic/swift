<?php

class Cache {
	static $instances = array();
	
	static function loadDrivers( $config ) {
		foreach( $config as $driver => $options ) {
			include LIB_DIR . "/cache/{$options[ 'driver' ]}.php";
			$name = "Cache_" . ucfirst( $options[ 'driver' ] );
			self::$instances[ $driver ] = new $name( $options );
		}
	}
	
	static function getInstance( $name ) {
		return self::$instances[ $name ];
	}
}

?>
