<?php

include_once LIB_DIR . "model/query.php";
include_once LIB_DIR . "model/validations.php";
include_once LIB_DIR . "model/callbacks.php";

class Model extends ModelCallbacks {
	var $name;
	
	function __construct() {
		$this -> name = strtolower( get_called_class() );
		$this -> relation = array( 'where' => array(), 'order' => '', 'select' => '*', 'limit' => array(), 'group' => '', 'having' => '' );
		
		$this -> init();
	}
	
	function __get( $name ) {
		global $db;
	
		$r = $this -> doQuery();
		
		if( $db -> numrows == 0 ) die( "No rows" );
		
		return $r[ 0 ][ $name ];
	}
}

?>
