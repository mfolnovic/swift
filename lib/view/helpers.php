<?php

class ViewHelpers {
	function javascript() {
		global $config;
		$ret = '';
		
		foreach( func_get_args() as $val )
			$ret .= "<script type=\"text/javascript\" src=\"/" . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . "public/javascripts/$val\"></script>\n";
			
		return $ret; // but would like to use return instead
	}	
	
	function stylesheet() {
		global $config;
		$ret = '';
		
		foreach( func_get_args() as $val )
			$ret .= "<link href=\"/" . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . "public/stylesheets/$val\" rel=\"stylesheet\" type=\"text/css\">\n";
			
		return $ret;
	}
	
	function image( $image ) {
		global $config;
//		$options = func_get_args();
		return "<img src=\"/" . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . "public/images/" . $image . "\">";
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
}

?>
