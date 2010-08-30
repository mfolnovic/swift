<?php

class modelTest extends TestCase {
	function test_instance() {
		$model = Model::instance();
		$this -> assert( true );
	}
}

?>
