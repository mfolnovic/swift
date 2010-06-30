<?php

class ViewHelpers {
	function javascript() {
		$ret = '';
		
		foreach( func_get_args() as $val )
			$ret .= "<script type=\"text/javascript\" src=\"/" . URL_PREFIX . "public/javascripts/$val\"></script>\n";
			
		return $ret; // but would like to use return instead
	}	
	
	function stylesheet() {
		$ret = '';
		
		foreach( func_get_args() as $val )
			$ret .= "<link href=\"/" . URL_PREFIX . "public/stylesheets/$val\" rel=\"stylesheet\" type=\"text/css\">\n";
			
		return $ret;
	}
	
	function image( $image ) {
//		$options = func_get_args();
		return "<img src=\"/" . URL_PREFIX . "public/images/" . $image . "\">";
	}

	function format_time( $timestamp ) {
		global $config;
		
		return date( $config -> options[ 'other' ][ 'format_date' ], $timestamp );
	}

	function form( $url, $options = array() ) {
		return array( "<form action=\"/" . URL_PREFIX . "$url\">", "</form>" );
	}

	function link( $title, $href ) {
		return '<a href="/' . URL_PREFIX . str_replace( " ", "+", $href ) . '">' . $title . '</a>';
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
