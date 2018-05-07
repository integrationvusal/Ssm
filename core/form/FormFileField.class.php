<?php

	class FormFileField {
        
        public $name;
        public $required;
        public $uploadFolder;
        public $allowedExtensions;
        public $maxFileSize;
        
        public function __construct($name, $uploadFolder, $allowedExtensions = Array(), $maxFileSize = 2000000, $required = true) {
            $this->name = $name;
            $this->uploadFolder = $uploadFolder;
            $this->required = $required;
            $this->allowedExtensions = $allowedExtensions;
            $this->maxFileSize = $maxFileSize;
        }
        
        public function getFromPost() {
            if (isset($_FILES[$this->name]) && ($_FILES[$this->name]["error"] == 0)) {
                $fileExtension = strrchr($_FILES[$this->name]["name"] ,'.');
                if (in_array($fileExtension, $this->allowedExtensions)) {
                    if ($_FILES[$this->name]["size"] <= $this->maxFileSize) {
                        $fileName = $this->generateFileName($_FILES[$this->name]["name"]);
                        $this->upload($_FILES[$this->name]["tmp_name"], $fileName);
                        return $fileName;
                    } else return false;
                } else return false;
            } else if ($this->required) return false;
            return "";
        }
        
        protected function generateFileName($inputFileName) {
            $fileName = md5(date('Y-m-d H:i:s') . rand(1,10000));
            $fileDest = "";
            for ($i = 0; $i < 3; $i++) {
                $fileDest = $fileDest . ds . substr($fileName,$i,1);
                if (!is_dir($this->uploadFolder . $fileDest)) mkdir($this->uploadFolder . $fileDest);
            }
            $fileName = $fileDest . ds . $fileName . strrchr($inputFileName,'.');
            return $fileName;
        }
        
        protected function upload($from, $to) {
            move_uploaded_file($from, $this->uploadFolder . $to);
        }
    }

?>