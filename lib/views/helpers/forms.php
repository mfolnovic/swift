<?php

class ViewHelpersForms extends ViewHelpersDatetime {
	function form( $url, $options = array() ) {
		return array( "<form action=\"$url\">", "</form>" );
	}
}

?>
