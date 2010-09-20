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
	var $controllers = NULL;
	var $models      = array();

	/**
	 * Load all plugins and change load_paths
	 *
	 * @access  public
	 * @return  void
	 */
	public function loadPlugins() {
		$plugins = Dir::dirs(PLUGIN_DIR);

		foreach($plugins as $plugin) {
			$path  = PLUGIN_DIR . '/' . $plugin . '/';
			$types = array('controller' => 'controllers', 'model' => 'models', 'library' => 'libs');

			foreach($types as $type => $subdir) {
				if(is_dir($path . $subdir)) {
					App::$load_paths[$type][] = $path . $subdir;
				}
			}
		}
	}

	/**
	 * Loads plugin $name
	 *
	 * @access public
	 * @param  string $name Plugin name
	 * @return void
	 */
	public function loadPlugin($name) {
		if(strpos('/', $name) === FALSE) {
			$name .= '/' . $name;
		}

		$path = PLUGIN_DIR . str_replace('_', '/', $name) . ".php";

		if(!file_exists($path)) {
			return FALSE;
		}

		include $path;
		
		return TRUE;
	}

	/**
	 * Loads manifest files for all plugins
	 *
	 * @access public
	 * @return void
	 */
	public function loadManifests() {
		$plugins = $this -> pluginList();

		foreach($plugins as $plugin) {
			$manifest =  simplexml_load_file(PLUGIN_DIR . $plugin . '/manifest.xml');
			$extends  =& $this -> extends;

			foreach($manifest -> class as $class) {
				if(!isset($extends[(string)$class -> extends])) {
					$extends[(string)$class -> extends] = array();
				}

				$this -> extends[(string)$class -> extends][] = $class;
			}

			$this -> manifests[(string)$manifest -> name] = $manifest;
		}
	}

	/**
	 * Returns list of extensions of class $class
	 *
	 * @access public
	 * @param  string $class Class for which to return extensions
	 * @return return
	 */
	public function extensions($class) {
		return isset($this -> extends[$class]) ? $this -> extends[$class] : array();
	}

	/**
	 * List of plugins
	 * @access  public
	 * @return  array
	 */
	public function pluginList() {
		return Dir::dirs(PLUGIN_DIR);
	}

	/**
	 * Load list of controllers
	 * @access  public
	 * @return  void
	 */
	public function listControllers() {
		$plugins             = $this -> pluginList();
		$this -> controllers = array();

		foreach($plugins as $plugin) {
			$dir = PLUGIN_DIR . $plugin . '/app/controllers/';
			if(!file_exists($dir)) continue;

			$controllers = Dir::files($dir);

			foreach($controllers as $controller) {
				$this -> controllers[filename($controller)] = $plugin;
			}
		}
	}

	/**
	 * Load controller from plugin
	 * @access  public
	 * @param   string $controller Controller name
	 * @return  string
	 */
	public function loadController($controller) {
		if($this -> controllers === NULL) {
			$this -> listControllers();
		}

		if(!isset($this -> controllers[$controller])) {
			return false;
		}

		$plugin = $this -> controllers[$controller];
		include_once PLUGIN_DIR . "$plugin/app/controllers/$controller.php";

		return $plugin;
	}
}

?>
