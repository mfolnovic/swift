<?php

class ViewHelpers {
	function javascript() {
		global $config;
		$args = func_get_args();
		
		$opt = & $config -> options[ 'other' ];
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
		global $config;

		$opt = & $config -> options[ 'other' ];
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
		global $config;
		return '<link rel="icon" href="/' . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . 'favicon.ico">';
	}
	
	function image( $image, $options = array() ) {
		global $config;
		if( strpos( $image, '/' ) === false ) $image = "images/$image";
//		$options = func_get_args();
		$opt = & $config -> options[ 'other' ];
		$options = $this -> attributes( $options );
		return "<img src=\"/{$opt['url_prefix']}/$image\" $options>";
	}

	function format_time( $timestamp ) {
		global $config;
		
		return date( $config -> options[ 'other' ][ 'format_date' ], $timestamp );
	}

	function form( $url, $options = array() ) {
		global $config;
		return "<form action=\"/" . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . "$url\" " . $this -> attributes( $options ) . ">";
	}
	
	function formEnd() {
		return "</form>";
	}	

	function link( $title, $href, $options = array() ) {
		global $config;
		$options = $this -> attributes( $options );
		return '<a href="/' . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . str_replace( " ", "+", $href ) . '" ' . $options . '>' . $title . '</a>';
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
