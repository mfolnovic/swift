<?php

class Yaml {
	static function parse( $path ) {
		return yaml_parse_file( $path );
	}
}

?>
