<?php

class Plugins extends Base {
	var $extends     = array();
	var $manifest    = array();
	static $instance = NULL;

	/**
	 * Loads plugin $name
	 * @access	public
	 * @param		string	name	Plugin name
	 * @return	void
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
	 * @access	public
	 * @return	void
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
	 * @access	public
	 * @param		string	class	Class for which to return extensions
	 * @return	return
	 */
	function extensions( $class ) {
		return isset( self::instance() -> extends[ $class ] ) ? self::instance() -> extends[ $class ] : array();
	}
}

?>
