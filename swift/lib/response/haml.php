<?php

/**
 * Swift
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

/**
 * Swift Response Class - HAML parser
 *
 * This class is responsible for parsing haml files
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Response
 * @todo       Support blocks, e.g. partial cache
 */

class Response_Haml extends Base {
	/**
	 * All HTML tags which don't need closing tag
	 */
	var $ommitCloseTag = array("br" => 1, "input" => 2, "link" => 3, "meta" => 4, 'col' => 5, 'td' => 6, 'tr' => 7, 'th' => 8, 'hr' => 9, "li" => 10, 'area' => 11);
	/**
	 * PHP control structures
	 * Used internally for putting brackets automatically
	 */
	var $structures = array("foreach", "for", "if", "else");
	/**
	 * Current line parsing
	 */
	var $line;
	/**
	 * Parsed until now
	 */
	var $parsed;
	/**
	 * Contains array of all tags which should be closed on specific depth
	*/
	var $tree;
	/**
	 * Is PHP tag open?
	 */
	var $phpOpen = false;

	/**
	 * Parses file $from, and writes to $to
	 *
	 * @access public
	 * @param  string $from Haml file to parse
	 * @param  string $to   Output
	 * @return void
	 */
	public function parse($from, $to) {
		Dir::make_dir($to);

		if(!file_exists($from)) {
			throw new ResponseException("Template $from doesn't exist!");
		}

		$content        = explode("\n", file_get_contents($from));
		$this -> parsed = '';
		$this -> tree   = array();

		foreach($content as $line) {
			$line = ' ' . $line;
			$this -> line =& $line;
			$this -> parseLine();
		}

		while(!empty($this -> tree)) {
			$curr            = array_shift($this -> tree);
			$this -> parsed .= $curr[1];
		}

		file_put_contents($to, $this -> parsed);
	}

	/**
	 * Parses current line
	 *
	 * @access public
	 * @return void
	 */
	public function parseLine() {
		$ret  =  '';
		$line =& $this -> line; // for easier typing
//		$line =  ' ' . substr($line, 0, -1); // remove newline
		$size =  strlen($line);
		$data =  array('tag' => '', 'attributes' => array(), 'html' => '');

		if($size == 1 || $line[1] == '#') {
			return;
		}

		/**
		 * count tabs
		 * @todo Strpos?
		 */
		for($tabs = 1; $tabs < $size && $line[$tabs] == "\t"; ++ $tabs);

		while(!empty($this -> tree) && $tabs <= $this -> tree[0][0]) {
			$curr            = array_shift($this -> tree);
			$this -> parsed .= $curr[1];
		}

		if(substr($line, $tabs, 3) == '!!!') {
			$this -> parsed .= "<!DOCTYPE html>";
			return;
		}

		if($tabs == $size) {
			return;
		}

		if($line[$tabs] == '-' || $line[$tabs] == '=') {
			$rest            = substr($line, $tabs + 1);
			$command         = trim(substr($rest, 0, strpos($rest, '(')));
			$structure       = in_array($command, $this -> structures);
			$this -> parsed .= "<?php " 
			                .  ($line[$tabs] == '=' ? 'echo ' : '')
			                .  ltrim($rest)
			                .  ($structure ? " { " : ";")
			                .  " ?>";

			if($structure) {
				array_unshift($this -> tree, array($tabs, "<?php } ?>"));
			}

			if(!empty($command) && function_exists('_' . $command . 'End')) {
				array_unshift($this -> tree, array($tabs, '<?php echo _' . $command . 'End(); ?>'));
			}

			return;
		}

		$pos = $tabs;
		// parse tag
		if($line[$pos] == '%' || $line[$pos] == '#' || $line[$pos] == '.') {
			$data['tag'] = 'div'; // default tag
			$type        = '';
			$str         = '';

			for($pos = $tabs; $pos < $size; ++ $pos) {
				$symbol = $line[$pos] == '%' || $line[$pos] == '#' || $line[$pos] == '.' || $line[$pos] == ' ';

				if($line[$pos] == '\\') {
					++ $pos; $symbol = false;
				}

				if(!$symbol) {
					$str .= $line[$pos];
				}

				if($symbol || $pos + 1 == $size) {
					if($type != '') {
						if($type == '%') {
							$data['tag'] = $str;
						} else if($type == '#') {
							$this -> pushValue($data['attributes'], 'id', $str);
						} else if($type == '.') {
							$this -> pushValue($data['attributes'], 'class', $str);
						}
					} else {
						$data['html'] .= $str;
					}

					$type = $line[$pos];
					$str  = '';

					if($line[$pos] == ' ') { 
						$pos ++;
						break;
					}
				}
			}
		}

		// parse attributes
		$attributesStart = strpos($line, "{", $pos);
		$attributesEnd   = strpos($line, "}", $pos);

		if($attributesStart !== FALSE && $attributesEnd !== FALSE) {
			$attributes  = trim(substr($line, $attributesStart + 1, $attributesEnd - $attributesStart - 1));
			$pos        += $attributesEnd - $attributesStart + 1;
			$status      = 1;

			for($i = 0, $attributesLen = strlen($attributes); $i < $attributesLen; ++ $i) {
				/** 
				 * @todo Strpos?
				 */
				for(; $attributes[$i] == ' '; ++ $i);

				if($attributes[$i] == ':' && $status) {
					$end   = strpos($attributes, '=>', $i);
					$index = trim(substr($attributes, $i + 1, $end - $i - 1));

					for($i = $end + 2; $attributes[$i + 1] == ','; ++ $i);
				} else {
					$char      = $attributes[$i];
					$is_string = $char == "'" || $char == '"';

					if($is_string) {
						$start = $i + 1;
						$end   = strpos($attributes, $char, $i + 1);

						if($end === false) $end = $attributesLen;
					} else {
						$start = $i; 
						$cnt   = 0;

						for($end = $i + 1; $end < $attributesLen; ++ $end) {
							if($attributes[$end] == '(') {
								++ $cnt;
							} else if($attributes[$end] == ')') {
								-- $cnt;
							} else if($status && $attributes[$end] == '=' && $attributes[$end + 1] == '>' && $cnt == 0) {
								break;
							} else if(!$status && $attributes[$end] == ',' && $cnt == 0) {
								break;
							}
						}
					}

					$tmp = trim(substr($attributes, $start, $end - $start));

					if(!$is_string) {
						$tmp = "<?php echo $tmp; ?>";
					}

					$i = $end + 1;

					if($status) {
						$index  = $tmp; 
						$i     += 2;
					} else {
						$value = $tmp;
					}

					if(!$status) {
						$this -> pushValue($data['attributes'], $index, $value);
					}
				}

				$status = !$status;
			}
		}

		$data['html'] .= substr($line, $pos);

		if(!empty($data['tag'])) {
			$this -> parsed .= "<{$data['tag']}" . $this -> attributesToHTML($data['attributes']) . ">";
			array_unshift($this -> tree, array($tabs, isset($this -> ommitCloseTag[$data['tag']]) ? "" : "</{$data['tag']}>"));
		}

		$this -> parsed .= $this -> parseHtml($data["html"]);
	}

