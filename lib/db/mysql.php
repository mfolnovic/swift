<?php

// Mysql driver
class DBDriver extends Base {
	var $conn, $last_query, $numrows;

	function __construct() {
		global $benchmark;
		$benchmark -> start( 'Connecting to database' );

		$this -> conn = @mysql_connect( DB_HOST, DB_USERNAME, DB_PASSWORD ) or die( "Didn't connect to mysql" );
		mysql_select_db( DB_DATABASE ) or die( "Database " . DB_DATABASE . " doesn't exist!" );
		
		$benchmark -> end( "Connecting to database" );
	}
	
	function __destruct() {
		if( !$this -> conn ) return;
		mysql_close( $this -> conn );
	}

	function query( $q ) {
		$r = mysql_query( $q, $this -> conn );
		
		if( is_resource( $r ) ) $this -> numrows = mysql_num_rows( $r );
		else if( $r === FALSE ) die( "Mysql query: " . mysql_error() );
		
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
