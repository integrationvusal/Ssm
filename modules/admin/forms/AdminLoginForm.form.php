<?php

	class AdminLoginForm extends Form {
		public $email;
		public $password;
		public $savePassword;

		public function __construct() {
			$this->email = new FormTextField('email', FORM_VALIDATION_STRING);
			$this->password = new FormPasswordField('password', FORM_VALIDATION_STRING);
			$this->savePassword = new FormTextField('savePassword', FORM_VALIDATION_NUMERIC, false);
		}
	}

?>