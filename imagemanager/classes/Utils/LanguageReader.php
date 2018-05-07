<?php
/**
 * LanguageReader.php
 *
 * @package MCFileManager.filesystems
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class handles XML language packs.
 *
 * @package MCFileManager.utils
 */
class LanguageReader {
	var $_items;
	var $_parser;
	var $_curTarget;
	var $_curItem;

	function LanguageReader() {
		$this->_items = array();
		$this->_curTarget = "";
		$this->_curItem = "";
	}

	function loadXML($file) {
		$this->_parser = xml_parser_create("ISO-8859-1");
		xml_set_object($this->_parser, $this);
		xml_set_element_handler($this->_parser, "_saxStartElement", "_saxEndElement");
		xml_set_character_data_handler($this->_parser, "_saxCharacterData");

		if (($fp = fopen($file, "r"))) {
			while ($data = fread($fp, 4096)) {
				if (!xml_parse($this->_parser, $data, feof($fp)))
					die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->_parser)), xml_get_current_line_number($this->_parser)));
			}
		} else
			die("Could not open XML language pack: " . $file);

		xml_parser_free($this->_parser);
	}

	function get($target, $name) {
		return isset($this->_items[$target][$name]) ? $this->_items[$target][$name] : ("$" . $name . "$");
	}

	// * * Private methods

	function _saxStartElement($parser, $name, $attrs) {
		if ($name == "GROUP") {
			$this->_curTarget = $attrs["TARGET"];
			if (!isset($this->_items[$this->_curTarget]))
				$this->_items[$this->_curTarget] = array();
		}

		if ($name == "ITEM")
			$this->_curItem = $attrs["NAME"];
	}

	function _saxEndElement($parser, $name) {
		if ($name == "GROUP")
			$this->_curTarget = "";

		if ($name == "ITEM")
			$this->_curItem = "";
	}

	function _saxCharacterData($parser, $data) {
		if ($this->_curTarget != "" && $this->_curItem != "") {
			if (!isset($this->_items[$this->_curTarget][$this->_curItem]))
				$this->_items[$this->_curTarget][$this->_curItem] = "";

			$this->_items[$this->_curTarget][$this->_curItem] .= $data;
		}
	}
}
?>