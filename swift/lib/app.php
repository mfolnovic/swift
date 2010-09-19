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
 * This class is responsible for loading classes
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage App
 */

class App extends Base {
	/**
	 * Contains paths where each type of class might be
	 */
	static $load_paths = array(
		'library'    => array(LIB_DIR),
		'controller' => array(CONTROLLERS_DIR),
		'model'      => array(MODEL_DIR),
		'vendor'     => array(VENDOR_DIR)
	);

	static $append_names = array(
		'library'    => '',
		'controller' => 'Controller',
		'model'      => '',
		'vendor'     => ''
	);

	/**
	 * Currently, only sets correct locale
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function boot() {
		setlocale(LC_ALL, Config::get('locale'));
	}

	/**
	 * Loads classes specified as argument.
	 * First argument indicates what type of class it needs to load.
	 * Possible types of classes are: library, controller, model, vendor
	 *
	 * @access public
	 * @param  string $type, ...  Type of class to load
	 * @param  string $class, ... Class to load
	 * @return void
	 */
	public static function load() {
		$classes = func_get_args();
		$type    = array_shift($classes);

		foreach($classes as $class) {
			$class_name = $class . self::$append_names[$type];

			if(class_exists($class_name, false)) {
				continue;
			}

			$class_path = strtr(strtolower($class), '_', '/');

			foreach(self::$load_paths[$type] as $directory) {
				$path = $directory . $class_path;

				/**
				 * This allows loading classes in vendors and plugins
				 */
				if(!file_exists($path . '.php') && is_dir($path)) {
					$path .= '/' . $class;
				}

				$path .= '.php';

				if(file_exists($path)) {
					include $path;

					if(method_exists($class_name, 'init')) {
						$class::init();
					}

					break;
				}
			}

			if(!class_exists($class_name, false)) {
			exit;
//				throw new AppException( "Couldn't load class $class!" );
			}
		}
	}
}

class AppException extends Exception {}

?>
