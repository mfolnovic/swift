<?php

class Table1 extends Model_Base {
	var $tableName = 'table1';

	var $validations = array();

	var $schema = array (
		'id' => array( 'type' => 'int', 'size' => 11, 'auto_increment' => true ),
		'number' => array( 'type' => 'int', 'size' => 11 ),
		'string' => array( 'type' => 'varchar', 'size' => 50 ),
		'time' => array( 'type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP' )
	);
	
	var $schema_keys = array( 
		'id' => 'primary'
	);
}

?>
