<?php

    class BlockFieldTemplateModel extends CRUDModel {
        public $fieldTitle;
        public $blockTemplateId;
		public $fieldTemplateType;

        public function  __construct() {
            $this->fieldTitle = new ModelTextField("fieldTitle", "fieldTitle", true, false);
			$this->blockTemplateId = new ModelSelectField("blockTemplateId", "blockTemplateId", Array(), true, false);
			$this->blockTemplateId->data[0] = 'Select Value';
			$blockTemplates = BlockTemplateModel::all();
			foreach ($blockTemplates as $tpl) {
				$this->blockTemplateId->data[$tpl->id->value] = $tpl->recordTitle->value;
			}
			$this->fieldTemplateType = new ModelSelectField("fieldTemplateType", "fieldTemplateType", ContentSettings::$blockFieldTypes, true, false);
        }
		
		public static function initialize() {
			self::$title = 'Block Field Template Model';
			self::$iconPath = 'default-icon.png';
			self::$displayFields = Array('fieldTitle', 'blockTemplateId', 'fieldTemplateType');
		}
		
    }
?>
