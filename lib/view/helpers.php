<?php

class ViewHelpers {
	function javascript() {
		global $config;
		$ret = '';
		
		foreach( func_get_args() as $val )
			$ret .= "<script type=\"text/javascript\" src=\"/" . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . "public/javascripts/$val\"></script>";
			
		return $ret;
	}	
	
	function stylesheet() {
		global $config;
		$ret = '';
		
		foreach( func_get_args() as $val )
			$ret .= "<link href=\"/" . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . "public/stylesheets/$val\" rel=\"stylesheet\" type=\"text/css\">";
			
		return $ret;
	}
	
	function favicon( $icon ) {
		global $config;
		return '<link rel="icon" href="/' . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . '/images/favicon.ico">';
	}
	
	function image( $image, $options = array() ) {
		global $config;
//		$options = func_get_args();
		$options = $this -> attributes( $options );
		return "<img src=\"/" . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . "public/images/" . $image . "\" $options>";
	}

	function format_time( $timestamp ) {
		global $config;
		
		return date( $config -> options[ 'other' ][ 'format_date' ], $timestamp );
	}

	function form( $url, $options = array() ) {
		global $config;
		return array( "<form action=\"/" . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . "$url\">", "</form>" );
	}

	function link( $title, $href ) {
		global $config;
		return '<a href="/' . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . str_replace( " ", "+", $href ) . '">' . $title . '</a>';
	}

	function partial( $name ) {
		global $view;
		return $view -> render( null, '_' . $name );
	}
	
	function textilize( $str ) {
		echo nl2br( $str );
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
