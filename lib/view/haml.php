<?php

/**
 * Swift
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

/**
 * Swift View Class - HAML parser
 *
 * This class is responsible for parsing haml files
 *
 * @package			Swift
 * @subpackage	View
 * @author			Swift dev team
 * @todo				Support blocks, e.g. partial cache
 */

class View_Haml {
	var $ommitCloseTag = array( "br" => 1, "input" => 2, "link" => 3, "meta" => 4, 'colgroup' => 5, 'td' => 6, 'tr' => 7, 'th' => 8, 'hr' => 9, "li" => 10 );
	var $structures = array( "foreach", "if", "else" );
	var $line;
	var $parsed;
	var $tree;
	static $instance = NULL;

	/**
	 * Parses file $from, and writes to $to
	 * @access	public
	 * @param		string	from	Haml file to parse
	 * @param		string	to		Output
	 * @return	void
	 */
	function parse( $from, $to ) {
		if( !file_exists( dirname( $to ) ) )
			if( @mkdir( dirname( $to ) ) === false )
				trigger_error( "chmod 777 -R tmp/ in directory where your site is!" );

		if( !file_exists( $from ) )
			die( "Template doesn't exist!" );

		$fileFrom = fopen( $from, "r" );
		$fileTo = fopen( $to, "w" );
		$this -> parsed = '';
		$this -> tree = array();

		while( $this -> line = fgets( $fileFrom ) )
			$this -> parseLine();

		while( !empty( $this -> tree ) ) {
			$curr = array_shift( $this -> tree );
			$this -> parsed .= $curr[ 1 ];
		}

		fwrite( $fileTo, $this -> parsed );

		fclose( $fileFrom );
		fclose( $fileTo );
	}

	/**
	 * Parses current line
	 * @access	public
	 * @return	void
	 */
	function parseLine() {
		$ret = '';
		$line = & $this -> line; // for easier typing
		$line = ' ' . substr( $line, 0, -1 ); // remove newline
		$size = strlen( $line );
		$data = array( 'tag' => '', 'attributes' => array(), 'html' => '' );

		if( $size == 1 ) return;
		if( $line[ 1 ] == '#' ) return;

		// count tabs
		for( $tabs = 1; $tabs < $size && $line[ $tabs ] == "\t"; ++ $tabs );
		while( !empty( $this -> tree ) && $tabs <= $this -> tree[ 0 ][ 0 ] ) {
			$curr = array_shift( $this -> tree );
			$this -> parsed .= $curr[ 1 ];
		}

		if( substr( $line, $tabs, 3 ) == '!!!' ) {
			$this -> parsed .= "<!DOCTYPE html>";
			return;
		}

		if( $tabs == $size ) return;

		if( $line[ $tabs ] == '-' ) {
			$rest = trim( substr( $line, $tabs + 1 ) );
			$command = substr( $rest, 0, strpos( $rest, '(' ) );
			$structure = in_array( $command, $this -> structures );
			$this -> parsed .= "<?php " . $rest . ( $structure ? " { " : ";" ) . " ?>";
			if( $structure ) array_unshift( $this -> tree, array( $tabs, "<?php } ?>" ) );
			if( method_exists( 'View', $command . 'End' ) ) array_unshift( $this -> tree, array( $tabs, '<?php echo $this -> ' . $command . 'End(); ?>' ) );
			return;
		}

		$pos = $tabs;
		// parse tag
		if( $line[ $pos ] == '%' || $line[ $pos ] == '#' || $line[ $pos ] == '.' ) {
			$data[ 'tag' ] = 'div'; // default tag
			$type = ''; $str = '';
			for( $pos = $tabs; $pos < $size; ++ $pos ) {
				$symbol = $line[ $pos ] == '%' || $line[ $pos ] == '#' || $line[ $pos ] == '.' || $line[ $pos ] == ' ';
				if( $line[ $pos ] == '\\' ) { ++ $pos; $symbol = false; }
				if( !$symbol ) $str .= $line[ $pos ]; 
				if( $symbol || $pos + 1 == $size ) {
					if( $type != '' ) {
						if( $type == '%' ) $data[ 'tag' ] = $str;
						else if( $type == '#' ) $this -> pushValue( $data[ 'attributes' ], 'id', $str );
						else if( $type == '.' ) $this -> pushValue( $data[ 'attributes' ], 'class', $str );
					} else $data[ 'html' ] .= $str;

					$type = $line[ $pos ];
					$str = '';

					if( $line[ $pos ] == ' ' ) { $pos ++; break; }
				}
			}
		}

		// parse attributes
		$attributesStart = strpos( $line, "{", $pos );
		$attributesEnd = strpos( $line, "}", $pos );

		if( $attributesStart !== FALSE && $attributesEnd !== FALSE ) {
			$attributes = trim( substr( $line, $attributesStart + 1, $attributesEnd - $attributesStart - 1 ) );
			$pos += $attributesEnd - $attributesStart + 1;

			$status = 1;
			for( $i = 0, $attributesLen = strlen( $attributes ); $i < $attributesLen; ++ $i ) {
				for( ; $attributes[ $i ] == ' '; ++ $i );
				if( $attributes[ $i ] == ':' && $status ) {
					$end = strpos( $attributes, '=>', $i );
					$index = trim( substr( $attributes, $i + 1, $end - $i - 1 ) );
					for( $i = $end + 2; $attributes[ $i + 1 ] == ','; ++ $i );
				} else {
					$char = $attributes[ $i ];
					$is_string = $char == "'" || $char == '"';
					if( $is_string ) {
						$start = $i + 1;
						$end = strpos( $attributes, $char, $i + 1 );
						if( $end === false ) $end = $attributesLen;
					} else {
						$start = $i; $cnt = 0;
						for( $end = $i + 1; $end < $attributesLen; ++ $end ) {
							if( $attributes[ $end ] == '(' ) ++ $cnt;
							else if( $attributes[ $end ] == ')' ) -- $cnt;
							else if( $status && $attributes[ $end ] == '=' && $attributes[ $end + 1 ] == '>' && $cnt == 0 ) break;
							else if( !$status && $attributes[ $end ] == ',' && $cnt == 0 ) break;
						}
					}

					$tmp = trim( substr( $attributes, $start, $end - $start ) );
					if( !$is_string ) $tmp = "<?php echo $tmp; ?>";
					$i = $end + 1;

					if( $status ) { $index = $tmp; $i += 2; }
					else $value = $tmp;

					if( !$status ) $this -> pushValue( $data[ 'attributes' ], $index, $value );
				}

				$status = !$status;
			}
		}
		$data[ 'html' ] .= trim( substr( $line, $pos ) );
		if( !empty( $data[ 'tag' ] ) ) {
			$this -> parsed .= "<{$data[ 'tag' ] }" . $this -> attributesToHTML( $data[ 'attributes' ] ) . ">";
			array_unshift( $this -> tree, array( $tabs, isset( $this -> ommitCloseTag[ $data[ 'tag' ] ] ) ? "" : "</{$data[ 'tag' ]}>" ) );
		}
		$this -> parsed .= $this -> parseHtml( $data[ "html" ] );
	}

