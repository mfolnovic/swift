#!/usr/bin/php
<?php

define( "LIB_DIR", "lib/" );

include LIB_DIR . "base.php";
include LIB_DIR . "constants.php";
include LIB_DIR . "dir.php";
include LIB_DIR . "testCase.php";
include LIB_DIR . "test.php";

$test -> load( "tests/" );

?>
