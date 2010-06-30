<?php

class BlogController extends ApplicationController {
	function __construct() {

	}

	function index() {
		$this -> posts = $this -> model( 'post' ) -> order( 'time', 'desc' );
	}
	
	function show() {
		$this -> post = $this -> model( 'post' ) -> find_by_id( $this -> data[ "id" ] );
	}
	
	function new_form() { // can't use new
		$this -> post = $this -> model( 'post' );
	}
	
	function create() {
		$post = $this -> model( 'post', $this -> data[ 'post' ] ) -> save();
	
		$this -> redirect( '/blog/index' );
	}
	
	function edit() {
		$this -> post = $this -> model( 'post' ) -> find_by_id( $this -> data[ "id" ] );
	}
	
	function update() {
		$this -> post = $this -> model( 'post' ) -> find_by_id( $this -> data[ "id" ] ) -> values( $this -> data[ 'post' ] ) -> save();

		if( empty( $this -> post -> errors ) )
			$this -> redirect( 'blog/show/' . $this -> data[ "id" ] );
		else 
			$this -> render( 'blog/edit' );
	}
	
	function delete() {
		$this -> model( 'post' ) -> find_by_id( $this -> data[ "id" ] ) -> delete();
		
		$this -> redirect( '/blog/index' );
	}
}

?>