	/**
	 * Pushes value $value to $data with key $attr
	 * Used internally to push html attributes
	 * @access	public
	 * @param		array		data	Data array, mostly attributes
	 * @param		string	attr	Key
	 * @param		mixed		value	New value
	 * @return	void
	 */
	function pushValue( &$data, $attr, $value ) {
		if( !isset( $data[ $attr ] ) ) $data[ $attr ] = '';
		if( $data[ $attr ] != '' ) $data[ $attr ] .= ' ';
		$data[ $attr ] .= $value;
	}

	/**
	 * Parses HTML, used only to parse variables and put <?php ?> around those
	 * @access	public
	 * @param		string	string	String to parse
	 * @return	string
	 */
	function parseHtml( $string ) {
		$ret = ''; $phpOpen = false;
		for( $i = 0, $len = strlen( $string ); $i < $len; ++ $i ) {
			if( substr( $string, $i, 5 ) == "<?php" ) $phpOpen = true;
			else if( substr( $string, $i, 2 ) == "?>" ) $phpOpen = false;
			
			if( $string[ $i ] == '$' ) {
				preg_match( '/[a-zA-Z->_\[\]\' ]+/', $string, $matches, null, $i + 1 );
				$str = "echo \${$matches[0]};";
				if( !$phpOpen ) $str = "<?php $str ?>";
				$length = strlen( $matches[ 0 ] );
				$string = substr_replace( $string, $str, $i, $length + 1 );
				$i += strlen( $str ) - $length;
			}
		}
		
		return $string;
	}

	/**
	 * Parses array of attributes to HTML
	 * @access	public
	 * @param		string	attributes	Attributes to parse
	 * @return	return
	 */
	function attributesToHTML( &$attributes ) {
		$ret = '';

		foreach( $attributes as $id => $val )
			$ret .= " $id=\"$val\"";

		return $ret;
	}

	/**
	 * Singleton
	 * @access	public
	 * @return	object
	 */
	static function getInstance() {
		if( empty( self::$instance ) ) self::$instance = new View_Haml;
		return self::$instance;
	}
}

?>
