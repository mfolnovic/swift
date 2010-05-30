<?php

class View extends Base {
	function render( $c = null, $a = null ) {
		if( $c == null ) {
			global $controller;
			$c = $controller -> data[ 'controller' ];
			$a = $controller -> data[ 'action' ];
		}
		
		include VIEWS_DIR . $c . '/' . $a . '.php'; 
	}
}

$view = new View;

?>
