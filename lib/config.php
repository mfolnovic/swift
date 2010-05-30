<?php

// Load config files
// TODO: make it yaml
$files = array( "application" );
foreach( $files as $file )
	include CONFIG_DIR . $file . ".php";

?>
