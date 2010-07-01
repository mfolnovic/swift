<?php

class DB extends Base {
	var $connections = array();
	
	function __construct() {
		global $config;
		
		foreach( $config -> options[ 'database' ] as $name => $options ) {
			$driver = ucfirst( $options[ 'driver' ] );
			include_once "{$options['driver']}.php";
			$this -> connections[ $name ] = new $driver;
			$this -> connections[ $name ] -> options = $options;
		}
	}
}

$db = new DB;

?>