	/**
	 * Pushes value $value to $data with key $attr
	 * Used internally to push html attributes
	 *
	 * @access public
	 * @param  array  $data  Data array, mostly attributes
	 * @param  string $attr  Key
	 * @param  mixed  $value New value
	 * @return void
	 */
	public function pushValue(&$data, $attr, $value) {
		if(!isset($data[$attr])) {
			$data[$attr] = '';
		}

		if($data[$attr] != '') {
			$data[$attr] .= ' ';
		}

		$data[$attr] .= $value;
	}

	/**
	 * Parses HTML, used only to parse variables and put <?php ?> around those
	 *
	 * @access public
	 * @param  string $string String to parse
	 * @return string
	 * @todo   Optimize!
	 */
	public function parseHtml($string) {
		$ret     = ''; 
		$phpOpen = false;

		for($i = 0, $len = strlen($string); $i < $len; ++ $i) {
			if(substr($string, $i, 5) == "<?php") {
				$phpOpen = true;
			} else if(substr($string, $i, 2) == "?>") {
				$phpOpen = false;
			}
			
			if($string[$i] == '$') {
				preg_match('/[0-9a-zA-Z->_\[\]\' ]+/', $string, $matches, null, $i + 1);

				$str = "echo \${$matches[0]};";
				if(!$phpOpen) {
					$str = "<?php $str ?>";
				}

				$length  = strlen($matches[0]);
				$string  = substr_replace($string, $str, $i, $length + 1);
				$i      += strlen($str) - $length;
			}
		}
		
		return $string;
	}

	/**
	 * Parses array of attributes to HTML
	 *
	 * @access public
	 * @param  string $attributes Attributes to parse
	 * @return return
	 */
	public function attributesToHTML(&$attributes) {
		$ret = '';

		foreach($attributes as $id => $val) {
			$ret .= " $id=\"$val\"";
		}

		return $ret;
	}
}

class ResponseException extends Exception {}

?>
