<?php

// Mysql driver
class DB extends Base {
	var $conn, $last_query, $numrows;

	function safe( $str ) {
		if( !$this -> conn ) $this -> connect();
		if( get_magic_quotes_gpc() ) $str = stripslashes( $string );
		if( !is_numeric( $str ) ) $str = "'" . mysqli_real_escape_string( $this -> conn, $str ) . "'";
		
		return $str;
	}
	
	function connect() {
		global $config;

		$opt = &$config -> options[ 'database' ][ 'default' ];

		$this -> conn = new mysqli( $opt[ 'host' ], $opt[ 'username' ], $opt[ 'password' ], $opt[ 'database' ] );
	}
	
	function __destruct() {
		if( !$this -> conn ) return;
		$this -> conn -> close();
	}

	function query( $q ) {
		if( !$this -> conn ) $this -> connect();
		
		return $this -> conn -> query( $q );
	}
}

$db = new DB;

?>
