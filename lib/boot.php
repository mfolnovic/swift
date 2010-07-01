<?php

session_start();

include LIB_DIR . "base.php";
include LIB_DIR . "errors/errors.php";

include LIB_DIR . "constants.php";
include LIB_DIR . "helpers.php";
include LIB_DIR . "dir/dir.php";
include LIB_DIR . "router/router.php";

include LIB_DIR . "config/config.php";
$config -> load();

include LIB_DIR . "log/log.php";
include LIB_DIR . "controller/base.php";
include LIB_DIR . "controller/controller.php";
include LIB_DIR . "cache/" . ( $config -> options[ 'cache' ][ 'driver' ] ) . ".php";
include LIB_DIR . "db/" . ( $config -> options[ 'database' ][ 'default' ][ 'driver' ] ) . ".php";
include LIB_DIR . "ldap/ldap.php";
exit;
include LIB_DIR . "model/model.php";
include LIB_DIR . "view/view.php";

// Route
$router -> route( $_SERVER[ "REQUEST_URI" ] );

// Run controller
$controller -> run();

// Render
$view -> render( 'layouts', $view -> layout );

?>
