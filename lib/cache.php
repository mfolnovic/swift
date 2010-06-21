<?php

include LIB_DIR . "cache/" . ( $config -> options[ 'cache' ][ 'driver' ] ) . ".php";

class Cache extends CacheDriver {
	
}

$cache = new Cache;

?>
