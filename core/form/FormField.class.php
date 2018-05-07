<?php

	class FormField {
        public $name;
        public $validation;
        public $required;
		public $templateName = 'test';
		public $title;
		public $minLength;
		
        
        public function __construct($name, $validation = 0, $required = true, $minLength = 1) {
            $this->name = $name;
            $this->validation = $validation;
            $this->required = $required;
			$this->minLength = $minLength;
        }
        
		public function getView(&$smarty) {
			return $this->templateName;
		}
		
        public function getFromPost() {
            if (isset($_POST[$this->name])) {
                if (is_array($_POST[$this->name])) {
                    $out = ",";
                    $count = count($_POST[$this->name]);
                    for($i = 0; $i < $count; $i++) {
                        $isValid = $this->isValid($_POST[$this->name][$i]);
                        if ($isValid === false) return false;
                        else $out .= $_POST[$this->name][$i] . ",";
                    }
                    return $out;
                } else {
					if (trim($_POST[$this->name]) == '') {
						if (!$this->required) return "";
						return false;
					}
					return $this->isValid($_POST[$this->name]);
				}
            } else if ($this->required) return false;
            return "";
        }
        
        private function isValid($val) {
			if (strlen($val) < $this->minLength) return false;
			if (!is_numeric($this->validation)) return preg_match($this->validation, $val);
            switch ($this->validation) {
                case 0:
                    return $val;
                case 1:
                    return $this->validationNumeric($val);
                case 2:
                    return $this->validationEmail($val);
                case 3:
                    return $this->validationString($val);
                case 4:
                    return $this->validationDate($val);
            }
            return false;
        }
        
        private function validationNumeric($val) {
            if (is_numeric($val)) return intval($val);
            else return false;
        }
        
        private function validationEmail($val) {
            if (filter_var($val, FILTER_VALIDATE_EMAIL)) return $val;
            else return false;
        }
        
        private function validationString($val) {
            if (!empty($val)) return Security::filterSql(Security::filterString($val), BaseModel::$mysqli);
            else return false;
        }
        
        private function validationDate($val) {
            if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/im", $val)) return $val;
            else return false;
        }
        
    }

?>