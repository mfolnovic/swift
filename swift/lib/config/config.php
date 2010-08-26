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

	/**
	 * Loads configurations and routes
	 * @access	public
	 * @return	void
	 */
	function load() {
		$files = array( 'application' );

		foreach( $files as $file ) {
			$path = $dir = CONFIG_DIR . $file;
			if( file_exists( $path . '.yml' ) )
				$this -> options = array_merge( $this -> options, Yaml::parse( $path = $path . '.yml' ) );
			else if( file_exists( $path . '.php' ) )
				include $path . ".php";
		}
	}

	/**
	 * Gets passed indexes from configuration
	 * @access	public
	 * @param		mixed	index1, ...	Indexes
	 * @return	return
	 */
	function get() {
		$curr =& $this -> options;

		foreach( func_get_args() as $index )
			$curr =& $curr[ $index ];

		return $curr;
	}
}

$config = new Config;

?>
