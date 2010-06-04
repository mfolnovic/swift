<?php

// this is an example for model

class User extends ModelBase {
	// here, you make any relationships, validations etc.
	function init() {
		$this -> validates_format_of( 'username', '/[a-zA-Z]/' );
	}
}

function User( $data = array() ) {
	global $model;
	
	$model -> initTable( 'user' );

	if( !empty( $data ) ) 
		return new ModelRow( $data );
	else
		return new User();
}

?>
