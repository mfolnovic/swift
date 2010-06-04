<?php

// Load config files
// TODO: make it yaml
$files = array( "application" );
foreach( $files as $file )
	if( file_exists( CONFIG_DIR . $file . ".php" ) )
		include CONFIG_DIR . $file . ".php";
	else
		die( "Didn't find config $file.php. Probably it exists at $file.php.default." );

?>
