<?php

class Dir {
	function files( $dir ) {
		$ret = array();
		$dir = scandir( $dir );

		foreach( $dir as $a )
			if( $a[ 0 ] != '.' && $a[ strlen( $a ) - 1 ] != '/' )
				$ret[] = $a;

		return $ret;
		//return array_slice( scandir( $dir ), 2 );
	}
	
	function read( $dir, $file ) {
		return file_get_contents( $dir . '/' . $file );
	}
}

$dir = new Dir;

?>
