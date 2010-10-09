<?php

define('ENV', 'development');
define('ROOT_DIR', realpath(getcwd() . '/../..') . '/');
define('LIB_DIR', ROOT_DIR . 'swift/lib/');

include LIB_DIR . "base.php";
include LIB_DIR . "dir.php";
include LIB_DIR . "constants.php";
include LIB_DIR . "errors.php";
include LIB_DIR . "helpers.php";
include LIB_DIR . 'app.php';

App::boot();

?>
