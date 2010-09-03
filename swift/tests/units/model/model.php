<?php

class modelTest extends Test_Case {
	function test_instance() {
		$model = Model::instance();
		$this -> assert( true );
	}
}

?>
