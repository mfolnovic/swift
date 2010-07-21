<?php

class ModelRow {
	function __construct( $row = array() ) {
		foreach( $row as $index => $value ) 
			$this -> $index = $value;
	}
}

class Model {
	var $tables = array();
	
	function create( $tableName, $newRow = NULL ) {
		if( !isset( $this -> tables[ $tableName ] ) ) {
			include_once MODEL_DIR . $tableName . '.php'; // crashes if I don't put _once :S
			$this -> tables[ $tableName ] = array();
		}
		
		return new $tableName( $tableName, $newRow );
	}
}

$model = new Model;

?>
