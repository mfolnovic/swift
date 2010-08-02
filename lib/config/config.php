<?php

/**
 * Swift
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

/**
 * Swift Configuration Class
 *
 * This class is used for loading configuration and routes
 *
 * @package			Swift
 * @subpackage	Controller
 * @author			Swift dev team
 * @todo				Implement YAML support
 * @todo				Cache!
 */

class Config extends Base {
	var $options = array();

	/**
	 * Loads configurations and routes
	 * @access	public
	 * @return	void
	 */
	function load() {
		global $router, $config;

		$files = array( 'application', 'routes' );
		foreach( $files as $file )
			include CONFIG_DIR . $file . ".php";
	}
}

$config = new Config;

?>
