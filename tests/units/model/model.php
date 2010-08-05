<?php

class modelTest extends TestCase {
	function test_instance() {
		$model = Model::getInstance();
		$this -> assert( true );
	}
}

?>
