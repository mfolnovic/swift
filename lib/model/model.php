<?php

class ModelRow {
	function __construct( $row ) {
		foreach( $row as $index => $value ) 
			$this -> $index = $value;
	}
}

class Model {
	var $tables = array();
	
	function create( $tableName, $newRow = NULL ) {
		if( !isset( $tables[ $tableName ] ) ) {
			include MODEL_DIR . $tableName . '.php';
			$this -> tables[ $tableName ] = array();
		}
		
		return new $tableName( $tableName, $newRow );
	}
}

$model = new Model;

?>
