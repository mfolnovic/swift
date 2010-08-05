<?php

class hamlTest extends TestCase {
	function init() {
	}
	
	function parse_template( $name ) {
		View_Haml::getInstance() -> parse( VIEWS_DIR . $name . ".php", TMP_DIR . $name . ".php" );
		return file_get_contents( TMP_DIR . $name . ".php" );
	}
	
	function test_normal_tags_with_ids_and_classes() {
		$this -> assert( $this -> parse_template( "haml/test_normal_tags_with_ids_and_classes" ) == '<h1>Heading 1</h1><h2>Heading 2</h2><div id="container">Div with id container</div><span class="row">Span with class row</span><span class="row even">Span with two classes, row and even</span>' );
	}
	
	function test_nesting() {
		$this -> assertEqual( $this -> parse_template( "haml/test_nesting" ), '<div id="first"><div id="second"></div></div><div id="third"><div id="fourth"><div id="fifth"></div></div><div id="sixth"><div id="seventh"><div id="eighth"></div></div></div><div id="ninth"></div></div>', "HAML parsed it to: {{actual}}, but it should be: {{expected}}" );
	}
};

?>
