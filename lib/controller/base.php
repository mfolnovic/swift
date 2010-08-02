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
 * Swift Controller Class - Base
 *
 * This class gives methods in all application's controllers
 *
 * @package	Swift
 * @author	Swift dev team
 */

class Controller_Base extends Base {
	var $data;
	var $config;
	var $globals = array();
	var $controllerName = "";

	/**
	 * Constructor
	 * @access	public
	 * @return	void
	 */
	function __construct() {
		global $config, $controller;

		$this -> config =& $config -> options;
		$this -> controllerName = strtolower( substr( get_class( $this ), 0, -10 ) );
		$this -> current_time = time();
		
		parent::__construct();
	}

	/**
	 * Used for changing layout
	 * @access	public
	 * @param		string	$layout	Name of new layout
	 * @return	void
	 */
	function layout( $layout ) {
		View::getInstance() -> layout = $layout;
	}

	/**
	 * Redirects to $url
	 * @access	public
	 * @param		string	$url	Url to redirect to
	 * @return 	void
	*/
	function redirect( $url ) {
		global $router, $config;

		if( !isAjax() ) header( "Location:/{$config -> options[ 'other' ][ 'url_prefix' ]}$url" );
		else {
			header( "X-Redirect: " . ( isAjax() ? '' : $config -> options[ 'other' ][ 'url_prefix' ] ) . "$url" );
			$this -> data = array();
			$router -> route( $url, false );
		}
	}

	/**
	 * Changes what it should be rendered
	 * @access	public
	 * @param		string	$url	Path which should be rendered
	 * @return	return
	 */
	function render( $path ) {
		global $router;

		if( $path === false ) View::getInstance() -> render = false;
		else $router -> route( $path, false );
	}

	/**
	 * Returns instance of model $name, and can also create new row from data $data
	 * @access	public
	 * @param	string	$name	Name of model
	 * @param	array		$data	New row
	 * @return object
	*/
	function model( $name, $data = array() ) {
		return Model::getInstance() -> create( $name, $data );
	}

	/**
	 * Tells router that it missed action and should continue finding right one
	 * @access	public
	 * @return	void
	 */
	function notFound() {
		global $router;
		$router -> continueRouting = true;
	}

	/**
	 * Caches actions specified as arguments
	 * @access	public
	 * @param		string	$action,...	Names of actions to cache
	 * @return	return
	 */
	function caches_action() {
		$view = View::getInstance();
		foreach( func_get_args() as $action )
			$view -> action_caches[] = array( &$this -> controllerName, $action );
	}

	function &__get( $index ) {
		return $this -> globals[ $index ];
	}

	function __set( $index, $value ) {
		$this -> globals[ $index ] = $value;
	}

	function __isset( $index ) {
		return isset( $this -> globals[ $index ] );
	}
};

?>
