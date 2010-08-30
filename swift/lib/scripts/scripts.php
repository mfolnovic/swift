<?php

class Scripts extends Base {
	function call( $arguments ) {
		if( empty( $arguments ) ) { $this -> help(); return; }
	
		$name = array_shift( $arguments );
		include LIB_DIR . "scripts/$name.php";
		$className = ucfirst( $name ) . "Script";
		$className::run();
	}
	
	function help() {
		global $dir;
		
		echo "Available commands:" . PHP_EOL;
		$files = Dir::files( LIB_DIR . "scripts/" );
		for( $i = 0, $cnt = count( $files ); $i < $cnt; ++ $i )
			if( !is_dir( LIB_DIR . "scripts/" . $files[ $i ] ) && $files[ $i ] != 'scripts.php' )
				echo "\t" . substr( $files[ $i ], 0, -4 ) . PHP_EOL;
	}
}

$scripts = new Scripts;

?>
