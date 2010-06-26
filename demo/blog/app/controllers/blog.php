<?php

class BlogController extends ControllerBase {
	function index() {
		$this -> posts = $this -> model( 'post' ) -> order( 'time', 'desc' );
	}
	
	function show() {
		$this -> post = $this -> model( 'post' ) -> find_by_ID( $this -> data[ "id" ] );
	}
	
	function new_form() { // can't use new
		$this -> post = $this -> model( 'post' );
	}
	
	function create() {
		$post = $this -> model( 'post', $this -> data ) -> save();
	
		$this -> redirect( '/blog/index' );
	}
	
	function edit() {
		$this -> post = $this -> model( 'post' ) -> find_by_ID( $this -> data[ "id" ] );
	}
	
	function update() {
		$this -> model( 'post' ) -> find_by_ID( $this -> data[ "id" ] ) -> values( $this -> data ) -> save();

		$this -> redirect( '/blog/index' );
	}
	
	function delete() {
		$this -> model( 'post' ) -> find_by_ID( $this -> data[ "id" ] ) -> delete();
		
		$this -> redirect( '/blog/index' );
	}
}

?>
