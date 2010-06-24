<?php

class Haml {
	var $ommitCloseTag = array( "br", "input", "link", "meta" );
	var $structures = array( "foreach", "if", "else" );
	var $currentLine = '';

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
		$tree = array();
		$depth_offset = 0; // for commands
		$ret = ""; 
		/*"<?php global \$controller; ?>"; /*, " . ( !empty( $controller -> globals ) ? '$' : '' ) . implode( ', $', array_keys( $controller -> globals ) ) . "; ?>";*/
		
		foreach( $parsed as $line ) {
			$t = "";
			for( $i = 0; $i < $line[ 0 ]; ++ $i ) $t .= "\t";
			
			$tag = $line[ 1 ];
			
			for( $i = 0; !empty( $tree ) && $line[ 0 ] <= $tree[ 0 ][ 2 ]; ++ $i ) {
				$curr = array_shift( $tree );
				if( substr( $curr[ 1 ], -2 ) == '?>' ) -- $depth_offset;
				if( $i > 0 ) $ret .= "\n" . $curr[ 0 ];
				$ret .= $curr[ 1 ];
			}
						
			if( isset( $tag[ 'command' ] ) ) {
				$tag[ 'command' ] = trim( $tag[ 'command' ] );
				$name = substr( $tag[ 'command' ], 0, strpos( $tag[ 'command' ], ' ' ) );
//				if( in_array( $name, $this -> structures ) ) $tag[ 'command' ] .= ' {';
				++ $depth_offset;
				$ret .= "\n$t" . ( $this -> parseCommand( $tag[ 'command' ] ) );
				if( substr( $tag[ 'command' ], -1, 1 ) == '{' ) array_unshift( $tree, array( '', '<?php } ?>', $line[ 0 ] ) );
			} else if( isset( $tag[ "tag" ] ) ) {
				$ret .= "\n$t<" . $tag[ "tag" ] . ( $this -> attributesToHTML( $tag[ 'attributes' ] ) ) . ">";
				if( !empty( $tag[ "html" ] ) ) $ret .= $tag[ "html" ];
				array_unshift( $tree, array( $t, in_array( $tag[ "tag" ], $this -> ommitCloseTag ) ? '' : '</' . $tag[ "tag" ] . '>', $line[ 0 ] ) );
			} else $ret .= "\n$t" . $tag[ "html" ];
		}

		for( $i = 0; !empty( $tree ); ++ $i ) {
			$curr = array_shift( $tree );
			if( $i > 0 && count( $curr[ 0 ] ) > 0 ) $ret .= "\n" . $curr[ 0 ];
			$ret .= $curr[ 1 ];
		}
		
		$ret .= "\n"; // final newline
		
		return $ret;
	}

	function parseLine( $line, $start ) {
		$ret = array( 'html' => '' ); $pos = 0;
		$id = ''; $value = '';
		$this -> currentLine = & $line;

		$line = substr( $line, $start, -1 ) . " ";
		$line = preg_replace_callback( '/\$[a-zA-Z->\[\]\' (]+/', array( $this, 'parseVar' ), $line );

		for( $i = 0, $len = strlen( $line ); $i < $len; ++ $i ) {
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
			} else if( $line[ $i ] == '(' ) {
				$pos = strpos( $line, ')', $i );
				if( !isset( $ret[ 'attributes' ] ) ) $ret[ 'attributes' ] = array();
				$ret[ 'attributes' ] = array_merge( $ret[ 'attributes' ], $this -> parseAttributes( substr( $line, $i + 1, $pos - $i - 1 ) ) );
				$ret[ 'html' ] .= trim( substr( $line, $pos + 1 ) );
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
		$attributes = explode( ',', $attributes );
		
		$ret = array();
		foreach( $attributes as $val ) {
			list( $id, $value ) = explode( ':', $val );
			$id = trim( $id );
			$value = trim( $value );
			
			if( !isset( $ret[ $id ] ) ) $ret[ $id ] = '';
			
			$ret[ $id ] .= ( empty( $ret[ $id ] ) ? '' : ' ' ) . ( $value[ 0 ] == "'" ? substr( $value, 1, -1 ) : $value );
		}
		
		return $ret;
	}
	
	function parseVar( $matches ) {
		$str = trim( $matches[ 0 ] );
		if( substr( $str, -1 ) == '(' ) return $str;
		else return ( $this -> currentLine[ 0 ] == '-' ) ? $str : "<?php echo $str; ?>";
	}

	function parseCommand( $code ) {
		$name = trim( substr( $code, 0, strpos( $code, '(' ) ) );
		if( is_callable( array( 'View', $name ) ) ) {
			if( strpos( $code, '$' ) !== false ) return '<?php echo $this -> ' . $code . '; ?>';
			else return eval( 'global $view; return $view -> ' . $code .';' );
		} else return '<?php ' . $code . ';?>';
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
