<?php

	class LoginForm extends Form {

		public $login;
		public $password;
		public $remember;

		public static $enableCSRFSecurity = false;
		public static $hasCaptcha = false;


		public function __construct() {
			$this->login = new FormTextField('login', FORM_VALIDATION_STRING);
			$this->password = new FormTextField('password', FORM_VALIDATION_STRING);
			$this->password->minLength = 2;
			$this->remember = new FormCheckBoxField("remember", 0, array(), false);
		}

	}

?>