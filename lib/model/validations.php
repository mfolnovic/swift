<?php

class ModelValidations extends ModelQuery {
	var $validations = array();

	function invalid() {
		foreach( $this -> validations as $val ) {
			$field = $val[ 0 ];
			if( preg_match( $val[ 1 ], $this -> $field ) )
				return true;
		}
		
		return false;
	}

	function validates_format_of( $field, $regex, $message = '', $on = '' ) {
		$this -> validations[] = array( $field, $regex, $message, $on );
		
		return $this;
	}
}

?>
