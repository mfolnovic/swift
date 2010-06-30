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
		
		$files = array( 'application', 'routes' );
		foreach( $files as $file )
			include CONFIG_DIR . $file . ".php";
		
/*		$this -> options = apc_fetch( 'config_' . DIR );
		if( $this -> options === false ) {
			include CONFIG_DIR . "applicaton.php";
		
			if( $this -> options[ 'other' ][ 'cache_config' ] )
				apc_store( 'config_' . DIR, $this -> options );
		}
		
		include CONFIG_DIR . "routes.php";*/
	}
}

$config = new Config;
?>
