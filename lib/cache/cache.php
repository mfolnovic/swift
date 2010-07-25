<?php

class Cache {
	var $instances = array();
	
	function loadDriver() {
		foreach( func_get_args() as $driver ) {
			if( isset( $instances[ $driver ] ) ) continue;
			include LIB_DIR . "/cache/$driver.php";
			$name = "Cache_$driver";
			$instances[ $driver ] = new $name;
		}
	}
}

$cache = new Cache;

?>
