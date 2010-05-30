<?php

class ControllerBase extends Base {
	var $data;
	
	function __construct() {

	}

	function initObj() {
		global $controller;	
	
		$this -> data = &$controller -> data;
	}
};

?>
