<?php

class apcTest extends TestCase {
	function test_setter() {
		$apc = Cache::getInstance( 'apc' );
		$apc -> clear(); // Just to make sure tests run cleanly

		$this -> assert( !$apc -> exists( 'foo' ), "Foo shouldn't exist since I didn't make it anywhere" );
	
		$apc -> set( 'foo', 'bar' );
		$this -> assertEqual( $apc -> get( 'foo' ), 'bar' );
		
		$apc -> delete( 'foo' );
		$this -> assert( !$apc -> exists( 'foo' ), "Foo exists, but it should exist" );
	}
}

?>
