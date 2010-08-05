<?php

class TestScript extends Base {
	static function run() {
		include LIB_DIR . "test/test.php";
		include LIB_DIR . "test/testCase.php";
		
		TestSuite::getInstance() -> load( ROOT_DIR . "/tests/units/" );
	}
}

?>
