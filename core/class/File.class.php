<?php

    class File {
        private $dir;
        private $badSymbols;

        public function __construct($dir) {
            $this->dir = $dir;
            $this->badSymbols = Array(".","(",")","{","}","[","]",","," ");
        }

        public function __get($name) {
            if ($handle = opendir($this->dir)) {
                while (false !== ($entry = readdir($handle))) { 
                    $property = str_replace($this->badSymbols, "_", $entry);
                    if ($property == $name) return new File($this->dir . ds . $entry);
                }
            }
            return $this->dir;
        }

        public function __toString() {
            return $this->dir;
        }
		
    }

?>
