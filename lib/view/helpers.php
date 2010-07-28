<?php

class ViewHelpers extends ApplicationHelpers {
	function javascript() {
		$args = func_get_args();
		
		$opt =& $this -> config[ 'other' ];
		$version = !isset( $opt[ 'static_version' ] ) || $opt[ 'static_version' ] === false ? '' : '.' . $opt[ 'static_version' ];
		
		if( $version != '' && file_exists( PUBLIC_DIR . 'javascripts/all.js' ) )
			return "<script type=\"text/javascript\" src=\"/{$opt[ 'url_prefix' ]}all$version.js\"></script>";
		else {
			$ret = '';
			foreach( $args as $val )
				$ret .= "<script type=\"text/javascript\" src=\"/{$opt[ 'url_prefix' ]}$val\"></script>";
			return $ret;
		}
	}	
	
	function stylesheet() {
		$opt =& $this -> config[ 'other' ];
		$version = !isset( $opt[ 'static_version' ] ) || $opt[ 'static_version' ] === false ? '' : '.' . $opt[ 'static_version' ];
		
		if( $version != '' && file_exists( PUBLIC_DIR . 'stylesheets/all.css' ) )
			return "<link href=\"/{$opt[ 'url_prefix' ]}all$version.css\" rel=\"stylesheet\" type=\"text/css\">";
		else {
			$ret = '';
		
			foreach( func_get_args() as $val )
				$ret .= "<link href=\"/{$opt[ 'url_prefix' ]}$val\" rel=\"stylesheet\" type=\"text/css\">";
			
			return $ret;
		}
	}
	
	function favicon( $icon ) {
		return '<link rel="icon" href="/' . ( $this -> config[ 'other' ][ 'url_prefix' ] ) . 'favicon.ico">';
	}
	
	function image( $image, $options = array() ) {
		if( strpos( $image, '/' ) === false ) $image = "images/$image";
		$options = $this -> attributes( $options );
		return "<img src=\"/{$this -> config[ 'other' ][ 'url_prefix' ]}/$image\" $options>";
	}

	function format_time( $timestamp ) {
		return date( $this -> config[ 'other' ][ 'format_date' ], $timestamp );
	}

	function form( $url, $options = array() ) {
		return "<form action=\"/" . ( $this -> config[ 'other' ][ 'url_prefix' ] ) . "$url\" " . $this -> attributes( $options ) . ">";
	}
	
	function formEnd() {
		return "</form>";
	}	

	function link( $title, $href, $options = array() ) {
		$options = $this -> attributes( $options );
		return '<a href="/' . ( $this -> config[ 'other' ][ 'url_prefix' ] ) . str_replace( " ", "+", $href ) . '" ' . $options . '>' . $title . '</a>';
	}

	function partial( $name ) {
		global $view;
		return $view -> render( null, '_' . $name );
	}
	
	function xss_clean( $string ) {
		return htmlentities( $string, ENT_QUOTES, 'utf-8' );
	}
	
	protected function attributes( $array ) {
		$ret = '';
		foreach( $array as $id => $val ) {
			if( $ret != '' ) $ret .= ' ';
			$ret .= "$id=\"$val\"";
		}
		
		return $ret;
	}
}

?>
