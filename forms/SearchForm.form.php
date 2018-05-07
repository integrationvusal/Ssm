<?php

	class SearchForm extends Form {
	
		public $searchText;
	
		public static $enableCSRFSecurity = true;
	
		public function __construct() {
			$this->searchText = new FormTextField('searchText', 0, FORM_VALIDATION_STRING);
		}
	
	}

?>