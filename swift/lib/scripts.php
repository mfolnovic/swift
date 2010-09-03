<?php

class Scripts extends Base {
	static function call( $arguments ) {
		if( empty( $arguments ) ) { self::help(); return; }

		$name = array_shift( $arguments );
		include LIB_DIR . "scripts/$name.php";
		$className = ucfirst( $name ) . "Script";
		$className::run();
	}
	
	static function help() {
		echo "Available commands:" . PHP_EOL;

		$files = Dir::files( LIB_DIR . "scripts/" );
		foreach( $files as $file )
			if( is_file( LIB_DIR . "scripts/" . $file ) )
				echo "\t" . substr( $file, 0, -4 ) . PHP_EOL;
	}
}

?>
