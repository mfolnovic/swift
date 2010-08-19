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
 * @subpackage	Config
 * @author			Swift dev team
 * @todo				Implement YAML support
 * @todo				Cache!
 */

class Config extends Base {
	var $options = array();
	static $instance;

	/**
	 * Loads configurations and routes
	 * @access	public
	 * @return	void
	 */
	function load() {
		global $config;

		$files = array( 'application' );

		foreach( $files as $file )
			include CONFIG_DIR . $file . ".php";
	}

	/**
	 * Singleton
	 * @access	public
	 * @return	object
	 */
	static function instance() {
		if( empty( self::$instance ) ) self::$instance = new Config;
		return self::$instance;
	}
}

$config = new Config;

?>
