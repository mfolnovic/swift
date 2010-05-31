<?php

include_once LIB_DIR . "model/query.php";
include_once LIB_DIR . "model/validations.php";
include_once LIB_DIR . "model/callbacks.php";
include_once LIB_DIR . "model/relation.php";

class Model extends ModelRelation {
	var $name;
	
	function __construct() {
		$this -> name = strtolower( get_called_class() );
		$this -> relation = array( 'where' => array(), 'order' => '', 'select' => '*', 'limit' => array(), 'group' => '', 'having' => '' );
		
		$this -> init();
	}
}

?>
