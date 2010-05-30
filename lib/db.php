<?php

// workaround
include LIB_DIR . "db/" . DB_DRIVER . ".php";

class DB extends DBDriver {
	function safe( $str ) {
		if( get_magic_quotes_gpc() ) $str = stripslashes( $string );
		if( !is_numeric( $str ) ) $str = "'" . mysql_real_escape_string( $str ) . "'";
		
		return $str;

	}
}

$db = new DB;

?>
