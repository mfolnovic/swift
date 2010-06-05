<?php

class Haml {
	function parse( $file ) {
		$f = fopen( $file, "r" );

		$curr_depth = -1;
		$parsed = array();
		while( $line = fgets( $f ) ) {
			if( $line[ 0 ] == '#' ) continue;
			$depth = $this -> tabs( $line ); // should optimize this
			$parsed[] = array( $depth, $this -> parseLine( $line, $depth ) );
		}
		
		$this -> toFile( $parsed );
	}
	
	function toFile( $parsed ) {
		$tree = array(); $depth = -1;
		foreach( $parsed as $line ) {
			$t = "";
			for( $i = 0; $i < $line[ 0 ]; ++ $i ) $t .= "\t";
			
			$tag = $line[ 1 ];
			
			if( $line[ 0 ] <= $depth ) {
				for( $i = 0; $i <= $depth - $line[ 0 ] && !empty( $tree ); ++ $i ) {
					$curr = array_shift( $tree );
					if( $i > 0 ) echo "\n" . $curr[ 0 ];
					echo "</" . $curr[ 1 ] . ">";
				}
			}
			if( isset( $tag[ "tag" ] ) ) {
				echo "\n$t<" . $tag[ "tag" ] . ">";
				if( isset( $tag[ "html" ] ) ) echo $tag[ "html" ];
				array_unshift( $tree, array( $t, $tag[ "tag" ] ) );
			}
					
			$depth = $line[ 0 ];
		}
		
		for( $i = 0; !empty( $tree ); ++ $i ) {
			$curr = array_shift( $tree );
			if( $i > 0 ) echo "\n" . $curr[ 0 ];
			echo "</" . $curr[ 1 ] . ">";
		}
	}

	function parseLine( $line, $start ) {
		$ret = array(); $pos = 0;
		$id = ''; $value = '';

		$line = substr( $line, 0, -1 ) . " ";

		for( $i = $start, $len = strlen( $line ); $i < $len; ++ $i ) {
			if( !isset( $ret[ 'tag' ] ) && ( $line[ $i ] == '%' || $line[ $i ] == '.' || $line[ $i ] == '#' ) ) {
				$pos = strpos( $line, ' ', $i );
				$tmp = $this -> parseTag( substr( $line, $i, $pos - $i ) );
				$ret[ 'tag' ] = $tmp[ 'tag' ]; unset( $tmp[ 'tag' ] );

				if( !isset( $ret[ 'attributes' ] ) ) $ret[ 'attributes' ] = array();
				$ret[ 'attributes' ] = array_merge( $ret[ 'attributes' ], $tmp );
				$i = $pos - 1;
			} else if( $line[ $i ] == '-' ) {
				return array( 'command' => substr( $line, $i + 1 ) );
				break;
			} else if( $line[ $i ] == ' ' ) {
				if( $id == '' ) { $value .= ' '; continue; }
				$ret[ 'tag' ] = $value;
			} else if( $line[ $i ] == '{' ) {
				$pos = strpos( $line, '}', $i );
				if( !isset( $ret[ 'attributes' ] ) ) $ret[ 'attributes' ] = array();
				$ret[ 'attributes' ] = array_merge( $ret[ 'attributes' ], $this -> parseattributes( substr( $line, $i, $pos - $i + 1 ) ) );
				$ret[ 'html' ] = substr( $line, $pos + 1 );
				break;
			} else $value .= $line[ $i ];
		}
		
		$value = trim( $value );
		if( !empty( $value ) ) $ret[ 'html' ] = $value;

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
	
	function parseattributes( $attributes ) {
		return json_decode( $attributes, true );
	}

	function tabs( $str ) {
		for( $i = 0; $str[ $i ] == "\t"; ++ $i ) {
			if( !isset( $str[ $i + 2 ] ) )
				return -1;
		}
		return $i;
	}

	private function isAlpha( $char ) {
		$char = strtolower( $char );
		return $char >= "a" && $char <= "z";
	}
}

$haml = new Haml;

?>
