<?php

/**
 * Swift
 *
 * @package   Swift
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 */

include APP_DIR . "helpers.php";
include LIB_DIR . "response/helpers.php";

/**
 * Swift Response Class
 *
 * This class is resposible for all rendering
 *
 * @package    Swift
 * @subpackage Response
 * @author     Swift dev team
 */

class Response extends Base {
	var $layout        = 'application';
	var $render        = NULL;
	var $action_caches = array();
	var $output        = '';
	var $view_path     = NULL;
	var $storage       = array();

	/**
	 * Destructor
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		if(!empty(Errors::$errors)) {
			return;
		}

		ob_start();
		echo $this -> output;
		ob_end_flush();
	}

	public function render2($request) {
		$view_id = Benchmark::start();

		if($this -> render === FALSE) {
			return;
		} else if($this -> render === TRUE || !empty($controller) || !empty($action)) {
			if(empty($controller)) $controller = $request -> controller;
			if(empty($action)) $action = $request -> action;

			$path = $controller . '/' . $action . '.php';
		} else {
			$path = $this -> render . '.php';
		}

		if(file_exists($cache)) {
			include $cache;
			return;
		}


		Log::write($path, 'Render', $view_id);
	}

	/**
	 * Outputs and clears all ob buffers
	 *
	 * @access public
	 * @return void
	*/
	public function end() {
		while(ob_get_level() > 0)
			$this -> output .= ob_get_clean();
	}

	public function renderLayout() {
		echo $this -> layout === FALSE ? $this -> storage['default'] : $this -> render('layouts/' . $this -> layout, 'layout');
	}

	public function render($tpl = '', $storage = 'default') {
		if($this -> render === FALSE) {
			return;
		} else if(empty($tpl)){
			$tpl = $this -> render;
		}

		$compiled = TMP_DIR . "views/$tpl.php";
		$template = ($this -> view_path ? $this -> view_path : VIEWS_DIR) . $tpl . '.php';

		if(!file_exists($compiled) || !Config::get('cache_views')) {
			$haml_id = Benchmark::start();
			Response_Haml::instance() -> parse($template, $compiled);
			Log::write($tpl, 'HAML', $haml_id);
		}

		extract(App::$request -> object -> globals);

		ob_start();
		include $compiled;
		return $this -> storage[$storage] = ob_get_clean();
	}
}

?>
