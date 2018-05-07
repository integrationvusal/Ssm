<?php

	class FormImageField extends FormFileField {
        public $resizeOptions = Array();
        public $deleteOriginal;
        
        public function __construct($name, $uploadFolder, $allowedExtensions = Array(), $maxFileSize = 2000000, $required = true, $deleteOriginal = false) {
            parent::__construct($name, $uploadFolder, $allowedExtensions, $maxFileSize, $required);
            $this->deleteOriginal = $deleteOriginal;
        }
        
        protected function generateFileName($inputFileName) {
            $fileName = md5(date('Y-m-d H:i:s') . rand(1,10000));
            $fileDest = "";
            for ($i = 0; $i < 3; $i++) {
                $fileDest = $fileDest . ds . substr($fileName,$i,1);
                $count = count($this->resizeOptions);
                for ($j = 0; $j < $count; $j++) {
                    if (!is_dir($this->uploadFolder . $this->resizeOptions[$j][0] . $fileDest)) {
                        mkdir($this->uploadFolder . ds . $this->resizeOptions[$j][0] . $fileDest);
                        chmod($this->uploadFolder . ds . $this->resizeOptions[$j][0] . $fileDest, 777);
                    }
                }
                if (!is_dir($this->uploadFolder . $fileDest)) mkdir($this->uploadFolder . $fileDest);
            }
            return $fileDest . ds . $fileName . strrchr($inputFileName,'.');
        }
        
        protected function upload($from, $to) {
            require_once app_root . ds . "phpthumb" . ds . "ThumbLib.inc.php";
            $tmpUploadFile = $this->uploadFolder . $to;
            move_uploaded_file($from, $tmpUploadFile);
            $count = count($this->resizeOptions);
            for ($i = 0; $i < $count; $i++) {
                $phpthumb = PhpThumbFactory::create($tmpUploadFile);
                $phpthumb->resize($this->resizeOptions[$i][1], $this->resizeOptions[$i][2]);
                $phpthumb->save($this->uploadFolder . ds . $this->resizeOptions[$i][0] . $to);
                unset($phpthumb);
            }
            if ($this->deleteOriginal) unlink($tmpUploadFile); 
        }
    }

?>