<?php

class TempObject {
	var $data = array();
	
	function __construct( $data ) {
		$this -> data = $data;
	}
}

class apcTest extends TestCase {
	function test_clear() {
		$apc = Cache::factory( 'apc' );
		$apc -> clear(); // Just to make sure tests run cleanly

		$this -> assert( !$apc -> exists( 'foo' ), "Foo shouldn't exist since I didn't make it anywhere" );
	}

	function test_set() {
		$apc = Cache::factory( 'apc' );

		$this -> assert( !$apc -> exists( 'foo' ), "Foo shouldn't exist." );

		$apc -> set( 'foo', 'bar' );
		$this -> assertEqual( $apc -> get( 'foo' ), 'bar' );
		$this -> assert( $apc -> exists( 'foo' ), "Foo should exist." );
		
		$array = array( 1 => 'hello', '2' => 'world' );
		$apc -> set( 'foo2', $array );
		$this -> assertEqual( $apc -> get( 'foo2' ), $array );
		$this -> assert( $apc -> exists( 'foo2' ), "Foo2 should exist." );
		
		$array2 = array( new TempObject( $array ), new TempObject( array_reverse( $array ) ) );
		$apc -> set( 'foo3', $array2 );
		$this -> assertEqual( $apc -> get( 'foo3' ), $array2 );
		$this -> assert( $apc -> exists( 'foo3' ), "Foo3 should exist" );
		
	}
	
	function test_delete() {
		$apc = Cache::factory( 'apc' );
	
		$apc -> delete( 'foo' );
		$this -> assert( !$apc -> exists( 'foo' ), "Foo exists, but it should exist" );
	}
}

?>
