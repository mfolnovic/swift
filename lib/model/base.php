<?php

include_once LIB_DIR . "model/query.php";
include_once LIB_DIR . "model/validations.php";
include_once LIB_DIR . "model/callbacks.php";
include_once LIB_DIR . "model/relation.php";
include_once LIB_DIR . "model/table.php";

class ModelBase extends ModelRelation {
	var $name;
	var $cache = array();
	
	function __construct() {
		global $model;

		$this -> name = strtolower( get_class( $this ) );
	
		$this -> relation = array( 'where' => array(), 'order' => '', 'select' => '*', 'limit' => array(), 'group' => '', 'having' => '' );
		$this -> init();
		
		return $this;
	}
}

?>
