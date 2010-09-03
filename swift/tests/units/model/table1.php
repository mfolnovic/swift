<?php

class table1Test extends Test_Case {
	var $time = 1283515031;

	function setup() {
		$model = model( 'table1' );
		$model -> recreateTable();
	}
	
	function test_insert() {
		$array = array(
			array( 'number' => 2, 'string' => 'foo', 'time' => $this -> time ),
			array( 'number' => 1, 'string' => 'bar', 'time' => $this -> time + 10 )
		);
		
		foreach( $array as $row ) {
			$current = model( 'table1' ) -> values( $row ) -> save();
		}
	}
	
	function test_select() {
		$row = model( 'table1' ) -> where( array( 'id' => 1 ) );
		
		$this -> assertEqual( $row -> id, 1 );
		$this -> assertEqual( $row -> number, 2 );
		$this -> assertEqual( $row -> string, 'foo' );
		$this -> assertEqual( $row -> time, $this -> time );
		
		$row = model( 'table1' ) -> find_by_id( 2 );
		$this -> assertEqual( $row -> id, 2 );
		$this -> assertEqual( $row -> number, 1 );
		$this -> assertEqual( $row -> string, 'bar' );
		$this -> assertEqual( $row -> time, $this -> time + 10 );
	}
}

?>
