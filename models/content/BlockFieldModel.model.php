<?php

    class BlockFieldModel extends CRUDModel {
		public $blockId;
        public $value;
        public $type;
		public $deleted;

        public function  __construct() {
			$this->fieldTitle = new ModelTextField("fieldTitle", "fieldTitle", true, false);
            $this->blockId = new ModelIntegerField("blockId", "blockId", true, false);
			$this->type = new ModelIntegerField("type", "type", true, false);
			$this->type->dbLength = '2';
			$this->value = new ModelTinyMce("value", "value", true, false);
			$this->deleted = new stdClass();
			$this->deleted->value = 0;
        }
		
		public static function initialize() {
			self::$title = 'Block field model';
			self::$iconPath = 'default-icon.png';
			self::$displayFields = Array('fieldTitle');
		}
		
    }

?>
