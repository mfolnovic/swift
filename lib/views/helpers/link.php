<?php

class ViewHelpersLinks extends ViewHelpersForms {
	function link( $title, $href ) {
		echo '<a href="' . URL_PREFIX . '/' . str_replace( " ", "+", $href ) . '">' . $title . '</a>';
	}
}

?>
