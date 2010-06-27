<?php

// Load config files

class Config extends Base {
	/**
		Array containing all application options
	*/
	var $options = array();

	/**
		Loads application config and routes
	*/
	function load() {
		global $router;
		
		$files = array( "application", "routes" );

		foreach( $files as $file )
			include CONFIG_DIR . $file . ".php";
	}
}

$config = new Config;
?>
