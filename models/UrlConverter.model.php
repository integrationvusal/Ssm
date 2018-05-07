<?php

    class UrlConverter extends CRUDModel {
        public $recordTitle;
        public $urlIn;
		public $patternIndex;

        public function  __construct() {
            $this->recordTitle = new ModelTextField("recordTitle", "Title", true, false);
			$this->urlIn = new ModelTextField("urlIn", "URL from", true, false);
			$this->patternIndex = new ModelIntegerField("patternIndex", "Pattern index", true, false);
        }
		
		public static function initialize() {
			self::$title = 'URL Converter';
			self::$iconPath = 'default-icon.png';
			self::$multiLang = false;
			self::$searchable = false;
			self::$displayFields = Array('recordTitle','urlIn','patternIndex');
		}
		
    }

?>
