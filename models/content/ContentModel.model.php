<?php

    class ContentModel extends CRUDModel {
        public $classId;
        public $classRecordId;
		public $deleted;
		
		private static $getAllBlocksCalled = false;
		
        public function  __construct() {
            $this->classId = new ModelIntegerField("classId", "Class id", true, false);
			$this->classRecordId = new ModelIntegerField("classRecordId", "Class Record id", true, false);
			$this->deleted = new stdClass();
			$this->deleted->value = 0;
        }
		
		public static function initialize() {
			self::$title = 'Content Model';
			self::$iconPath = 'default-icon.png';
			self::$multiLang = false;
			self::$displayFields = Array('classId');
		}
		
		public function __get($name) {
			return $name;
		}
		
		
		public static function getAllBlocks() {
			if (!self::$getAllBlocksCalled) {
				$out = Array();
				$allBlocks = BlockTemplateModel::all();
				$c = count($allBlocks);
				for ($i = 0; $i < $c; $i++) {
					$out[$allBlocks[$i]->id->value] = $allBlocks[$i]->recordTitle->value;
				}
				self::$getAllBlocksCalled = true;
				return $out;
			}
			return false;
		}
		
		public static function getExistsBlocks($classId, $recordId, $lang = false) {
			$contentTable = ContentModel::getTableName();
			$blockTable = BlockModel::getTableName();
			
			$existsBlocks = BlockModel::find("
				INNER JOIN `".$contentTable."` CT ON CT.`id` = ".$blockTable.".`contentModelId`
				WHERE `".$blockTable."`.`deleted` = '0' AND CT.`classId` = '{#1}' AND CT.`classRecordId` = '{#2}'
			", Array($classId, $recordId));
			
			$blocks = "";
			foreach ($existsBlocks as $b) {
				$blockFields = BlockFieldModel::find(" WHERE `blockId` = '{#1}'", Array($b->id->value));
				$blocks .= self::getBlock($b->blockTemplateId->value, $lang, $blockFields, true, $b->id->value);
			}
			return $blocks;
		}
		
		public static function removeBlock($blockId) {
			BlockModel::delete(" WHERE `id` = '{#1}'", Array($blockId));
			BlockFieldModel::delete(" WHERE `blockId` = '{#1}'", Array($blockId));
		}
		
		public static function getBlock($blockTplId, $lang, $fieldValues = Array(), $return = false, $id = false) {
			$randId = rand(999,9999);
			$blockFields = "";
			
			if (!count($fieldValues)) {
				$blockFieldsTemplates = BlockFieldTemplateModel::find(" WHERE `blockTemplateId` = '{#1}'", Array($blockTplId));
				// get block fields html
				foreach ($blockFieldsTemplates as $f) {
					$blockFields .= self::getFieldHtml($f, $randId, $lang);
				}
			} else {
				foreach ($fieldValues as $f) {
					$blockFields .= self::getFieldHtml($f, $randId, $lang, $f->value->value, $f->id->value);
				}
			}
			$blockTemplate = self::getBlockTemplate($randId, $blockTplId, $blockFields, $lang, $id);
			if (!$return ) die($blockTemplate);
			else return $blockTemplate;
		}
		
		private static function getBlockTemplate($randId, $id, $blockFields, $lang, $blockId) {
			return Controller::renderTemplate("model" . ds . "content-block-template.tpl" , Array('tplId' => $id, 'id' => $blockId, 'lang' => $lang, 'randId' => $randId, 'blockFields' => $blockFields), true);
		}
		
		public static function saveContent($o, $classId) {
			if (isset($o->lang_id)) if (!self::haveBlockForLang($o->lang_id->value)) return; 
			{
				$content = ContentModel::find(" WHERE `classId` = '{#1}' AND `classRecordId` = '{#2}'", Array($classId, $o->id->value));
				
				if (!count($content)) {
					$content = new ContentModel();
				} else $content = $content[0];
				$content->classId->value = $classId;
				$content->classRecordId->value = $o->id->value;
				$content->save();

				$randId = $_POST['randId'];
				$c = count($randId);
				for ($i = 0; $i < $c; $i++) {
					if (isset($o->lang_id) && ($_POST['blockLang'][$randId[$i]][0] != $o->lang_id->value)) continue;
					{
						$blockTplId = $_POST['block'][$randId[$i]][0];
						$blockId = $_POST['blockId'][$randId[$i]][0];
						$block = BlockModel::find(" WHERE `id` = '{#1}'", Array($blockId));
						
						if (count($block)) {
							$block = $block[0];
						} else {
							$block = new BlockModel();
						}
						
						$block->blockTemplateId->value = $blockTplId;
						$block->contentModelId->value = $content->id->value;
						$block->save();
						
						$fCount = count($_POST['fieldValue'][$randId[$i]]);
						for ($k = 0; $k < $fCount; $k++) {
							if (!empty($_POST['fieldId'][$randId[$i]][$k])) {
								$fieldId = intval($_POST['fieldId'][$randId[$i]][$k]);
								$field = BlockFieldModel::find(" WHERE `id` = '{#1}'", Array($fieldId));
								if (count($field == 1)) {
									$field[0]->value->value = $_POST['fieldValue'][$randId[$i]][$k];
									$field[0]->save();
								} else echo 'error';
							} else {
								$field = new BlockFieldModel();
								$field->value->value = $_POST['fieldValue'][$randId[$i]][$k];
								$field->blockId->value = $block->id->value;
								$field->type->value = $_POST['fieldType'][$randId[$i]][$k];
								$field->save();
							}
						}
					}
				}
				$block = new BlockModel();
			}
		}
		
		private static function haveBlockForLang($lang) {
			if (isset($_POST['randId']) && count($_POST['randId'])) {
				foreach ($_POST['randId'] as $rId) {
					if (isset($_POST['blockLang'][$rId]) && in_array($lang, $_POST['blockLang'][$rId])) {
						return true;
					}
				}
			}
			return false;
		}
		
		private static function getFieldHtml($f, $randId, $lang, $value = false, $id = false) {
			switch ($value ? $f->type->value : $f->fieldTemplateType->value) {
				case 1:
					$field = new ModelFMImageField("fieldValue", $f->fieldTitle->value, true, false);
					$startDir = Application::$settings['public_folder'];
					$value = str_replace($startDir, "", $value);
					$field->value = $value;
					$field->name .= '[' . $randId . '][]';
					$data = $field->getHTML($lang);
					$data['data']['randId'] = $randId;
					$data['data']['id'] = $id;
					$data['data']['type'] = $value ? $f->type->value : $f->fieldTemplateType->value;
					$data['data']['forContent'] = 1;
					return Controller::renderTemplate("model" . ds . "fields" . ds . $data['file'] , $data['data'], true);
					break;
				case 2:
					$field = new ModelTextArea("fieldValue", $f->fieldTitle->value, true, false);
					$field->value = $value;
					$field->name .= '[' . $randId . '][]';
					$data = $field->getHTML($lang);
					$data['data']['randId'] = $randId;
					$data['data']['id'] = $id;
					$data['data']['type'] = $value ? $f->type->value : $f->fieldTemplateType->value;
					$data['data']['forContent'] = 1;
					return Controller::renderTemplate("model" . ds . "fields" . ds . $data['file'] , $data['data'], true);
					break;
			}
		}
		
		/*
			$contentTable = ContentModel::getTableName();
			$blockTable = BlockModel::getTableName();
			$blockFieldTable = BlockFieldModel::getTableName();
			return BlockFieldModel::find("
				INNER JOIN `". $blockTable ."` ON `".$blockTable."`.`id` = `".$blockFieldTable."`.`blockId` 
				INNER JOIN `".$contentTable."` ON `".$contentTable."`.`id` = `".$blockTable."`.`contentModelId`
				WHERE `".$contentTable."`.`classId` = '{#1}' AND `".$contentTable."`.`classRecordId` = '{#2}'
			", Array($classId, $recordId));
		*/
		
    }

?>
