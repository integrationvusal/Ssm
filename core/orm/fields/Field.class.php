<?php

    abstract class ModelField {
        
        public $multiLang;
        public $required;
        public $title;
        public $name;
        public $regExp;
		public $common;
		public $hidden;
		public $tplFile;
		public $htmlCss;
		
		// db settings
		public $dbType;
		public $allowNull = true;
		public $defaultValue = '0';
		public $index = false;
		public $dbLength = 50;
		// db settings end
		
		public $value;
        
        public function __construct($name, $title, $required, $multiLang, $common = false, $regExp = "/^.*/im") {
            $this->multiLang = $multiLang;
            $this->required = $required;
            $this->title = $title;
            $this->name = $name;
            $this->regExp = $regExp;
			$this->common = $common;
			$this->hidden = false;
        }

        public function getName($lang = false) {
			
            $name = $this->name;
            if ($this->multiLang && !$this->common) {
                $name .= '['. $lang .']';
            }
            return $name;
        }

		public function getDisplayValue() {
			return $this->getValue();
		}
		
        public function getSqlData($lang = false) {
            $value = $this->checkValueFromPost($lang);
            if ($value["success"]) {
				$value['value'] = addslashes($value['value']);
				$this->setValue($value['value']);
                return Array(
                    "success" => true,
                    "name" => $value["name"],
                    "data" => $value["value"],
                );
            } else {
                return Array(
                    "success" => false,
					"name" => $value["name"],
                );
            }
        }
        
		public function setValue($value) {
			$this->value = $value;
		}
		
		public function getValue() {
			return $this->value;
		}
		
        private function validateValue($val) {
            if (preg_match($this->regExp, $val)) return $val;
            else return false;
        }
        
		
        protected function getValueFromPost($lang = false) {
            if ($this->multiLang) {
				if ($this->common) {
					if (isset($_POST[$this->name])) return $_POST[$this->name];
				} else {
					if (isset($_POST[$this->name][$lang])) return $_POST[$this->name][$lang];
				}
            } else if (isset($_POST[$this->name])) return $_POST[$this->name];
        }
        
        protected function checkValueFromPost($lang = false) {
            
            $value = $this->getValueFromPost($lang);
            
            $error = Array(
                "success" => false,
                "name" => $this->name,
            );
            
            $success = Array(
                "success" => true,
                "name" => $this->name,
            );

            if (is_array($value) && (count($value) == 0) && $this->required) return $error;
            else if ((!is_array($value)) && (trim($value) == '') && $this->required) return $error;
			
            if (is_array($value)) {
                $out = Array();
                foreach ($value as $v) {
					if (trim($v) != "") $out[] = $this->validateValue($v);
				}
				
                $success["value"] = "," . join(",", $out) . ",";
                return $success;
            } else {
                $success["value"] = $this->validateValue($value);
                return $success;
            }
        }

		protected function  generateHTML($templateFile, $value = false, $lang = false, $keyValue = Array(), $defaultValue = 0) {

            $data = Array(
				'title' => $this->title,
				'name' => $this->getName($lang),
				'keyValue' => $keyValue,
				'defaultValue' => $defaultValue,
				'lang' => $lang,
				'elementId' => 'element-' . rand(10000,99999),
				'htmlCss' => $this->htmlCss,
			);
			
			if (is_array($value)) {
				$c = count($value);
				for ($i = 0; $i < $c; $i++) {
					if (trim($value[$i]) == "") unset($value[$i]);
				}
			}
			
			if ($this->multiLang) $data['lang'] = $lang;
			$data['value'] = $value;
			
			return Array(
                'file' => $templateFile,
                'data' => $data,
            );
        }
    }

	require_once 'TextField.class.php';
	require_once 'PasswordField.class.php';
	require_once 'IntegerField.class.php';
	require_once 'BooleanField.class.php';
	require_once 'RadioField.class.php';
	require_once 'SelectField.class.php';
	require_once 'CheckboxField.class.php';
	require_once 'DateField.class.php';
	require_once 'FileField.class.php';
	require_once 'FMFileField.class.php';
	require_once 'ImageField.class.php';
	require_once 'FMImageField.class.php';
	require_once 'Textarea.class.php';
	require_once 'Tinymce.class.php';
	
?>
