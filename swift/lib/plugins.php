<?php

/**
 * Swift
 *
 * @package   Swift
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 */

/**
 * Swift Plugins Class
 *
 * This class handles all plugins
 *
 * @package    Swift
 * @subpackage Plugins
 * @author     Swift dev team
 */

class Plugins extends Base {
	/**
	 * List of all classes which extended other class
	*/
	var $extends     = array();
	/**
	 * Parset .manifest files
	 */
	var $manifest    = array();

	/**
	 * Loads plugin $name
	 *
	 * @access public
	 * @param  string $name Plugin name
	 * @return void
	 */
	function loadPlugin( $name ) {
		if( strpos( '/', $name ) === FALSE ) $name .= '/' . $name;
		$path = PLUGIN_DIR . str_replace( '_', '/', $name ) . ".php";

		if( !file_exists( $path ) ) return FALSE;

		include $path;
		
		return TRUE;
	}

	/**
	 * Loads manifest files for all plugins
	 *
	 * @access public
	 * @return void
	 * @todo   Cache
	 */
	function loadManifests() {
		$plugins = Dir::dirs( PLUGIN_DIR );
		foreach( $plugins as $plugin ) {
			$manifest = simplexml_load_file( PLUGIN_DIR . $plugin . '/manifest.xml' );
			$extends  =& $this -> extends;

			foreach( $manifest -> class as $class ) {
				if( !isset( $extends[ (string)$class -> extends ] ) ) $extends[ (string)$class -> extends ] = array();
				$this -> extends[ (string)$class -> extends ][] = $class;
			}

			$this -> manifests[ (string)$manifest -> name ] = $manifest;
		}
	}

	/**
	 * Returns list of extensions of class $class
	 *
	 * @access public
	 * @param  string $class Class for which to return extensions
	 * @return return
	 */
	function extensions( $class ) {
		return isset( $this -> extends[ $class ] ) ? $this -> extends[ $class ] : array();
	}
}

?>
