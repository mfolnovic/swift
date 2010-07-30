<?php

class DB extends Base {
	static $instances = array();

	static function getInstance( $name ) {
		global $config;
		
		if( !isset( self::$instances[ $name ] ) ) {
			$conf = $config -> options[ 'database' ][ $name ];
			$driver = ucfirst( $conf[ 'driver' ] );
			include_once "{$conf['driver']}.php";
			self::$instances[ $name ] = new $driver( $conf );
		}
	
		return self::$instances[ $name ];
	}
}

?>
