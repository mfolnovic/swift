<?php

class View extends Base {
	var $layout = 'application';

	function render( $c = null, $a = null ) {
		if( $c == null ) {
			global $controller;
			$c = $controller -> data[ 'controller' ];
			$a = $controller -> data[ 'action' ];
		}
		
		include VIEWS_DIR . $c . '/' . $a . '.php'; 
	}
	
	function renderLayout() {
		include VIEWS_DIR . "layouts/" . ( $this -> layout ) . ".php";
	}
}

$view = new View;

?>
