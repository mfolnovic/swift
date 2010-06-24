<?php

include_once LIB_DIR . "model/query.php";
include_once LIB_DIR . "model/validations.php";
include_once LIB_DIR . "model/callbacks.php";
include_once LIB_DIR . "model/relation.php";
include_once LIB_DIR . "model/table.php";
include_once LIB_DIR . "model/tableResult.php";

class ModelBase extends ModelRelation {
	var $name;
	var $currentDataSet = NULL;
	var $newRecord = false;
			
	function __construct( $data = array() ) {
		global $model;
		$this -> name = isset( $this -> tableName ) ? $this -> tableName : strtolower( get_class( $this ) );
		
		$model -> initTable( $this -> name );
		if( !empty( $data ) ) $this -> currentDataSet = new ModelRow( $data );
		
		$this -> newRecord = true;
		
		$this -> relation = array( 'where' => array(), 'order' => '', 'select' => '*', 'limit' => array( 0 => -1 ), 'group' => '', 'having' => '' );
		$this -> init();
		
		return $this;
	}
}

?>
