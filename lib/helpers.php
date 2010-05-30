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

?>
