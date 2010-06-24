<?php

// Mysql driver
class DBDriver extends Base {
	var $conn, $last_query, $numrows;

	function safe( $str ) {
		if( !$this -> conn ) $this -> connect();
		if( get_magic_quotes_gpc() ) $str = stripslashes( $string );
		if( !is_numeric( $str ) ) $str = "'" . mysql_real_escape_string( $str, $this -> conn ) . "'";
		
		return $str;
	}
	
	function connect() {
		global $benchmark, $config;
//		$benchmark -> start( 'Connecting to database' );
		
		$opt = $config -> options[ 'database' ][ 'default' ];

		$this -> conn = @mysql_connect( $opt[ 'host' ], $opt[ 'username' ], $opt[ 'password' ] ) or die( "Didn't connect to mysql" );
		mysql_select_db( $opt[ 'database' ] ) or die( "Database " . $opt[ 'database' ] . " doesn't exist!" );
		
//		$benchmark -> end( "Connecting to database" );
	}
	
	function __destruct() {
		if( !$this -> conn ) return;
		mysql_close( $this -> conn );
	}

	function query( $q ) {
		if( !$this -> conn ) $this -> connect();
		$r = mysql_query( $q, $this -> conn );
		if( is_resource( $r ) ) $this -> numrows = mysql_num_rows( $r );
		else if( $r === FALSE ) die( "Mysql query: " . mysql_error() );
		
		return $r;
	}
	
	function fetchRow( $r ) {
		return mysql_fetch_assoc( $r );
	}
}

?>
