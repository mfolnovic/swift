<?php

class table1Test extends Test_Case {
	var $time;

	function setup() {
		$this -> time = time();
		$model = model( 'table1' );
		$model -> recreateTable();

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
		$this -> assertEqual( $row -> time, new Model_Type_Timestamp( $this -> time ) );

		$row = model( 'table1' ) -> find_by_id( 2 );
		$this -> assertEqual( $row -> id, 2 );
		$this -> assertEqual( $row -> number, 1 );
		$this -> assertEqual( $row -> string, 'bar' );
		$this -> assertEqual( $row -> time, new Model_Type_Timestamp( $this -> time + 10 ) );
	}

	function test_insert() {
		model( 'table1' ) -> values( array( 'number' => 3, 'string' => 'hello' ) ) -> save();
		$row = model( 'table1' ) -> find_by_string( 'hello' );

		$this -> assertEqual( $row -> id, 3 );
		$this -> assertEqual( $row -> number, 3 );
		$this -> assertEqual( $row -> string, 'hello' );
		$this -> assertEqual( $row -> time, new Model_Type_Timestamp() );
	}

	function test_update() {
		$row = model( 'table1' ) -> find_by_id( 1 ) -> values( array( 'number' => 4 ) );

		$this -> assertEqual( $row -> number, 4 );
		$row -> save();

		$row = model( 'table1' ) -> find_by_id( 1 );
		$this -> assertEqual( $row -> number, 4 );
		$this -> assertEqual( $row -> string, 'foo' );
	}

	function test_delete() {
		model( 'table1' ) -> find_by_id( 1 ) -> delete();
		$row = model( 'table1' ) -> find_by_id( 1 ) -> first();

		$this -> assertEmpty( $row );
	}
}

?>
