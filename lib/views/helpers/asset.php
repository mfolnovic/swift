<?php

class ViewHelpersAsset extends Base {
	function javascript() {
		$ret = '';
		
		foreach( func_get_args() as $val )
			$ret .= "<script type=\"text/javascript\" src=\"" . URL_PREFIX . "/public/javascripts/$val\"></script>\n";
			
		echo $ret; // but would like to use return instead
	}	
	
	function stylesheet() {
		$ret = '';
		
		foreach( func_get_args() as $val )
			$ret .= "<link href=\"" . URL_PREFIX . "/public/stylesheets/$val\" rel=\"stylesheet\" type=\"text/css\">\n";
			
		echo $ret;
	}
	
	function image() {
		$options = func_get_args();
		echo "<img src=\"" . URL_PREFIX . "/public/images/" . $options[ 0 ] . "\">";
	}
}

?>
