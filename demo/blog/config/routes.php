<?php

$router -> addRoute( "%controller%/%action%/%id%", array( 'controller' => 'blog', 'action' => 'index' ) );
$router -> root( 'blog', 'index' );

?>
