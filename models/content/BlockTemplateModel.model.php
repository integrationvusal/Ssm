<?php

    class BlockTemplateModel extends CRUDModel {
        public $recordTitle;
		public $tplFileName;
        public $description;

        public function  __construct() {
            $this->recordTitle = new ModelTextField("recordTitle", "recordTitle", true, false);
			$this->tplFileName = new ModelTextField("tplFileName", "tplFileName", true, false);
			$this->description = new ModelTextArea("description", "description", true, false);
        }
		
		public static function initialize() {
			self::$title = 'Blocks Templates';
			self::$iconPath = 'default-icon.png';
			self::$displayFields = Array('recordTitle');
		}
		
    }

?>
