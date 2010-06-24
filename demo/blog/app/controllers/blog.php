<?php

class BlogController extends ControllerBase {
	function index() {
		$this -> posts = $this -> model( 'post' );
	}
}

?>
