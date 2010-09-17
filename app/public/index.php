<?php

define( 'ENV', 'development' );
define( 'ROOT_DIR', realpath( getcwd() . '/../..' ) . '/' );
define( 'DIR', ROOT_DIR );
define( 'LIB_DIR', ROOT_DIR . 'swift/lib/' );

include LIB_DIR . 'boot.php';

?>
