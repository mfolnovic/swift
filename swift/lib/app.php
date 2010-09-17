<?php

/**
 * Swift
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

/**
 * Swift App Class
 *
 * This class is responsible for loading internal classes
 *
 * @author      Swift dev team
 * @package     Swift
 * @subpackage  App
 */

class App {
	static $load_paths = array(
	                      'library' => array( LIB_DIR ),
	                      'controller' => array( CONTROLLERS_DIR ),
	                      'model' => array( MODEL_DIR ),
	                      'vendor' => array( VENDOR_DIR )
	                     );

	/**
	 * Currently, only sets correct locale
	 *
	 * @access  public
	 * @return  void
	 * @static
	 */
	static function boot() {
		setlocale( LC_ALL, Config::instance() -> get( 'locale' ) );
	}

	/**
	 * Loads classes specified as argument
	 * First argument indicates what to load, library, controller or model
	 *
	 * @access  public
	 * @param   string $type, ...  Type of class to load
	 * @param   string $class, ... Class to load
	 * @return  void
	 */
	static function load() {
		$classes = func_get_args();
		$type    = array_shift( $classes );

		foreach( $classes as $class ) {
			if( class_exists( $class, false ) ) {
				continue;
			}

			$class_path = str_replace( '_', '/', $class );

			foreach( self::$load_paths[ $type ] as $directory ) {
				$path = $directory . $class_path;

				if( !file_exists( $path . '.php' ) && is_dir( $path ) ) {
					$path .= '/' . $class;
				}

				$path .= '.php';

				if( file_exists( $path ) ) {
					include $path;

					if( method_exists( $class, 'init' ) ) {
						$class::init();
					}

					break;
				}
			}

			if( !class_exists( $class, false ) ) {
				trigger_error( "Couldn't load class $class!", ERROR );
			}
		}
	}
}

?>
