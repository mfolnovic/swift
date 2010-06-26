<?php

// Load config files

class Config extends Base {
	var $options = array();

	function loadConfig() {
		global $router;
		
		$files = array( "application", "routes" );

		foreach( $files as $file )
			include CONFIG_DIR . $file . ".php";
			
	}
}

$config = new Config;
?>
