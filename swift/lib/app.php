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

	static $extends = array();
	static $request;
	static $response;

	/**
	 * Currently, only sets correct locale
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function boot() {
		self::load('library', 'config', 'router', 'view', 'security');
		spl_autoload_register('App::load');

		setlocale(LC_ALL, Config::get('locale'));

		self::loadPlugins();

		Security::instance();
		self::$request = new Request($_GET['url']);
		self::$response = new Response();

		self::$request -> route();

		if(self::$request -> code != 200) {
			ob_clean();
			include PUBLIC_DIR . "/" . self::$request -> code . ".html";
			exit;
		}

		if(empty(self::$response -> render)) {
			self::$response -> render = self::$request -> controller . '/' . self::$request -> action;
		}

		self::$response -> render();
		self::$response -> renderLayout();
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

		if(in_array($classes[0], array_keys(self::$load_paths))) {
			$type    = array_shift($classes);
		} else {
			$type = 'library';
		}

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
				throw new AppException( "Couldn't load class $class!" );
			}
		}
	}

	/**
	 * Load all plugins and change load_paths
	 *
	 * @access  public
	 * @return  void
	 */
	public static function loadPlugins() {
		$plugins = Dir::dirs(PLUGIN_DIR);

		foreach($plugins as $plugin) {
			$path  = PLUGIN_DIR . '/' . $plugin . '/';
			$types = array('controller' => 'controllers', 'model' => 'models', 'library' => 'libs');

			foreach($types as $type => $subdir) {
				$subpath = $path . $subdir . '/';
				if(is_dir($subpath)) {
					self::$load_paths[$type][] = $subpath;

					if($type == 'library') {
						$files = Dir::files($subpath);

						foreach($files as $file) {
							include $subpath . $file;
							$class_name = ucfirst(filename($file));
							foreach($class_name::$extends as $extend) {
								self::$extends[$extend] = $class_name;
							}
						}
					}
				}
			}
		}
	}
}

class AppException extends Exception {}

?>
