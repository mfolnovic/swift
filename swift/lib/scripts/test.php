<?php

class TestScript extends Base {
	static function run() {
		Test_Suite::instance() -> load( ROOT_DIR . "tests/units/" );
	}
}

?>
