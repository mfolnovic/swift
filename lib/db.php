<?php

// workaround
include LIB_DIR . "db/" . ( $config -> options[ 'database' ][ 'default' ][ 'driver' ] ) . ".php";
include LIB_DIR . "model/row.php";

class DB extends DBDriver {
	function insert( $table, $rows, $v ) {
		if( empty( $rows ) || empty( $v ) ) die( "Nothing specified" );
		$q = "INSERT INTO $table ( `" . implode( '`,`', $rows ) .'` ) VALUES';
		if( !is_array( $v[ 0 ] ) ) $values = array( 0 => $v );
		else $values = $v;
		
		$first = true;
		foreach( $values as $v ) {
			if( !$first ) $q .= ', '; 
			else $first = false;
			
			$q .= ' (';

			$f = true;
			foreach( $v as $field ) {
				if( !$f ) $q .= ', ';
				else $f = false;
				$q .= $this -> safe( $field );
			}
			
			$q .= ')';
		}
	
		$this -> query( $q );
	}
}

$db = new DB;

?>
