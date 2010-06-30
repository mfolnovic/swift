<?php

function array_print( $array, $t = '' ) {
	foreach( $array as $id => $val ) {
		if( is_array( $val ) ) {
			echo $t . $id . ": [ <br>";
			array_print( $val, $t . '&nbsp;&nbsp;' );
			echo $t . "] <br>";
		} else
			echo $t . $id . ": " . $val . "<br>";
	}
}

	
/** 
 * Tests if ajax is active
*/
function isAjax() {
	return isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) === 'xmlhttprequest';
}

?>
