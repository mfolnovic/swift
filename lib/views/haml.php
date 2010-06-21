<?php

class Haml {
	function parse( $file ) {
		global $view, $controller;
		
		if( !file_exists( VIEWS_DIR . $file ) ) $controller -> render404();
	
		$f = fopen( VIEWS_DIR . $file, "r" );

		$curr_depth = -1;
		$parsed = array();
		while( $line = fgets( $f ) ) {
			if( $line[ 0 ] == '#' ) continue;
			$depth = $this -> tabs( $line ); // should optimize this
			$parsed[] = array( $depth, $this -> parseLine( $line, $depth ) );
		}
		
		$compiled = $this -> toFile( $parsed );
		$view -> cacheView( $file, $compiled );
	}
	
	function toFile( $parsed ) {
		global $controller;
		$tree = array(); $depth = -1; 
		$depth_offset = 0; // for commands
		$ret = "<?php global \$controller; ?>"; /*, " . ( !empty( $controller -> globals ) ? '$' : '' ) . implode( ', $', array_keys( $controller -> globals ) ) . "; ?>";*/
		
		foreach( $parsed as $line ) {
			$t = "";
			for( $i = 0; $i < $line[ 0 ] - $depth_offset; ++ $i ) $t .= "\t";
			
			$tag = $line[ 1 ];
			
			if( $line[ 0 ] <= $depth ) {
				for( $i = 0; $i <= $depth - $line[ 0 ] && !empty( $tree ); ++ $i ) {
//				for( $i = 0; !empty( $tree ) && $tree[ 0 ][ 1 ] >= $line[ 0 ]; ++ $i ) {
					$curr = array_shift( $tree );
					if( substr( $curr[ 1 ], -2 ) == '?>' ) -- $depth_offset;
					if( $i > 0 ) $ret .= "\n" . $curr[ 0 ];
					$ret .= $curr[ 1 ];
				}
			}
			
			if( isset( $tag[ 'command' ] ) ) {
				$tag[ 'command' ] = trim( $tag[ 'command' ] );
				++ $depth_offset;
				$ret .= "\n" . '<?php ' . $tag[ 'command' ] . " ?>";
				if( substr( $tag[ 'command' ], -1, 1 ) == '{' ) array_unshift( $tree, array( '', '<?php } ?>' ) );
			} else if( isset( $tag[ "tag" ] ) ) {
				$ret .= "\n$t<" . $tag[ "tag" ] . ( $this -> attributesToHTML( $tag[ 'attributes' ] ) ) . ">";
				if( !empty( $tag[ "html" ] ) ) $ret .= $tag[ "html" ][ 0 ] == '$' ? "<?php echo " . $tag[ "html" ] . "; ?>" : $tag[ "html" ];
				array_unshift( $tree, array( $t, '</' . $tag[ "tag" ] . '>' ) );
			} else $ret .= "\n$t" . ( !empty( $tag[ 'html' ] ) && $tag[ "html" ][ 0 ] == '$' ? "<?php echo " . $tag[ "html" ] . "; ?>" : $tag[ "html" ] );
					
			if( !isset( $tag[ 'command' ] ) ) 
				$depth = $line[ 0 ];
		}

		for( $i = 0; !empty( $tree ); ++ $i ) {
			$curr = array_shift( $tree );
			if( $i > 0 ) $ret .= "\n" . $curr[ 0 ];
			$ret .= $curr[ 1 ];
		}
		
		$ret .= "\n"; // final newline
		
		return $ret;
	}

	function parseLine( $line, $start ) {
		$ret = array( 'html' => '' ); $pos = 0;
		$id = ''; $value = '';
		
		$line = substr( $line, 0, -1 ) . " ";
//		$line = preg_replace_callback( '/\$[a-zA-Z.]+/', array( $this, 'parseVar' ), $line );

		for( $i = $start, $len = strlen( $line ); $i < $len; ++ $i ) {
			if( !isset( $ret[ 'tag' ] ) && ( $line[ $i ] == '%' || $line[ $i ] == '.' || $line[ $i ] == '#' ) ) {
				$pos = strpos( $line, ' ', $i );
				$tmp = $this -> parseTag( substr( $line, $i, $pos - $i ) );
				$ret[ 'tag' ] = $tmp[ 'tag' ]; unset( $tmp[ 'tag' ] );
				
				if( !isset( $ret[ 'attributes' ] ) ) $ret[ 'attributes' ] = array();
				$ret[ 'attributes' ] = array_merge( $ret[ 'attributes' ], $tmp );
				$i = $pos - 1;
			} else if( $line[ $i ] == '-' && empty( $ret[ 'tag' ] ) ) {
				return array( 'command' => substr( $line, $i + 1 ) );
				break;
			} else if( $line[ $i ] == ' ' ) {
				if( $id == '' ) { $value .= ' '; continue; }
				$ret[ 'tag' ] = $value;
			} else if( $line[ $i ] == '{' ) {
				$pos = strpos( $line, '}', $i );
				if( !isset( $ret[ 'attributes' ] ) ) $ret[ 'attributes' ] = array();
				$ret[ 'attributes' ] = array_merge( $ret[ 'attributes' ], $this -> parseAttributes( substr( $line, $i, $pos - $i + 1 ) ) );
				$ret[ 'html' ] .= substr( $line, $pos + 1 );
				break;
			} else $value .= $line[ $i ];
		}

		$value = trim( $value );
		if( !empty( $value ) ) $ret[ 'html' ] .= $value;

		return $ret;
	}
	
	function parseTag( $tag ) {
		$ret = array(); $id = 'bla'; $val = '';
		$tag .= '%';
		for( $i = 0, $n = strlen( $tag ); $i < $n; ++ $i ) {
			if( $tag[ $i ] == '#' ) { $ret[ $id ] = $val; $id = 'id'; $val = ''; }
			else if( $tag[ $i ] == '.' ) { $ret[ $id ] = $val; $id = 'class'; $val = ''; }
			else if( $tag[ $i ] == '%' ) { $ret[ $id ] = $val; $id = 'tag'; $val = ''; }
			else $val .= $tag[ $i ];
		}
		
		if( !isset( $ret[ 'tag' ] ) ) $ret[ 'tag' ] = 'div';

		unset( $ret[ "bla" ] );
		return $ret;		
	}
	
	function parseAttributes( $attributes ) {
		return json_decode( $attributes, true );
	}

	function tabs( $str ) {
		for( $i = 0; $str[ $i ] == "\t"; ++ $i ) {
			if( !isset( $str[ $i + 2 ] ) )
				return 0;
		}
		return $i;
	}
	
	function attributesToHTML( $attrs ) {
		$ret = '';
		foreach( $attrs as $id => $val )
			$ret .= " $id=\"$val\"";
		
		return $ret;
	}

	private function isAlpha( $char ) {
		$char = strtolower( $char );
		return $char >= "a" && $char <= "z";
	}
}

$haml = new Haml;

?>
