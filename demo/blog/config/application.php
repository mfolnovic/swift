<?php

define( 'URL_PREFIX', '/PRF/demo/blog/' );

global $config;
$config -> options[ 'database' ] = array(
	'default' => array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => 'pass',
		'database' => 'blog',
		'driver' => 'mysql' 
	)
);

$config -> options[ 'cache' ] = array(
	'driver' => 'apc'
);

$config -> options[ 'other' ] = array (
	'log' => false,
	'cache_views' => true,
	'format_date' => 'm.d.y H:i:s'
);

?>
