#!/usr/bin/php
<?php

define( "DIR", getcwd() );
define( "LIB_DIR", "lib/" );

include LIB_DIR . "base.php";
include LIB_DIR . "constants.php";
include LIB_DIR . "dir/dir.php";
include LIB_DIR . "test/testCase.php";
include LIB_DIR . "test/test.php";

$test -> load( "tests/" );

?>
