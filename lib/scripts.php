<?php

class Scripts extends Base {
	function call( $arguments ) {
		if( empty( $arguments ) ) { $this -> help(); return; }
	
		include LIB_DIR . "scripts/{$arguments[0]}.php";
	}
	
	function help() {
		global $dir;
		
		echo "Available commands:" . PHP_EOL;
		$files = $dir -> files( LIB_DIR . "scripts/" );
		for( $i = 0, $cnt = count( $files ); $i < $cnt; $i += 2 )
			echo "\t" . $files[ $i ] . PHP_EOL;
	}
}

$scripts = new Scripts;

?>
