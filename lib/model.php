<?php

include_once LIB_DIR . "model/query.php";
include_once LIB_DIR . "model/validations.php";
include_once LIB_DIR . "model/callbacks.php";
include_once LIB_DIR . "model/relation.php";

class Model extends ModelRelation {
	var $name;
	var $cache = array();
	
	function __construct( $data = NULL ) {
		if( is_array( $data ) )
			foreach( $data as $id => $value )
				$this -> $id = $value;
		
		$this -> name = strtolower( get_called_class() );
		$this -> relation = array( 'where' => array(), 'order' => '', 'select' => '*', 'limit' => array(), 'group' => '', 'having' => '' );
		
		$this -> init();
	}
}

?>
