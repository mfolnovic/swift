<?php

class ViewHelpersDatetime extends ViewHelpersCache {
	function format_time( $timestamp ) {
		global $config;
		
		echo date( $config -> options[ 'other' ][ 'format_date' ], $timestamp );
	}
}

?>
