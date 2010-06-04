<?php

class Haml {
	function parse( $file ) {
		$f = fopen( $file, "r" );

		$curr_depth = -1;
		while( $line = fgets( $f ) ) {
			$depth = $this -> tabs( $line ); // should optimize this
			print_r( $this -> parseLine( $line, $depth ) ); echo "<br>";
		}
	}

	function parseLine( $line, $start ) {
		$ret = array(); $pos = 0;
		$id = ''; $value = '';

	 	echo $line."<br>";

		$line .= ' ';

		for( $i = $start, $len = strlen( $line ); $i < $len; ++ $i ) {
			if( $line[ $i ] == '%' ) {
				$id = 'tag';
				$value = '';
			} else if( $line[ $i ] == '-' ) {
				return array();
				break;
			} else if( $line[ $i ] == ' ' ) {
				if( $id == '' ) continue;
				$ret[ $id ] = $value;
				$value = ''; $id = '';
			} else if( $line[ $i ] == '{' ) {
				$options = true;
				$option_id = false;
				$option_val = false;
			} else if( $line[ $i ] == '}' ) {
				$options = false;
			} else if( $line[ $i ] == ',' ) {

			} else if( $line[ $i ] == '"' ) {
				$option_val 
			} else if( $this -> isAlpha( $line[ $i ] ) ) {
	
			} else $value .= $line[ $i ];
		}

		return $ret;
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
