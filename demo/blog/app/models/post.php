<?php

class Post extends Model_Base {
	var $tableName = 'posts';
	var $schema = array (
		'id' => array( 'type' => 'int', 'size' => 11, 'auto_increment' => true ),
		'title' => array( 'type' => 'varchar', 'size' => 50 ),
		'content' => array( 'type' => 'text' ),
		'time' => array( 'type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP' )
	);
	var $schema_keys = array( 
		'id' => 'primary'
	);
	
	var $dropAndCreateTable = false;
}

?>
