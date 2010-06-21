<?php

// Load config files

class Config extends Base {
	var $options = array();

	function __construct() {
		global $router;
	
		$files = array( "application" );
		
		if( !is_callable( 'yaml_parse_file' ) ) die( "Get PECL extension YAML (http://pecl.php.net/package/yaml)" );
		
		foreach( $files as $file )
			if( file_exists( CONFIG_DIR . $file . ".php" ) )
				$this -> options = array_merge( $this -> options, yaml_parse_file( CONFIG_DIR . $file . ".php" ) );
			else
				die( "Didn't find config $file.php. Probably it exists at $file.php.default." );
				
		include CONFIG_DIR . "routes.php";
	}
}

$config = new Config;
?>
