<?php

class ControllerBase extends Base {
	var $form;
	var $globals = array();
	
	function __construct() {}

	function initObj() {
		global $controller;	
	
		$this -> form = &$controller -> data;
	}

	function layout( $layout ) {
		global $view;

		$view -> layout = $layout;
	}

	function __get( $index ) {
		return $this -> globals[ $index ];
	}

 	function __set( $index, $value ) {
		$this -> globals[ $index ] = $value;
	}
};

?>
