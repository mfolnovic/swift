<?php

class ModelTable {
	var $cache = array();
	
	function __construct( $array = array() ) {
		if( !empty( $array ) ) 
			foreach( $array as $id => $val )
				$this -> $id = $val;
	}
	
	function __get( $id ) {
		return $this -> cache[ $id ];
	}
	
	function __set( $id, $val ) {
		$this -> cache[ $id ] = $val;
		
		return $this;
	}
}

?>
