<?php

// Mysql driver
class DBDriver extends Base {
	var $conn, $last_query, $numrows;

	function __construct() {
		global $benchmark;
		$benchmark -> start( 'Connecting to database' );

		$this -> conn = @mysql_connect( DB_HOST, DB_USERNAME, DB_PASSWORD ) or die( "Didn't connect to mysql" );
		mysql_select_db( DB_DATABASE );
		
		$benchmark -> end( "Connecting to database" );
	}
	
	function __destruct() {
		mysql_close( $this -> conn );
	}

	function query( $q ) {
		$r = mysql_query( $q, $this -> conn );
		
		$this -> numrows = mysql_num_rows( $r );
		
		return $r;
	}
	
	function allRows( $r ) {
		$ret = array();
		
		while( $row = mysql_fetch_assoc( $r ) )
			$ret[] = $row;
			
		return $ret;
	}
}

?>
