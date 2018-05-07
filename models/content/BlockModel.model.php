<?php

    class BlockModel extends CRUDModel {
		public $contentModelId;
		public $blockTemplateId;
		public $deleted;

        public function  __construct() {
            $this->contentModelId = new ModelIntegerField("contentModelId", "contentModelId", true, false);
			
			$this->blockTemplateId = new ModelIntegerField("blockTemplateId", "blockTemplateId", true, false);
			
			$this->deleted = new stdClass();
			$this->deleted->value = 0;
        }
		
		public static function initialize() {
			self::$title = 'Block Model';
			self::$iconPath = 'default-icon.png';
			self::$displayFields = Array('blockTitle');
		}
		
    }

?>
