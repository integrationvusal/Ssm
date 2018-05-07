<?php

	class FormRadioField extends FormField {
		
		public $templateName = 'forms/fields/radiofield.tpl';
		public $options;
	
        public function __construct($name, $validation = 0, $options, $required = true) {
            parent::__construct($name, $validation, $required);
			$this->options = $options;
        }
		
		public function getView(&$smarty) {
			$smarty->assign('options',$this->options);
			$smarty->assign('name',$this->name);
			$smarty->assign('title',$this->title);
			$out = $smarty->fetch($this->templateName);
			$smarty->clearAssign('options');
			$smarty->clearAssign('name');
			$smarty->clearAssign('title');
			return $out;
		}
    }

?>