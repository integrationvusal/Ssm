<?php

	class FormPasswordField extends FormField  {
		
		public $templateName = 'forms/fields/passwordfield.tpl';
	
        public function __construct($name, $validation = 0, $required = true) {
            parent::__construct($name, $validation, $required);
        }
		
		public function getView(&$smarty) {
			$smarty->assign('name',$this->name);
			$smarty->assign('title',$this->title);
			$out = $smarty->fetch($this->templateName);
			$smarty->clearAssign('name');
			$smarty->clearAssign('title');
			return $out;
		}
		
		public function getFromPost() {
            return md5(parent::getFromPost());
        }
    }

?>