<?php

include "views/haml.php";

class View extends Base {
	var $layout = 'application';

	function render( $c = null, $a = null ) {
		global $haml;
	
		if( $c == null ) {
			global $controller;
			$c = $controller -> controller;
			$a = $controller -> action;
		}
			
		$haml -> parse( VIEWS_DIR . $c . '/' . $a . '.php' ) ; exit;
	}
	
	function renderLayout() {
		include VIEWS_DIR . "layouts/" . ( $this -> layout ) . ".php";
	}
}

$view = new View;

?>
