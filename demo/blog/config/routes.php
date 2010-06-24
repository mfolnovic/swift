<?php

/*$router -> resource( 'vijesti' );
$router -> resource( 'evaluator' );*/

if( isset( $router ) ) {
	$router -> addRoute( "%controller%/(%action%/(%id%))", array( 'controller' => 'vijesti', 'action' => 'pocetna' ) );
	$router -> root( 'vijesti', 'pocetna' );
}

?>
